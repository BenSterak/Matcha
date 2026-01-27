<?php
$pageTitle = 'המשרות שלי';
require_once '../includes/header.php';
requireRole('employer');

// Get user's jobs
try {
    $stmt = $pdo->prepare("
        SELECT j.*, 
            (SELECT COUNT(*) FROM matches m WHERE m.jobId = j.id AND m.status = 'pending') as pendingCount,
            (SELECT COUNT(*) FROM matches m WHERE m.jobId = j.id AND m.status = 'matched') as matchedCount
        FROM jobs j 
        WHERE j.business_id = ?
        ORDER BY j.createdAt DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $jobs = $stmt->fetchAll();
} catch (PDOException $e) {
    $jobs = [];
}
?>

<!-- Header -->
<header class="header">
    <a href="/business/dashboard.php" class="header-icon-btn">
        <i data-feather="arrow-right"></i>
    </a>
    <h1 style="font-size: 1.125rem; font-weight: 600;">המשרות שלי</h1>
    <a href="/business/job-edit.php" class="header-icon-btn" style="color: var(--primary);">
        <i data-feather="plus"></i>
    </a>
</header>

<main class="profile-page">
    <?php if (empty($jobs)): ?>
        <div class="empty-state">
            <i data-feather="briefcase"></i>
            <h2>אין לכם משרות עדיין</h2>
            <p>התחילו לפרסם משרות כדי למצוא את המועמדים המושלמים!</p>
            <a href="/business/job-edit.php" class="btn btn-primary" style="margin-top: var(--spacing-lg);">
                <i data-feather="plus" style="width: 18px; height: 18px;"></i>
                פרסום משרה חדשה
            </a>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: var(--spacing-md);">
            <?php foreach ($jobs as $job): ?>
                <div class="profile-section" style="padding: 0; overflow: hidden;">
                    <div style="display: flex; gap: var(--spacing-md); padding: var(--spacing-md);">
                        <img src="<?php echo htmlspecialchars($job['image'] ?: 'https://via.placeholder.com/80'); ?>"
                            alt="<?php echo htmlspecialchars($job['title']); ?>"
                            style="width: 80px; height: 80px; border-radius: var(--radius-md); object-fit: cover;">
                        <div style="flex: 1;">
                            <h3
                                style="font-size: 1rem; font-weight: 600; color: var(--secondary); margin-bottom: var(--spacing-xs);">
                                <?php echo htmlspecialchars($job['title']); ?>
                            </h3>
                            <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: var(--spacing-sm);">
                                <?php echo htmlspecialchars($job['location']); ?> •
                                <?php echo htmlspecialchars($job['type'] ?: 'משרה מלאה'); ?>
                            </p>
                            <div style="display: flex; gap: var(--spacing-sm);">
                                <span class="match-status pending">
                                    <?php echo $job['pendingCount']; ?> ממתינים
                                </span>
                                <span class="match-status matched">
                                    <?php echo $job['matchedCount']; ?> התאמות
                                </span>
                            </div>
                        </div>
                    </div>
                    <div style="display: flex; border-top: 1px solid var(--border);">
                        <a href="/business/job-edit.php?id=<?php echo $job['id']; ?>"
                            style="flex: 1; padding: var(--spacing-md); text-align: center; color: var(--primary); font-weight: 500; text-decoration: none;">
                            <i data-feather="edit-2" style="width: 16px; height: 16px;"></i>
                            עריכה
                        </a>
                        <a href="/business/candidates.php?job=<?php echo $job['id']; ?>"
                            style="flex: 1; padding: var(--spacing-md); text-align: center; color: var(--secondary); font-weight: 500; text-decoration: none; border-right: 1px solid var(--border);">
                            <i data-feather="users" style="width: 16px; height: 16px;"></i>
                            מועמדים
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <a href="/business/job-edit.php" class="btn btn-primary btn-full" style="margin-top: var(--spacing-xl);">
            <i data-feather="plus" style="width: 18px; height: 18px;"></i>
            פרסום משרה חדשה
        </a>
    <?php endif; ?>
</main>

<?php include '../includes/nav.php'; ?>
<?php include '../includes/footer.php'; ?>