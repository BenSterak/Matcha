<?php
require_once __DIR__ . '/../config/db.php';

echo "Starting schema update...\n";

// 1. Add new columns
$columns = [
    'company_website' => "VARCHAR(255) NULL DEFAULT NULL AFTER company_name",
    'company_location' => "VARCHAR(255) NULL DEFAULT NULL AFTER company_website",
    'company_size' => "VARCHAR(50) NULL DEFAULT NULL AFTER company_location",
    'company_cover' => "VARCHAR(255) NULL DEFAULT NULL AFTER company_size",
    'resume_file' => "VARCHAR(255) NULL DEFAULT NULL AFTER salary",
    'portfolio_url' => "VARCHAR(255) NULL DEFAULT NULL AFTER resume_file",
    'linkedin_url' => "VARCHAR(255) NULL DEFAULT NULL AFTER portfolio_url"
];

foreach ($columns as $col => $def) {
    try {
        // Check if column exists
        $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE '$col'");
        if ($stmt->fetch()) {
            echo "Column '$col' already exists. Skipping.\n";
        } else {
            $pdo->exec("ALTER TABLE users ADD COLUMN $col $def");
            echo "Added column '$col'.\n";
        }
    } catch (PDOException $e) {
        echo "Error adding column '$col': " . $e->getMessage() . "\n";
    }
}

// 2. Create upload directories
$baseDir = dirname(__DIR__);
$dirs = [
    '/uploads',
    '/uploads/avatars',
    '/uploads/jobs',
    '/uploads/covers',
    '/uploads/cvs'
];

echo "\nChecking directories...\n";
foreach ($dirs as $dir) {
    $path = $baseDir . $dir;
    if (!is_dir($path)) {
        if (mkdir($path, 0777, true)) {
            echo "Created directory: $path\n";
        } else {
            echo "FAILED to create directory: $path\n";
        }
    } else {
        echo "Directory exists: $path\n";
    }

    // Try to make writable just in case
    @chmod($path, 0777);
}

echo "\nMigration completed!\n";
?>