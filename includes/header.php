<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// Helper function to check if user is logged in
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

// Helper function to get current user
function getCurrentUser()
{
    global $pdo;
    if (!isLoggedIn())
        return null;

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

// Helper function to require authentication
function requireAuth()
{
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit;
    }
}

// Helper function to require specific role
function requireRole($role)
{
    requireAuth();
    $user = getCurrentUser();
    if ($user['role'] !== $role) {
        header('Location: /feed.php');
        exit;
    }
}

// Determine current page for navigation highlighting
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="he" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#10B981">
    <meta name="description" content="Matcha - מצא את העבודה הבאה שלך בהחלקה">

    <title>
        <?php echo isset($pageTitle) ? $pageTitle . ' - Matcha' : 'Matcha - מצא את העבודה הבאה שלך'; ?>
    </title>

    <!-- Google Analytics - Replace GA_MEASUREMENT_ID with your actual ID -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'GA_MEASUREMENT_ID');
    </script>

    <link rel="stylesheet" href="/assets/css/style.css">

    <!-- Feather Icons -->
    <script src="https://unpkg.com/feather-icons"></script>
</head>

<body>
    <div class="app-container">