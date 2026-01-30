<?php
$pageTitle = 'פיד משרות';
require_once 'includes/header.php';
requireAuth();

$user = getCurrentUser();

// Check if this is a new user
$isNewUser = isset($_SESSION['new_user']) && $_SESSION['new_user'] === true;
if ($isNewUser) {
    unset($_SESSION['new_user']); // Clear the flag
}

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
    <img src="assets/images/ICON.jpeg" alt="Matcha" class="header-logo">
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

    <!-- Swipe Controls Removed as per user request -->
</main>

<?php include 'includes/nav.php'; ?>

<!-- Job Card Template -->
<template id="jobCardTemplate">
    <div class="swipe-card" data-job-id="">
        <!-- Swipe Indicators -->
        <div class="swipe-indicator like">
            <span>מעוניין/ת</span>
            <i data-feather="check"></i>
        </div>
        <div class="swipe-indicator nope">
            <span>לא כרגע</span>
            <i data-feather="x"></i>
        </div>

        <div class="job-card">
            <!-- Match Score Badge -->
            <div class="match-score-badge">
                <span class="score-value">95%</span>
                <span class="score-label">התאמה</span>
            </div>

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
                        <span>שכר: </span>
                        <span class="salary-value"></span>
                        <span style="font-weight: bold;">₪</span>
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

                <div class="job-details-grid">
                    <div class="detail-item">
                        <span class="label">דרישות</span>
                        <span class="value requirements-value">3+ שנות ניסיון</span>
                    </div>
                    <div class="detail-item">
                        <span class="label">מודל עבודה</span>
                        <span class="value work-model-value">היברידי</span>
                    </div>
                </div>

                <div class="job-description">
                    <h3>על התפקיד</h3>
                    <p></p>
                </div>

                <div class="swipe-instruction">
                    <div class="instruction-side left">
                        <i data-feather="arrow-right"></i>
                        <span>לשמירה</span>
                    </div>
                    <div class="instruction-side right">
                        <span>לדלג</span>
                        <i data-feather="arrow-left"></i>
                    </div>
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

<?php if ($isNewUser): ?>
    <!-- Welcome Modal for New Users -->
    <div id="welcomeModal" class="modal-overlay" style="display: flex;">
        <div class="modal-content welcome-modal">
            <div class="welcome-modal-icon">
                <i data-feather="check-circle"></i>
            </div>
            <h2>ברוכים הבאים, <?php echo htmlspecialchars($user['name']); ?>!</h2>
            <p>תודה שהצטרפתם ל-Matcha! אנחנו שמחים שבחרתם בנו למצוא את המשרה הבאה שלכם.</p>
            <div class="welcome-tips">
                <div class="welcome-tip">
                    <i data-feather="arrow-left"></i>
                    <span>החליקו שמאלה לדלג</span>
                </div>
                <div class="welcome-tip">
                    <i data-feather="arrow-right"></i>
                    <span>החליקו ימינה ללייק</span>
                </div>
            </div>
            <button class="btn btn-primary btn-full" onclick="closeWelcomeModal()">
                בואו נתחיל!
            </button>
        </div>
    </div>

    <style>
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--overlay);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: var(--spacing-lg);
        }

        .modal-content {
            background: var(--surface);
            border-radius: var(--radius-xl);
            padding: var(--spacing-xl);
            max-width: 380px;
            width: 100%;
            text-align: center;
            animation: scaleIn 0.3s ease;
        }

        .welcome-modal-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--success-light);
            color: var(--success);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto var(--spacing-lg);
        }

        .welcome-modal-icon svg {
            width: 40px;
            height: 40px;
        }

        .welcome-modal h2 {
            font-size: 1.5rem;
            color: var(--secondary);
            margin-bottom: var(--spacing-sm);
        }

        .welcome-modal p {
            color: var(--text-muted);
            margin-bottom: var(--spacing-lg);
            line-height: 1.6;
        }

        .welcome-tips {
            display: flex;
            gap: var(--spacing-md);
            justify-content: center;
            margin-bottom: var(--spacing-xl);
        }

        .welcome-tip {
            display: flex;
            align-items: center;
            gap: var(--spacing-xs);
            font-size: 0.875rem;
            color: var(--text-muted);
            background: var(--background);
            padding: var(--spacing-sm) var(--spacing-md);
            border-radius: var(--radius-full);
        }

        .welcome-tip svg {
            width: 16px;
            height: 16px;
            color: var(--primary);
        }
    </style>

    <script>
        function closeWelcomeModal() {
            document.getElementById('welcomeModal').style.display = 'none';
        }
    </script>
<?php endif; ?>

<?php
$additionalScripts = ['/assets/js/swipe.js'];
include 'includes/footer.php';
?>