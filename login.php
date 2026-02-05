<?php
session_start();
require_once 'config/db.php';

// If user is already logged in, redirect
if (isset($_SESSION['user_id'])) {
    header('Location: /feed.php');
    exit;
}

$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'אנא מלאו את כל השדות';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Check if user is blocked
                if (!empty($user['is_blocked'])) {
                    $error = 'החשבון שלך נחסם. פנה לתמיכה לפרטים נוספים.';
                } else {
                    // Login successful
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['user_name'] = $user['name'];

                    // Redirect based on role
                    if (!empty($user['is_admin'])) {
                        header('Location: /admin/');
                    } elseif ($user['role'] === 'employer') {
                        header('Location: /business/dashboard.php');
                    } else {
                        header('Location: /feed.php');
                    }
                    exit;
                }
            } else {
                $error = 'אימייל או סיסמה שגויים';
            }
        } catch (PDOException $e) {
            $error = 'אירעה שגיאה. אנא נסו שוב.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="he" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#2ECC71">
    <title>התחברות - Matcha</title>

    <!-- Google Analytics - Replace GA_MEASUREMENT_ID with your actual ID -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'GA_MEASUREMENT_ID');
    </script>

    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://unpkg.com/feather-icons"></script>
</head>

<body>
    <div class="app-container auth-page">
        <a href="index.php" class="auth-back-btn">
            <i data-feather="arrow-right"></i>
            חזרה לדף הבית
        </a>

        <div class="auth-content">
            <div class="auth-logo-container">
                <img src="assets/images/ICON.jpeg" alt="Matcha Logo" class="auth-logo">
                <h1 class="auth-title">ברוכים השבים</h1>
                <p class="auth-subtitle">התחברו כדי להמשיך את המסע התעסוקתי שלכם</p>
            </div>

            <form method="POST" class="auth-form">
                <?php if ($error): ?>
                    <div class="auth-error">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label class="form-label">אימייל</label>
                    <div class="input-wrapper">
                        <i data-feather="mail"></i>
                        <input type="email" name="email" class="form-input" placeholder="your@email.com"
                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required
                            autocomplete="email">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">סיסמה</label>
                    <div class="input-wrapper">
                        <i data-feather="lock"></i>
                        <input type="password" name="password" class="form-input" placeholder="הזינו את הסיסמה שלכם"
                            required autocomplete="current-password">
                    </div>
                </div>

                <a href="#" class="auth-forgot-link">שכחתי סיסמה</a>

                <button type="submit" class="btn btn-primary btn-full">התחברות</button>
            </form>

            <div class="auth-divider">
                <span>או</span>
            </div>

            <p class="auth-register-link">
                עדיין אין לכם חשבון? <a href="register.php">הירשמו עכשיו</a>
            </p>
        </div>
    </div>

    <script>
        feather.replace();
    </script>
</body>

</html>