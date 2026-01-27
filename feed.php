<?php
$pageTitle = 'פיד משרות';
require_once 'includes/header.php';
requireAuth();

$user = getCurrentUser();

// Redirect employers to their dashboard
if ($user['role'] === 'employer') {
    header('Location: /business/dashboard.php');
    exit;
}
?>

<!-- Header -->
<header class="header">
    <a href="profile.php" class="header-icon-btn">
        <i data-feather="user"></i>
    </a>
    <img src="assets/images/LOGO.jpeg" alt="Matcha" class="header-logo">
    <a href="matches.php" class="header-icon-btn">
        <i data-feather="message-circle"></i>
        <span class="notification-dot" id="matchNotification" style="display: none;"></span>
    </a>
</header>

<!-- Main Feed -->
<main class="feed-container">
    <div class="feed-content">
        <div class="swipe-deck">
            <div id="deckWrapper" class="swipe-deck-wrapper">
                <div class="loading-container" id="loadingState">
                    <div class="loading-spinner"></div>
                    <p>טוען משרות...</p>
                </div>

                <!-- Cards will be inserted here by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Swipe Controls -->
    <div class="swipe-controls">
        <button class="btn-circle nope" id="btnNope" onclick="swipeCard('left')">
            <i data-feather="x"></i>
        </button>
        <button class="btn-circle like" id="btnLike" onclick="swipeCard('right')">
            <i data-feather="heart"></i>
        </button>
    </div>
</main>

<?php include 'includes/nav.php'; ?>

<!-- Job Card Template -->
<template id="jobCardTemplate">
    <div class="swipe-card" data-job-id="">
        <!-- Swipe Indicators -->
        <div class="swipe-indicator like">LIKE</div>
        <div class="swipe-indicator nope">NOPE</div>

        <div class="job-card">
            <div class="job-card-image-container">
                <img class="job-card-image" src="" alt="">
                <div class="job-card-overlay">
                    <h2 class="job-card-title"></h2>
                    <p class="job-card-company"></p>
                </div>
            </div>
            <div class="job-card-content">
                <div class="job-card-tags">
                    <div class="job-tag salary-tag">
                        <i data-feather="dollar-sign"></i>
                        <span></span>
                    </div>
                    <div class="job-tag location-tag">
                        <i data-feather="map-pin"></i>
                        <span></span>
                    </div>
                    <div class="job-tag type-tag">
                        <i data-feather="briefcase"></i>
                        <span></span>
                    </div>
                </div>
                <div class="job-description">
                    <h3>על התפקיד</h3>
                    <p></p>
                </div>
            </div>
        </div>
    </div>
</template>

<!-- Empty State Template -->
<template id="emptyStateTemplate">
    <div class="empty-state">
        <i data-feather="inbox"></i>
        <h2>אין עוד משרות כרגע</h2>
        <p>חזרו מאוחר יותר או הרחיבו את הסינון שלכם</p>
    </div>
</template>

<?php
$additionalScripts = ['/assets/js/swipe.js'];
include 'includes/footer.php';
?>