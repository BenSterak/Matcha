<?php
/**
 * Matches API Endpoint
 * Handles match-related operations
 */

header('Content-Type: application/json; charset=utf-8');
session_start();
require_once '../config/db.php';

$action = $_GET['action'] ?? '';

// Helper function to send JSON response
function jsonResponse($data, $statusCode = 200)
{
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// Check authentication
function requireAuth()
{
    if (!isset($_SESSION['user_id'])) {
        jsonResponse(['success' => false, 'error' => 'Unauthorized'], 401);
    }
    return $_SESSION['user_id'];
}

switch ($action) {
    case 'swipe':
        // Record user swipe (like = create pending match)
        $userId = requireAuth();

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }

        $jobId = $input['jobId'] ?? null;
        $swipeAction = $input['action'] ?? 'like'; // 'like' or 'pass'

        if (!$jobId) {
            jsonResponse(['success' => false, 'error' => 'Job ID required'], 400);
        }

        try {
            // Check if match already exists
            $stmt = $pdo->prepare("SELECT id FROM matches WHERE userId = ? AND jobId = ?");
            $stmt->execute([$userId, $jobId]);

            if ($stmt->fetch()) {
                jsonResponse(['success' => true, 'message' => 'Already swiped']);
            }

            // Create match record (pending status for likes)
            $status = $swipeAction === 'like' ? 'pending' : 'passed';

            $stmt = $pdo->prepare("
                INSERT INTO matches (userId, jobId, status, createdAt)
                VALUES (?, ?, ?, NOW())
            ");
            $stmt->execute([$userId, $jobId, $status]);

            jsonResponse(['success' => true, 'matchId' => $pdo->lastInsertId()]);
        } catch (PDOException $e) {
            jsonResponse(['success' => false, 'error' => 'Database error'], 500);
        }
        break;

    case 'user':
        // Get user's matches
        $userId = requireAuth();
        $status = $_GET['status'] ?? null;

        // Check user role
        $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $userRole = $stmt->fetchColumn();

        try {
            if ($userRole === 'employer') {
                // Employer: Get matched CANDIDATES
                $sql = "
                    SELECT m.*, u.name as title, '' as company, 'candidate' as type, u.photo as image
                    FROM matches m
                    JOIN users u ON m.userId = u.id
                    JOIN jobs j ON m.jobId = j.id
                    WHERE j.business_id = ?
                ";
                $params = [$userId];
            } else {
                // Job Seeker: Get matched JOBS
                $sql = "
                    SELECT m.*, j.title, j.company, j.location, j.image, j.salaryRange
                    FROM matches m
                    JOIN jobs j ON m.jobId = j.id
                    WHERE m.userId = ?
                ";
                $params = [$userId];
            }

            if ($status) {
                $sql .= " AND m.status = ?";
                $params[] = $status;
            } else {
                $sql .= " AND m.status != 'passed'";
            }

            $sql .= " ORDER BY m.createdAt DESC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $matches = $stmt->fetchAll();

            jsonResponse(['success' => true, 'matches' => $matches]);
        } catch (PDOException $e) {
            jsonResponse(['success' => false, 'error' => 'Database error'], 500);
        }
        break;

    case 'job':
        // Get candidates for a job (for employers)
        $userId = requireAuth();
        $jobId = $_GET['job_id'] ?? null;

        if (!$jobId) {
            jsonResponse(['success' => false, 'error' => 'Job ID required'], 400);
        }

        try {
            // Verify user owns the job
            $stmt = $pdo->prepare("SELECT id FROM jobs WHERE id = ? AND business_id = ?");
            $stmt->execute([$jobId, $userId]);

            if (!$stmt->fetch()) {
                jsonResponse(['success' => false, 'error' => 'Job not found or unauthorized'], 404);
            }

            // Get candidates
            $stmt = $pdo->prepare("
                SELECT m.*, u.name, u.email, u.bio, u.photo, u.field, u.salary as expectedSalary, u.workModel
                FROM matches m
                JOIN users u ON m.userId = u.id
                WHERE m.jobId = ? AND m.status IN ('pending', 'matched')
                ORDER BY m.createdAt DESC
            ");
            $stmt->execute([$jobId]);
            $candidates = $stmt->fetchAll();

            jsonResponse(['success' => true, 'candidates' => $candidates]);
        } catch (PDOException $e) {
            jsonResponse(['success' => false, 'error' => 'Database error'], 500);
        }
        break;

    case 'approve':
        // Business approves match (pending -> matched)
        $userId = requireAuth();
        $matchId = $_GET['id'] ?? null;

        if (!$matchId) {
            jsonResponse(['success' => false, 'error' => 'Match ID required'], 400);
        }

        try {
            // Verify user owns the job
            $stmt = $pdo->prepare("
                SELECT m.id FROM matches m
                JOIN jobs j ON m.jobId = j.id
                WHERE m.id = ? AND j.business_id = ?
            ");
            $stmt->execute([$matchId, $userId]);

            if (!$stmt->fetch()) {
                jsonResponse(['success' => false, 'error' => 'Match not found or unauthorized'], 404);
            }

            // Update status
            $stmt = $pdo->prepare("UPDATE matches SET status = 'matched' WHERE id = ?");
            $stmt->execute([$matchId]);

            jsonResponse(['success' => true]);
        } catch (PDOException $e) {
            jsonResponse(['success' => false, 'error' => 'Database error'], 500);
        }
        break;

    case 'reject':
        // Business rejects match
        $userId = requireAuth();
        $matchId = $_GET['id'] ?? null;

        if (!$matchId) {
            jsonResponse(['success' => false, 'error' => 'Match ID required'], 400);
        }

        try {
            // Verify user owns the job
            $stmt = $pdo->prepare("
                SELECT m.id FROM matches m
                JOIN jobs j ON m.jobId = j.id
                WHERE m.id = ? AND j.business_id = ?
            ");
            $stmt->execute([$matchId, $userId]);

            if (!$stmt->fetch()) {
                jsonResponse(['success' => false, 'error' => 'Match not found or unauthorized'], 404);
            }

            // Update status
            $stmt = $pdo->prepare("UPDATE matches SET status = 'rejected' WHERE id = ?");
            $stmt->execute([$matchId]);

            jsonResponse(['success' => true]);
        } catch (PDOException $e) {
            jsonResponse(['success' => false, 'error' => 'Database error'], 500);
        }
        break;

    case 'pending_count':
        // Get count of pending matches for notification
        $userId = requireAuth();

        try {
            $stmt = $pdo->prepare("
                SELECT COUNT(*) as count FROM matches m
                WHERE m.userId = ? AND m.status = 'matched'
            ");
            $stmt->execute([$userId]);
            $result = $stmt->fetch();

            jsonResponse(['success' => true, 'count' => $result['count']]);
        } catch (PDOException $e) {
            jsonResponse(['success' => false, 'error' => 'Database error'], 500);
        }
        break;

    default:
        jsonResponse(['success' => false, 'error' => 'Invalid action'], 400);
}
