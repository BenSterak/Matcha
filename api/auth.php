<?php
/**
 * Authentication API Endpoint
 * Handles login, register, and session management
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

switch ($action) {
    case 'login':
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }

        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';

        if (empty($email) || empty($password)) {
            jsonResponse(['success' => false, 'error' => 'אימייל וסיסמה נדרשים'], 400);
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_name'] = $user['name'];

                jsonResponse([
                    'success' => true,
                    'user' => [
                        'id' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'role' => $user['role']
                    ]
                ]);
            } else {
                jsonResponse(['success' => false, 'error' => 'אימייל או סיסמה שגויים'], 401);
            }
        } catch (PDOException $e) {
            jsonResponse(['success' => false, 'error' => 'שגיאת מסד נתונים'], 500);
        }
        break;

    case 'register':
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }

        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';
        $name = trim($input['name'] ?? '');
        $role = $input['role'] ?? 'jobseeker';

        if (empty($email) || empty($password) || empty($name)) {
            jsonResponse(['success' => false, 'error' => 'כל השדות נדרשים'], 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            jsonResponse(['success' => false, 'error' => 'כתובת אימייל לא תקינה'], 400);
        }

        if (strlen($password) < 6) {
            jsonResponse(['success' => false, 'error' => 'הסיסמה חייבת להכיל לפחות 6 תווים'], 400);
        }

        try {
            // Check if email exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                jsonResponse(['success' => false, 'error' => 'כתובת האימייל כבר קיימת'], 409);
            }

            // Create user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("
                INSERT INTO users (email, password, name, role, createdAt)
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$email, $hashedPassword, $name, $role]);

            $userId = $pdo->lastInsertId();

            // Auto login
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_role'] = $role;
            $_SESSION['user_name'] = $name;

            jsonResponse([
                'success' => true,
                'user' => [
                    'id' => $userId,
                    'name' => $name,
                    'email' => $email,
                    'role' => $role
                ]
            ]);
        } catch (PDOException $e) {
            jsonResponse(['success' => false, 'error' => 'שגיאת מסד נתונים'], 500);
        }
        break;

    case 'logout':
        session_destroy();
        jsonResponse(['success' => true]);
        break;

    case 'me':
        if (!isset($_SESSION['user_id'])) {
            jsonResponse(['success' => false, 'error' => 'לא מחובר'], 401);
        }

        try {
            $stmt = $pdo->prepare("SELECT id, name, email, role, bio, photo, field, salary, workModel FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();

            if (!$user) {
                session_destroy();
                jsonResponse(['success' => false, 'error' => 'משתמש לא נמצא'], 404);
            }

            jsonResponse(['success' => true, 'user' => $user]);
        } catch (PDOException $e) {
            jsonResponse(['success' => false, 'error' => 'שגיאת מסד נתונים'], 500);
        }
        break;

    case 'update':
        if (!isset($_SESSION['user_id'])) {
            jsonResponse(['success' => false, 'error' => 'לא מחובר'], 401);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }

        try {
            $fields = [];
            $values = [];

            $allowedFields = ['name', 'bio', 'field', 'salary', 'workModel', 'photo'];
            foreach ($allowedFields as $field) {
                if (isset($input[$field])) {
                    $fields[] = "$field = ?";
                    $values[] = $input[$field];
                }
            }

            if (empty($fields)) {
                jsonResponse(['success' => true, 'message' => 'אין שינויים']);
            }

            $values[] = $_SESSION['user_id'];

            $stmt = $pdo->prepare("UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?");
            $stmt->execute($values);

            // Update session name if changed
            if (isset($input['name'])) {
                $_SESSION['user_name'] = $input['name'];
            }

            jsonResponse(['success' => true]);
        } catch (PDOException $e) {
            jsonResponse(['success' => false, 'error' => 'שגיאת מסד נתונים'], 500);
        }
        break;

    default:
        jsonResponse(['success' => false, 'error' => 'פעולה לא חוקית'], 400);
}
