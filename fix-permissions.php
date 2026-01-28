<?php
/**
 * Fix Permissions Script
 * Run this once to fix upload directory permissions
 * DELETE THIS FILE AFTER USE!
 */

$uploadDir = __DIR__ . '/uploads/avatars';

echo "<h1>Matcha - Fix Permissions</h1>";

// Create directory if doesn't exist
if (!is_dir($uploadDir)) {
    if (mkdir($uploadDir, 0755, true)) {
        echo "<p style='color: green;'>✅ Created directory: $uploadDir</p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to create directory</p>";
    }
}

// Try to change permissions
if (is_dir($uploadDir)) {
    // Try 755 first
    if (@chmod($uploadDir, 0755)) {
        echo "<p style='color: green;'>✅ Set permissions to 755</p>";
    } elseif (@chmod($uploadDir, 0775)) {
        echo "<p style='color: green;'>✅ Set permissions to 775</p>";
    } elseif (@chmod($uploadDir, 0777)) {
        echo "<p style='color: orange;'>⚠️ Set permissions to 777 (less secure, but works)</p>";
    } else {
        echo "<p style='color: red;'>❌ Could not change permissions automatically.</p>";
        echo "<p>Please change permissions manually via your hosting control panel (cPanel/Plesk):</p>";
        echo "<ol>";
        echo "<li>Go to File Manager</li>";
        echo "<li>Navigate to: <code>uploads/avatars</code></li>";
        echo "<li>Right-click → Permissions → Set to 755 or 775</li>";
        echo "</ol>";
    }

    // Test write
    $testFile = $uploadDir . '/test_' . time() . '.txt';
    if (@file_put_contents($testFile, 'test')) {
        @unlink($testFile);
        echo "<p style='color: green;'>✅ Write test successful! Uploads should work now.</p>";
    } else {
        echo "<p style='color: red;'>❌ Write test failed. Manual permission change required.</p>";
    }
}

echo "<hr>";
echo "<p><strong>⚠️ IMPORTANT: Delete this file after use!</strong></p>";
echo "<p><a href='/profile.php'>Go to Profile</a> to test upload</p>";
?>
