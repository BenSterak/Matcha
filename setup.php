<?php
/**
 * Database Setup Script for Matcha
 * Run this once to ensure proper database structure
 */

require_once 'config/db.php';

$messages = [];

function logMessage($message, $type = 'info')
{
    global $messages;
    $messages[] = ['message' => $message, 'type' => $type];
}

try {
    // Check if password column exists in users table
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'password'");
    if ($stmt->rowCount() === 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN password VARCHAR(255) NULL");
        logMessage("Added 'password' column to users table", 'success');
    } else {
        logMessage("'password' column already exists in users table");
    }

    // Check if role column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'role'");
    if ($stmt->rowCount() === 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN role ENUM('jobseeker', 'employer') DEFAULT 'jobseeker'");
        logMessage("Added 'role' column to users table", 'success');
    } else {
        logMessage("'role' column already exists in users table");
    }

    // Check if company_name column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'company_name'");
    if ($stmt->rowCount() === 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN company_name VARCHAR(255) NULL");
        logMessage("Added 'company_name' column to users table", 'success');
    } else {
        logMessage("'company_name' column already exists in users table");
    }

    // Check if business_id column exists in jobs table
    $stmt = $pdo->query("SHOW COLUMNS FROM jobs LIKE 'business_id'");
    if ($stmt->rowCount() === 0) {
        $pdo->exec("ALTER TABLE jobs ADD COLUMN business_id INT NULL");
        logMessage("Added 'business_id' column to jobs table", 'success');
    } else {
        logMessage("'business_id' column already exists in jobs table");
    }

    // Check if status column exists in matches table
    $stmt = $pdo->query("SHOW COLUMNS FROM matches LIKE 'status'");
    if ($stmt->rowCount() === 0) {
        $pdo->exec("ALTER TABLE matches ADD COLUMN status ENUM('pending', 'matched', 'rejected', 'passed') DEFAULT 'pending'");
        logMessage("Added 'status' column to matches table", 'success');
    } else {
        logMessage("'status' column already exists in matches table");
    }

    // Create messages table if it doesn't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            matchId INT NOT NULL,
            senderId INT NOT NULL,
            content TEXT NOT NULL,
            createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            readAt TIMESTAMP NULL,
            INDEX (matchId),
            INDEX (senderId)
        )
    ");
    logMessage("Messages table is ready");

    // Create a demo employer account if none exists
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'employer'");
    $result = $stmt->fetch();
    if ($result['count'] == 0) {
        $hashedPassword = password_hash('demo123', PASSWORD_DEFAULT);
        $pdo->exec("
            INSERT INTO users (email, password, name, role, company_name, bio, createdAt)
            VALUES ('demo@company.com', '$hashedPassword', 'Demo Company', 'employer', 'TechStart', 'A demo company for testing', NOW())
        ");
        logMessage("Created demo employer account (demo@company.com / demo123)", 'success');
    }

    // Create a demo job seeker account if none exists
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'jobseeker'");
    $result = $stmt->fetch();
    if ($result['count'] == 0) {
        $hashedPassword = password_hash('demo123', PASSWORD_DEFAULT);
        $pdo->exec("
            INSERT INTO users (email, password, name, role, bio, field, createdAt)
            VALUES ('seeker@demo.com', '$hashedPassword', 'דני ישראלי', 'jobseeker', 'מפתח Full Stack עם 3 שנות ניסיון', 'tech', NOW())
        ");
        logMessage("Created demo job seeker account (seeker@demo.com / demo123)", 'success');
    }

    // Add some demo jobs if none exist
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM jobs");
    $result = $stmt->fetch();
    if ($result['count'] == 0) {
        // Get the employer ID
        $stmt = $pdo->query("SELECT id FROM users WHERE role = 'employer' LIMIT 1");
        $employer = $stmt->fetch();
        $employerId = $employer ? $employer['id'] : 1;

        $demoJobs = [
            ['מפתח/ת Full Stack', 'TechStart', 'אנחנו מחפשים מפתח/ת Full Stack עם ניסיון ב-React ו-Node.js. הצטרפו לצוות מדהים!', 'תל אביב', '25,000-35,000 ₪', 'משרה מלאה', 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=500'],
            ['מעצב/ת UX/UI', 'DesignHub', 'מעצב/ת UX/UI עם עין לפרטים ויכולת עבודה בסביבה דינמית. ניסיון עם Figma חובה.', 'הרצליה', '20,000-28,000 ₪', 'היברידי', 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=500'],
            ['מנהל/ת שיווק דיגיטלי', 'MarketPro', 'מנהל/ת שיווק דיגיטלי עם ניסיון בקמפיינים ברשתות חברתיות ו-Google Ads.', 'רמת גן', '18,000-25,000 ₪', 'עבודה מהבית', 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=500'],
            ['Data Analyst', 'DataCo', 'אנליסט/ית נתונים עם ידע ב-SQL ו-Python. הזדמנות להשפיע על החלטות עסקיות.', 'תל אביב', '22,000-30,000 ₪', 'משרה מלאה', 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=500'],
            ['DevOps Engineer', 'CloudTech', 'מהנדס/ת DevOps עם ניסיון ב-AWS/GCP, Docker, ו-Kubernetes.', 'פתח תקווה', '30,000-40,000 ₪', 'היברידי', 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=500'],
        ];

        $stmt = $pdo->prepare("
            INSERT INTO jobs (title, company, description, location, salaryRange, type, image, business_id, createdAt)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");

        foreach ($demoJobs as $job) {
            $stmt->execute([...$job, $employerId]);
        }

        logMessage("Created " . count($demoJobs) . " demo job listings", 'success');
    }

    logMessage("Database setup completed successfully!", 'success');

} catch (PDOException $e) {
    logMessage("Database error: " . $e->getMessage(), 'error');
}
?>
<!DOCTYPE html>
<html lang="he" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matcha - Database Setup</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="app-container" style="padding: var(--spacing-xl);">
        <div style="max-width: 600px; margin: 0 auto;">
            <h1 style="color: var(--primary); margin-bottom: var(--spacing-xl);">🍵 Matcha Database Setup</h1>

            <div style="display: flex; flex-direction: column; gap: var(--spacing-md);">
                <?php foreach ($messages as $msg): ?>
                    <div class="profile-section" style="
                    border-right: 4px solid <?php
                    echo $msg['type'] === 'success' ? 'var(--success)' :
                        ($msg['type'] === 'error' ? 'var(--error)' : 'var(--info)');
                    ?>;
                ">
                        <?php if ($msg['type'] === 'success'): ?>
                            ✅
                        <?php elseif ($msg['type'] === 'error'): ?>
                            ❌
                        <?php else: ?>
                            ℹ️
                        <?php endif; ?>
                        <?php echo htmlspecialchars($msg['message']); ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="margin-top: var(--spacing-xxl); text-align: center;">
                <a href="index.php" class="btn btn-primary btn-lg">
                    המשך לאפליקציה
                </a>
            </div>

            <div
                style="margin-top: var(--spacing-xxl); padding: var(--spacing-lg); background: var(--warning-light); border-radius: var(--radius-lg);">
                <h3 style="color: var(--warning); margin-bottom: var(--spacing-sm);">⚠️ Demo Accounts</h3>
                <p style="font-size: 0.875rem; color: var(--secondary);">
                    <strong>מעסיק:</strong> demo@company.com / demo123<br>
                    <strong>מחפש עבודה:</strong> seeker@demo.com / demo123
                </p>
            </div>
        </div>
    </div>
</body>

</html>