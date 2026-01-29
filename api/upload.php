<?php
/**
 * File Upload API Endpoint
 * Handles profile image uploads
 */

header('Content-Type: application/json; charset=utf-8');
session_start();
require_once '../config/db.php';

// Check authentication
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized'], JSON_UNESCAPED_UNICODE);
    exit;
}

$userId = $_SESSION['user_id'];
$action = $_GET['action'] ?? 'avatar';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($action === 'avatar') {
    // Handle avatar upload
    if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'הקובץ גדול מדי',
            UPLOAD_ERR_FORM_SIZE => 'הקובץ גדול מדי',
            UPLOAD_ERR_PARTIAL => 'הקובץ הועלה חלקית בלבד',
            UPLOAD_ERR_NO_FILE => 'לא נבחר קובץ',
            UPLOAD_ERR_NO_TMP_DIR => 'שגיאת שרת',
            UPLOAD_ERR_CANT_WRITE => 'שגיאת שרת',
            UPLOAD_ERR_EXTENSION => 'סוג קובץ לא נתמך'
        ];
        $errorCode = $_FILES['avatar']['error'] ?? UPLOAD_ERR_NO_FILE;
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => $errorMessages[$errorCode] ?? 'שגיאה בהעלאת הקובץ'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $file = $_FILES['avatar'];

    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    if (!in_array($mimeType, $allowedTypes)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'סוג קובץ לא נתמך. אנא העלו תמונה (JPEG, PNG, GIF, WebP)'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Validate file size (max 5MB)
    $maxSize = 5 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'הקובץ גדול מדי. גודל מקסימלי: 5MB'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Generate unique filename
    $extensions = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp'
    ];
    $extension = $extensions[$mimeType] ?? 'jpg';
    $filename = 'avatar_' . $userId . '_' . time() . '.' . $extension;

    // Use realpath for proper path handling on Windows/Linux
    $baseDir = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..') ?: dirname(__DIR__);
    $uploadDir = $baseDir . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'avatars' . DIRECTORY_SEPARATOR;
    $uploadPath = $uploadDir . $filename;

    // Create directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'שגיאה ביצירת תיקיית העלאות'], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    // Check if directory is writable
    if (!is_writable($uploadDir)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'אין הרשאות כתיבה לתיקייה'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'שגיאה בשמירת הקובץ. בדקו הרשאות.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Update user's photo in database
    $photoUrl = '/uploads/avatars/' . $filename;

    try {
        $stmt = $pdo->prepare("UPDATE users SET photo = ? WHERE id = ?");
        $stmt->execute([$photoUrl, $userId]);

        // Delete old avatar if exists
        $stmt = $pdo->prepare("SELECT photo FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $oldPhoto = $stmt->fetchColumn();

        if ($oldPhoto && strpos($oldPhoto, '/uploads/avatars/') === 0) {
            $oldFilename = basename($oldPhoto);
            $oldPath = $uploadDir . $oldFilename;
            if (file_exists($oldPath) && $oldPath !== $uploadPath) {
                @unlink($oldPath);
            }
        }

        echo json_encode([
            'success' => true,
            'url' => $photoUrl,
            'message' => 'התמונה הועלתה בהצלחה!'
        ], JSON_UNESCAPED_UNICODE);

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'שגיאה בעדכון הפרופיל'], JSON_UNESCAPED_UNICODE);
    }
} elseif ($action === 'job') {
    // Handle job image upload
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'הקובץ גדול מדי',
            UPLOAD_ERR_FORM_SIZE => 'הקובץ גדול מדי',
            UPLOAD_ERR_PARTIAL => 'הקובץ הועלה חלקית בלבד',
            UPLOAD_ERR_NO_FILE => 'לא נבחר קובץ',
            UPLOAD_ERR_NO_TMP_DIR => 'שגיאת שרת',
            UPLOAD_ERR_CANT_WRITE => 'שגיאת שרת',
            UPLOAD_ERR_EXTENSION => 'סוג קובץ לא נתמך'
        ];
        $errorCode = $_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE;
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => $errorMessages[$errorCode] ?? 'שגיאה בהעלאת הקובץ'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $file = $_FILES['image'];

    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    if (!in_array($mimeType, $allowedTypes)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'סוג קובץ לא נתמך. אנא העלו תמונה (JPEG, PNG, GIF, WebP)'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Validate file size (max 5MB)
    $maxSize = 5 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'הקובץ גדול מדי. גודל מקסימלי: 5MB'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Generate unique filename
    $extensions = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp'
    ];
    $extension = $extensions[$mimeType] ?? 'jpg';
    $filename = 'job_' . $userId . '_' . time() . '.' . $extension;

    // Use realpath for proper path handling on Windows/Linux
    $baseDir = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..') ?: dirname(__DIR__);
    $uploadDir = $baseDir . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'jobs' . DIRECTORY_SEPARATOR;
    $uploadPath = $uploadDir . $filename;

    // Create directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0775, true)) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'שגיאה ביצירת תיקיית העלאות'], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    // Check if directory is writable
    if (!is_writable($uploadDir)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'אין הרשאות כתיבה לתיקייה'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'שגיאה בשמירת הקובץ. בדקו הרשאות.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $imageUrl = '/uploads/jobs/' . $filename;

    echo json_encode([
        'success' => true,
        'url' => $imageUrl,
        'message' => 'התמונה הועלתה בהצלחה!'
    ], JSON_UNESCAPED_UNICODE);

} elseif ($action === 'cover') {
    // Handle cover image upload
    if (!isset($_FILES['cover']) || $_FILES['cover']['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'הקובץ גדול מדי',
            UPLOAD_ERR_FORM_SIZE => 'הקובץ גדול מדי',
            UPLOAD_ERR_PARTIAL => 'הקובץ הועלה חלקית בלבד',
            UPLOAD_ERR_NO_FILE => 'לא נבחר קובץ',
            UPLOAD_ERR_NO_TMP_DIR => 'שגיאת שרת',
            UPLOAD_ERR_CANT_WRITE => 'שגיאת שרת',
            UPLOAD_ERR_EXTENSION => 'סוג קובץ לא נתמך'
        ];
        $errorCode = $_FILES['cover']['error'] ?? UPLOAD_ERR_NO_FILE;
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => $errorMessages[$errorCode] ?? 'שגיאה בהעלאת הקובץ'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $file = $_FILES['cover'];

    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    if (!in_array($mimeType, $allowedTypes)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'סוג קובץ לא נתמך. אנא העלו תמונה (JPEG, PNG, GIF, WebP)'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Validate file size (max 5MB)
    $maxSize = 5 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'הקובץ גדול מדי. גודל מקסימלי: 5MB'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Generate unique filename
    $extensions = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp'
    ];
    $extension = $extensions[$mimeType] ?? 'jpg';
    $filename = 'cover_' . $userId . '_' . time() . '.' . $extension;

    // Use realpath for proper path handling on Windows/Linux
    $baseDir = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..') ?: dirname(__DIR__);
    $uploadDir = $baseDir . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'covers' . DIRECTORY_SEPARATOR;
    $uploadPath = $uploadDir . $filename;

    // Create directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0775, true)) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'שגיאה ביצירת תיקיית העלאות'], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    // Check if directory is writable
    if (!is_writable($uploadDir)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'אין הרשאות כתיבה לתיקייה'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'שגיאה בשמירת הקובץ. בדקו הרשאות.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $imageUrl = '/uploads/covers/' . $filename;

    // Update database immediately for better UX
    try {
        $stmt = $pdo->prepare("UPDATE users SET company_cover = ? WHERE id = ?");
        $stmt->execute([$imageUrl, $userId]);
    } catch (PDOException $e) {
        // Continue even if DB update fails (will be saved on save changes)
    }

    echo json_encode([
        'success' => true,
        'url' => $imageUrl,
        'message' => 'התמונה הועלתה בהצלחה!'
    ], JSON_UNESCAPED_UNICODE);

} elseif ($action === 'cv') {
    // Handle CV upload
    if (!isset($_FILES['cv']) || $_FILES['cv']['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'הקובץ גדול מדי',
            UPLOAD_ERR_FORM_SIZE => 'הקובץ גדול מדי',
            UPLOAD_ERR_PARTIAL => 'הקובץ הועלה חלקית בלבד',
            UPLOAD_ERR_NO_FILE => 'לא נבחר קובץ',
            UPLOAD_ERR_NO_TMP_DIR => 'שגיאת שרת',
            UPLOAD_ERR_CANT_WRITE => 'שגיאת שרת',
            UPLOAD_ERR_EXTENSION => 'סוג קובץ לא נתמך'
        ];
        $errorCode = $_FILES['cv']['error'] ?? UPLOAD_ERR_NO_FILE;
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => $errorMessages[$errorCode] ?? 'שגיאה בהעלאת הקובץ'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $file = $_FILES['cv'];

    // Validate file type
    $allowedTypes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    if (!in_array($mimeType, $allowedTypes)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'קובץ לא נתמך. אנא העלו PDF או Word'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Validate file size (max 5MB)
    $maxSize = 5 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'הקובץ גדול מדי. גודל מקסימלי: 5MB'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Generate unique filename
    $extension = 'pdf'; // Default
    if ($mimeType === 'application/msword')
        $extension = 'doc';
    if ($mimeType === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')
        $extension = 'docx';

    $filename = 'cv_' . $userId . '_' . time() . '.' . $extension;

    // Use realpath for proper path handling on Windows/Linux
    $baseDir = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..') ?: dirname(__DIR__);
    $uploadDir = $baseDir . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'cvs' . DIRECTORY_SEPARATOR;
    $uploadPath = $uploadDir . $filename;

    // Create directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0775, true)) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'שגיאה ביצירת תיקיית העלאות'], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    // Check if directory is writable
    if (!is_writable($uploadDir)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'אין הרשאות כתיבה לתיקייה'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'שגיאה בשמירת הקובץ. בדקו הרשאות.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $fileUrl = '/uploads/cvs/' . $filename;

    // Update database immediately
    try {
        $stmt = $pdo->prepare("UPDATE users SET resume_file = ? WHERE id = ?");
        $stmt->execute([$fileUrl, $userId]);
    } catch (PDOException $e) {
        // Continue even if DB update fails
    }

    echo json_encode([
        'success' => true,
        'url' => $fileUrl,
        'message' => 'הקובץ הועלה בהצלחה!'
    ], JSON_UNESCAPED_UNICODE);

} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid action'], JSON_UNESCAPED_UNICODE);
}
