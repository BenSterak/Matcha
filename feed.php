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

<!-- Daily Pick Banner -->
<div id="dailyPickBanner" style="display: none; margin: var(--spacing-md); padding: 0; border-radius: var(--radius-lg); overflow: hidden; background: linear-gradient(135deg, #f0fdf4, #dcfce7); border: 1px solid #bbf7d0; cursor: pointer;" onclick="swipeDailyPick()">
    <div style="padding: var(--spacing-md); display: flex; align-items: center; gap: var(--spacing-md);">
        <div style="width: 56px; height: 56px; border-radius: var(--radius-md); overflow: hidden; flex-shrink: 0;">
            <img id="dailyPickImage" src="" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
        <div style="flex: 1; min-width: 0;">
            <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 2px;">
                <span style="font-size: 0.7rem; background: var(--primary); color: white; padding: 2px 8px; border-radius: var(--radius-full); font-weight: 600;">Daily Pick</span>
            </div>
            <h3 id="dailyPickTitle" style="font-size: 0.95rem; font-weight: 600; color: var(--secondary); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"></h3>
            <p id="dailyPickCompany" style="font-size: 0.8rem; color: var(--text-muted); margin: 0;"></p>
        </div>
        <i data-feather="chevron-left" style="color: var(--primary); flex-shrink: 0;"></i>
    </div>
</div>

<script>
async function loadDailyPick() {
    try {
        const response = await fetch('/api/jobs.php?action=daily_pick');
        const data = await response.json();
        if (data.success && data.daily_pick) {
            const pick = data.daily_pick;
            document.getElementById('dailyPickImage').src = pick.image;
            document.getElementById('dailyPickTitle').textContent = pick.title;
            document.getElementById('dailyPickCompany').textContent = pick.company + (pick.location ? ' • ' + pick.location : '');
            document.getElementById('dailyPickBanner').style.display = 'block';
            document.getElementById('dailyPickBanner').dataset.jobId = pick.id;
            feather.replace();
        }
    } catch (e) { /* silent */ }
}

async function swipeDailyPick() {
    const jobId = document.getElementById('dailyPickBanner').dataset.jobId;
    if (!jobId) return;
    try {
        await fetch('/api/matches.php?action=swipe', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ jobId: jobId, action: 'like' })
        });
        document.getElementById('dailyPickBanner').innerHTML = '<div style="padding: var(--spacing-md); text-align: center; color: var(--success); font-weight: 600;">שמרת את ה-Daily Pick! &#10003;</div>';
        setTimeout(() => { document.getElementById('dailyPickBanner').style.display = 'none'; }, 2000);
    } catch (e) { alert('שגיאה'); }
}

document.addEventListener('DOMContentLoaded', loadDailyPick);
</script>

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