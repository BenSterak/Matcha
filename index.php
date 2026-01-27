<?php
$pageTitle = 'ברוכים הבאים';
session_start();

// If user is already logged in, redirect to feed
if (isset($_SESSION['user_id'])) {
    header('Location: /feed.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="he" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#2ECC71">
    <meta name="description" content="Matcha - מצא את העבודה הבאה שלך בהחלקה">
    <title>Matcha - מצא את העבודה הבאה שלך</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://unpkg.com/feather-icons"></script>
</head>

<body>
    <div class="app-container welcome-page">
        <!-- Background Decorations -->
        <div class="welcome-decor welcome-decor-1"></div>
        <div class="welcome-decor welcome-decor-2"></div>

        <!-- Content -->
        <div class="welcome-content">
            <div class="welcome-logo-container">
                <img src="assets/images/LOGO.jpeg" alt="Matcha Logo" class="welcome-logo">
            </div>

            <h1 class="welcome-title">Matcha</h1>
            <p class="welcome-tagline">המשחק החדש של עולם הגיוס - מצאו את ההתאמה המושלמת</p>

            <div class="welcome-features">
                <div class="welcome-feature">
                    <div class="welcome-feature-icon">
                        <i data-feather="zap"></i>
                    </div>
                    <span class="welcome-feature-text">התאמה חכמה</span>
                </div>
                <div class="welcome-feature">
                    <div class="welcome-feature-icon">
                        <i data-feather="users"></i>
                    </div>
                    <span class="welcome-feature-text">קשר ישיר</span>
                </div>
                <div class="welcome-feature">
                    <div class="welcome-feature-icon">
                        <i data-feather="briefcase"></i>
                    </div>
                    <span class="welcome-feature-text">משרות איכותיות</span>
                </div>
            </div>

            <div class="welcome-actions">
                <a href="register.php" class="btn btn-primary btn-lg btn-full">
                    בואו נתחיל
                    <i data-feather="arrow-left"></i>
                </a>
                <a href="login.php" class="welcome-login-link">יש לי כבר חשבון</a>
            </div>
        </div>

        <footer class="welcome-footer">
            <p>Matcha
                <?php echo date('Y'); ?> - כל הזכויות שמורות
            </p>
        </footer>
    </div>

    <script>
        feather.replace();
    </script>
</body>

</html>