<?php
session_start();
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

function jsonResponse($data, $code = 200)
{
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// Check authentication
if (!isset($_SESSION['user_id'])) {
    jsonResponse(['success' => false, 'error' => 'לא מחובר'], 401);
}

// Check admin permission
$stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$adminCheck = $stmt->fetch();

if (!$adminCheck || !$adminCheck['is_admin']) {
    jsonResponse(['success' => false, 'error' => 'אין הרשאה'], 403);
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'stats':
        // Total users
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users");
        $stmt->execute();
        $totalUsers = $stmt->fetch()['count'];

        // New users today
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE DATE(createdAt) = CURDATE()");
        $stmt->execute();
        $newToday = $stmt->fetch()['count'];

        // Total jobs
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM jobs");
        $stmt->execute();
        $totalJobs = $stmt->fetch()['count'];

        // Total matches
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM matches WHERE status = 'matched'");
        $stmt->execute();
        $totalMatches = $stmt->fetch()['count'];

        // Total messages
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM messages");
        $stmt->execute();
        $totalMessages = $stmt->fetch()['count'];

        // Online users (active in last 5 minutes)
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE last_seen > DATE_SUB(NOW(), INTERVAL 5 MINUTE)");
        $stmt->execute();
        $onlineNow = $stmt->fetch()['count'];

        // Blocked users
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE is_blocked = 1");
        $stmt->execute();
        $blockedUsers = $stmt->fetch()['count'];

        // Users by role
        $stmt = $pdo->prepare("SELECT role, COUNT(*) as count FROM users GROUP BY role");
        $stmt->execute();
        $byRole = [];
        while ($row = $stmt->fetch()) {
            $byRole[$row['role']] = $row['count'];
        }

        jsonResponse([
            'success' => true,
            'stats' => [
                'totalUsers' => (int) $totalUsers,
                'newToday' => (int) $newToday,
                'totalJobs' => (int) $totalJobs,
                'totalMatches' => (int) $totalMatches,
                'totalMessages' => (int) $totalMessages,
                'onlineNow' => (int) $onlineNow,
                'blockedUsers' => (int) $blockedUsers,
                'jobseekers' => (int) ($byRole['jobseeker'] ?? 0),
                'employers' => (int) ($byRole['employer'] ?? 0),
            ]
        ]);
        break;

    case 'users':
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;
        $search = trim($_GET['search'] ?? '');
        $roleFilter = $_GET['role'] ?? '';

        $where = "WHERE 1=1";
        $params = [];

        if ($search !== '') {
            $where .= " AND (name LIKE ? OR email LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        if ($roleFilter === 'jobseeker' || $roleFilter === 'employer') {
            $where .= " AND role = ?";
            $params[] = $roleFilter;
        }

        if ($roleFilter === 'blocked') {
            $where .= " AND is_blocked = 1";
        }

        // Count total
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users $where");
        $stmt->execute($params);
        $total = $stmt->fetch()['count'];

        // Get users
        $stmt = $pdo->prepare("
            SELECT id, name, email, role, photo, company_name, field,
                   is_blocked, is_admin, createdAt, last_seen
            FROM users
            $where
            ORDER BY createdAt DESC
            LIMIT $limit OFFSET $offset
        ");
        $stmt->execute($params);
        $users = $stmt->fetchAll();

        jsonResponse([
            'success' => true,
            'users' => $users,
            'total' => (int) $total,
            'page' => $page,
            'pages' => ceil($total / $limit)
        ]);
        break;

    case 'toggle_block':
        $userId = (int) ($_POST['user_id'] ?? 0);
        if ($userId <= 0) {
            jsonResponse(['success' => false, 'error' => 'מזהה משתמש חסר'], 400);
        }

        // Don't allow blocking yourself
        if ($userId === (int) $_SESSION['user_id']) {
            jsonResponse(['success' => false, 'error' => 'לא ניתן לחסום את עצמך'], 400);
        }

        // Don't allow blocking other admins
        $stmt = $pdo->prepare("SELECT is_admin, is_blocked FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $targetUser = $stmt->fetch();

        if (!$targetUser) {
            jsonResponse(['success' => false, 'error' => 'משתמש לא נמצא'], 404);
        }

        if ($targetUser['is_admin']) {
            jsonResponse(['success' => false, 'error' => 'לא ניתן לחסום מנהל'], 400);
        }

        $newStatus = $targetUser['is_blocked'] ? 0 : 1;
        $stmt = $pdo->prepare("UPDATE users SET is_blocked = ? WHERE id = ?");
        $stmt->execute([$newStatus, $userId]);

        jsonResponse([
            'success' => true,
            'is_blocked' => $newStatus,
            'message' => $newStatus ? 'המשתמש נחסם' : 'החסימה הוסרה'
        ]);
        break;

    default:
        jsonResponse(['success' => false, 'error' => 'פעולה לא חוקית'], 400);
}
