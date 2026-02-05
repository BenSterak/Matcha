<?php
// Load database configuration
require_once __DIR__ . '/../config/db.php';

try {
    echo "Connecting to database...\n";
    // $pdo is created in db.php

    echo "Querying 'users' table schema...\n";
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Columns in 'users' table:\n";
    $hasCreatedAt = false;
    $hasLastSeen = false;

    foreach ($columns as $col) {
        echo "- " . $col['Field'] . " (" . $col['Type'] . ")\n";
        if ($col['Field'] === 'createdAt')
            $hasCreatedAt = true;
        if ($col['Field'] === 'last_seen')
            $hasLastSeen = true;
    }

    echo "\nAnalysis:\n";
    if ($hasCreatedAt) {
        echo "[OK] 'createdAt' column exists.\n";
    } else {
        echo "[FAIL] 'createdAt' column fails to exist. 'AFTER createdAt' in ALTER TABLE will fail.\n";
    }

    if ($hasLastSeen) {
        echo "[INFO] 'last_seen' column already exists.\n";
    } else {
        echo "[INFO] 'last_seen' column deos not exist yet.\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>