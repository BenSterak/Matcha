<?php
/**
 * Messages API Endpoint
 * Handles chat messages between matched users and employers
 */

header('Content-Type: application/json; charset=utf-8');
session_start();
require_once '../config/db.php';
require_once '../includes/email.php';

// Check authentication
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized'], JSON_UNESCAPED_UNICODE);
    exit;
}

$userId = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'send':
        // Send a new message
        $input = json_decode(file_get_contents('php://input'), true);
        $matchId = intval($input['matchId'] ?? 0);
        $content = trim($input['content'] ?? '');

        if (!$matchId || empty($content)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Match ID and content required'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        try {
            // Verify user has access to this match
            $stmt = $pdo->prepare("
                SELECT m.id, m.userId, j.business_id
                FROM matches m
                JOIN jobs j ON m.jobId = j.id
                WHERE m.id = ? AND m.status = 'matched'
                AND (m.userId = ? OR j.business_id = ?)
            ");
            $stmt->execute([$matchId, $userId, $userId]);
            $match = $stmt->fetch();

            if (!$match) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'Access denied'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // Insert message
            $stmt = $pdo->prepare("
                INSERT INTO messages (matchId, senderId, content, createdAt)
                VALUES (?, ?, ?, NOW())
            ");
            $stmt->execute([$matchId, $userId, $content]);
            $messageId = $pdo->lastInsertId();

            // Send email notification to recipient (if offline)
            notifyNewMessage($pdo, $matchId, $userId, $content);

            echo json_encode([
                'success' => true,
                'messageId' => $messageId,
                'message' => [
                    'id' => $messageId,
                    'content' => $content,
                    'senderId' => $userId,
                    'isMine' => true,
                    'createdAt' => date('Y-m-d H:i:s')
                ]
            ], JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Database error'], JSON_UNESCAPED_UNICODE);
        }
        break;

    case 'get':
        // Get messages for a match
        $matchId = intval($_GET['matchId'] ?? 0);
        $after = intval($_GET['after'] ?? 0); // For polling new messages

        if (!$matchId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Match ID required'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        try {
            // Verify user has access to this match
            $stmt = $pdo->prepare("
                SELECT m.id, m.userId, j.business_id
                FROM matches m
                JOIN jobs j ON m.jobId = j.id
                WHERE m.id = ? AND m.status = 'matched'
                AND (m.userId = ? OR j.business_id = ?)
            ");
            $stmt->execute([$matchId, $userId, $userId]);
            $match = $stmt->fetch();

            if (!$match) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'Access denied'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // Get messages
            $sql = "SELECT id, senderId, content, createdAt FROM messages WHERE matchId = ?";
            $params = [$matchId];

            if ($after > 0) {
                $sql .= " AND id > ?";
                $params[] = $after;
            }

            $sql .= " ORDER BY createdAt ASC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $messages = $stmt->fetchAll();

            // Mark messages as read
            if (!empty($messages)) {
                $stmt = $pdo->prepare("
                    UPDATE messages
                    SET readAt = NOW()
                    WHERE matchId = ? AND senderId != ? AND readAt IS NULL
                ");
                $stmt->execute([$matchId, $userId]);
            }

            // Format messages
            $formattedMessages = array_map(function($msg) use ($userId) {
                return [
                    'id' => $msg['id'],
                    'content' => $msg['content'],
                    'senderId' => $msg['senderId'],
                    'isMine' => $msg['senderId'] == $userId,
                    'createdAt' => $msg['createdAt']
                ];
            }, $messages);

            echo json_encode([
                'success' => true,
                'messages' => $formattedMessages
            ], JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Database error'], JSON_UNESCAPED_UNICODE);
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid action'], JSON_UNESCAPED_UNICODE);
}
