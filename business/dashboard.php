<?php
$pageTitle = 'לוח בקרה';
require_once '../includes/header.php';
requireRole('employer');

$user = getCurrentUser();

// Check if this is a new user
$isNewUser = isset($_SESSION['new_user']) && $_SESSION['new_user'] === true;
if ($isNewUser) {
    unset($_SESSION['new_user']); // Clear the flag
}

// Get stats
try {
    // Count jobs
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM jobs WHERE business_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $jobsCount = $stmt->fetch()['count'];

    // Count pending candidates
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count FROM matches m
        JOIN jobs j ON m.jobId = j.id
        WHERE j.business_id = ? AND m.status = 'pending'
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $pendingCount = $stmt->fetch()['count'];

    // Count matched
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count FROM matches m
        JOIN jobs j ON m.jobId = j.id
        WHERE j.business_id = ? AND m.status = 'matched'
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $matchedCount = $stmt->fetch()['count'];
} catch (PDOException $e) {
    $jobsCount = 0;
    $pendingCount = 0;
    $matchedCount = 0;
}
?>

<!-- Header -->
<header class="header">
    <div style="width: 44px;"></div>
    <img src="/assets/images/ICON.jpeg" alt="Matcha" class="header-logo">
    <a href="/profile.php" class="header-icon-btn">
        <i data-feather="user"></i>
    </a>
</header>

<main class="profile-page">
    <div style="text-align: center; margin-bottom: var(--spacing-xl);">
        <h1 style="font-size: 1.5rem; margin-bottom: var(--spacing-xs);">
            שלום,
            <?php echo htmlspecialchars($user['name']); ?>! 👋
        </h1>
        <p style="color: var(--text-muted);">
            <?php echo htmlspecialchars($user['company_name'] ?? 'החברה שלי'); ?>
        </p>
    </div>

    <!-- Stats Cards -->
    <div
        style="display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--spacing-md); margin-bottom: var(--spacing-xl);">
        <a href="/business/jobs.php" class="profile-section" style="text-align: center; text-decoration: none;">
            <div style="font-size: 2rem; font-weight: 700; color: var(--primary);">
                <?php echo $jobsCount; ?>
            </div>
            <div style="font-size: 0.875rem; color: var(--text-muted);">משרות פעילות</div>
        </a>

        <a href="/business/candidates.php" class="profile-section" style="text-align: center; text-decoration: none;">
            <div style="font-size: 2rem; font-weight: 700; color: var(--warning);">
                <?php echo $pendingCount; ?>
            </div>
            <div style="font-size: 0.875rem; color: var(--text-muted);">ממתינים לאישור</div>
        </a>

        <div class="profile-section" style="text-align: center;">
            <div style="font-size: 2rem; font-weight: 700; color: var(--success);">
                <?php echo $matchedCount; ?>
            </div>
            <div style="font-size: 0.875rem; color: var(--text-muted);">התאמות</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="profile-section">
        <h3 class="profile-section-title">
            <i data-feather="zap" style="width: 18px; height: 18px;"></i>
            פעולות מהירות
        </h3>

        <div style="display: flex; flex-direction: column; gap: var(--spacing-md);">
            <a href="/business/job-edit.php" class="btn btn-primary btn-full">
                <i data-feather="plus" style="width: 18px; height: 18px;"></i>
                פרסום משרה חדשה
            </a>

            <a href="/business/candidates.php" class="btn btn-secondary btn-full">
                <i data-feather="users" style="width: 18px; height: 18px;"></i>
                צפייה במועמדים
            </a>

            <a href="/business/jobs.php" class="btn btn-outline btn-full">
                <i data-feather="briefcase" style="width: 18px; height: 18px;"></i>
                ניהול משרות
            </a>
        </div>
    </div>

    <!-- Tips -->
    <div class="profile-section" style="background: var(--primary-light); border: 1px solid var(--primary);">
        <h3 class="profile-section-title" style="color: var(--primary-dark);">
            <i data-feather="info" style="width: 18px; height: 18px;"></i>
            טיפ
        </h3>
        <p style="color: var(--secondary); font-size: 0.875rem;">
            ככל שתוסיפו יותר פרטים למשרה (תמונה, תיאור מפורט, תגיות), כך תקבלו יותר מועמדים מתאימים!
        </p>
    </div>
</main>

<?php include '../includes/nav.php'; ?>

<?php if ($isNewUser): ?>
<!-- Welcome Modal for New Employers -->
<div id="welcomeModal" class="modal-overlay" style="display: flex;">
    <div class="modal-content welcome-modal">
        <div class="welcome-modal-icon">
            <i data-feather="check-circle"></i>
        </div>
        <h2>ברוכים הבאים, <?php echo htmlspecialchars($user['name']); ?>!</h2>
        <p>תודה שהצטרפתם ל-Matcha! אנחנו שמחים שבחרתם בנו למצוא את העובדים המתאימים לחברה שלכם.</p>
        <div class="welcome-tips">
            <div class="welcome-tip">
                <i data-feather="plus"></i>
                <span>פרסמו משרה</span>
            </div>
            <div class="welcome-tip">
                <i data-feather="users"></i>
                <span>צפו במועמדים</span>
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

<?php include '../includes/footer.php'; ?>