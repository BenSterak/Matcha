<?php
$pageTitle = 'מועמדים';
require_once '../includes/header.php';
requireRole('employer');

$jobFilter = $_GET['job'] ?? null;

// Get jobs for filter dropdown
try {
    $stmt = $pdo->prepare("SELECT id, title FROM jobs WHERE business_id = ? ORDER BY createdAt DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $jobs = $stmt->fetchAll();
} catch (PDOException $e) {
    $jobs = [];
}

// Get candidates
try {
    $sql = "
        SELECT m.*, u.name, u.email, u.bio, u.photo, u.field, u.salary as expectedSalary, u.workModel,
               j.title as jobTitle, j.id as jobId
        FROM matches m
        JOIN users u ON m.userId = u.id
        JOIN jobs j ON m.jobId = j.id
        WHERE j.business_id = ? AND m.status IN ('pending', 'matched')
    ";
    $params = [$_SESSION['user_id']];

    if ($jobFilter) {
        $sql .= " AND j.id = ?";
        $params[] = $jobFilter;
    }

    $sql .= " ORDER BY m.status ASC, m.createdAt DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $candidates = $stmt->fetchAll();
} catch (PDOException $e) {
    $candidates = [];
}
?>

<!-- Header -->
<header class="header">
    <a href="/business/dashboard.php" class="header-icon-btn">
        <i data-feather="arrow-right"></i>
    </a>
    <h1 style="font-size: 1.125rem; font-weight: 600;">מועמדים</h1>
    <div style="width: 44px;"></div>
</header>

<main class="profile-page">
    <!-- Filter -->
    <?php if (count($jobs) > 1): ?>
        <div style="margin-bottom: var(--spacing-lg);">
            <select id="jobFilter" class="form-input" style="padding-right: var(--spacing-md);"
                onchange="filterByJob(this.value)">
                <option value="">כל המשרות</option>
                <?php foreach ($jobs as $job): ?>
                    <option value="<?php echo $job['id']; ?>" <?php echo $jobFilter == $job['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($job['title']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    <?php endif; ?>

    <?php if (empty($candidates)): ?>
        <div class="empty-state">
            <i data-feather="users"></i>
            <h2>אין מועמדים עדיין</h2>
            <p>כשמועמדים יחליקו ימינה על המשרות שלכם, הם יופיעו כאן!</p>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: var(--spacing-md);" id="candidatesList">
            <?php foreach ($candidates as $candidate): ?>
                <div class="profile-section candidate-card" data-match-id="<?php echo $candidate['id']; ?>"
                    style="padding: 0; overflow: hidden;">
                    <div style="display: flex; gap: var(--spacing-md); padding: var(--spacing-md);">
                        <img src="<?php echo htmlspecialchars($candidate['photo'] ?: 'https://ui-avatars.com/api/?name=' . urlencode($candidate['name']) . '&background=2ECC71&color=fff'); ?>"
                            alt="<?php echo htmlspecialchars($candidate['name']); ?>"
                            style="width: 70px; height: 70px; border-radius: var(--radius-full); object-fit: cover;">
                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <h3 style="font-size: 1rem; font-weight: 600; color: var(--secondary);">
                                    <?php echo htmlspecialchars($candidate['name']); ?>
                                </h3>
                                <span class="match-status <?php echo $candidate['status']; ?>">
                                    <?php echo $candidate['status'] === 'matched' ? 'התאמה!' : 'ממתין'; ?>
                                </span>
                            </div>
                            <p style="font-size: 0.875rem; color: var(--primary); margin-bottom: var(--spacing-xs);">
                                למשרת:
                                <?php echo htmlspecialchars($candidate['jobTitle']); ?>
                            </p>
                            <?php if ($candidate['bio']): ?>
                                <p
                                    style="font-size: 0.875rem; color: var(--text-muted); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    <?php echo htmlspecialchars($candidate['bio']); ?>
                                </p>
                            <?php endif; ?>
                            <div style="display: flex; gap: var(--spacing-sm); margin-top: var(--spacing-sm); flex-wrap: wrap;">
                                <?php if ($candidate['field']): ?>
                                    <span class="job-tag">
                                        <i data-feather="briefcase" style="width: 12px; height: 12px;"></i>
                                        <?php echo htmlspecialchars($candidate['field']); ?>
                                    </span>
                                <?php endif; ?>
                                <?php if ($candidate['expectedSalary']): ?>
                                    <span class="job-tag">
                                        <i data-feather="dollar-sign" style="width: 12px; height: 12px;"></i>
                                        <?php echo number_format($candidate['expectedSalary']); ?> ₪
                                    </span>
                                <?php endif; ?>
                                <?php if ($candidate['workModel']): ?>
                                    <span class="job-tag">
                                        <i data-feather="home" style="width: 12px; height: 12px;"></i>
                                        <?php
                                        $workModels = ['office' => 'משרד', 'remote' => 'מהבית', 'hybrid' => 'היברידי'];
                                        echo $workModels[$candidate['workModel']] ?? $candidate['workModel'];
                                        ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php if ($candidate['status'] === 'pending'): ?>
                        <div style="display: flex; border-top: 1px solid var(--border);">
                            <button onclick="rejectCandidate(<?php echo $candidate['id']; ?>)"
                                style="flex: 1; padding: var(--spacing-md); text-align: center; color: var(--nope); font-weight: 600; background: none; border: none; cursor: pointer;">
                                <i data-feather="x" style="width: 18px; height: 18px;"></i>
                                לא מתאים
                            </button>
                            <button onclick="approveCandidate(<?php echo $candidate['id']; ?>)"
                                style="flex: 1; padding: var(--spacing-md); text-align: center; color: var(--like); font-weight: 600; background: none; border: none; cursor: pointer; border-right: 1px solid var(--border);">
                                <i data-feather="check" style="width: 18px; height: 18px;"></i>
                                מאשר!
                            </button>
                        </div>
                    <?php else: ?>
                        <div style="display: flex; border-top: 1px solid var(--border);">
                            <a href="mailto:<?php echo htmlspecialchars($candidate['email']); ?>"
                                style="flex: 1; padding: var(--spacing-md); text-align: center; color: var(--primary); font-weight: 500; text-decoration: none;">
                                <i data-feather="mail" style="width: 16px; height: 16px;"></i>
                                שליחת מייל
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<script>
    function filterByJob(jobId) {
        if (jobId) {
            window.location.href = '/business/candidates.php?job=' + jobId;
        } else {
            window.location.href = '/business/candidates.php';
        }
    }

    async function approveCandidate(matchId) {
        try {
            const response = await fetch('/api/matches.php?action=approve&id=' + matchId, {
                method: 'POST'
            });
            const data = await response.json();

            if (data.success) {
                // Update UI
                const card = document.querySelector(`[data-match-id="${matchId}"]`);
                const statusEl = card.querySelector('.match-status');
                statusEl.textContent = 'התאמה!';
                statusEl.classList.remove('pending');
                statusEl.classList.add('matched');

                // Update buttons
                const buttonsContainer = card.querySelector('[style*="border-top"]');
                buttonsContainer.innerHTML = `
                <a href="#" style="flex: 1; padding: var(--spacing-md); text-align: center; color: var(--primary); font-weight: 500; text-decoration: none;">
                    <i data-feather="mail" style="width: 16px; height: 16px;"></i>
                    שליחת מייל
                </a>
            `;
                feather.replace();
            } else {
                alert('אירעה שגיאה');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('אירעה שגיאה');
        }
    }

    async function rejectCandidate(matchId) {
        if (!confirm('האם אתם בטוחים שברצונכם לדחות את המועמד?')) {
            return;
        }

        try {
            const response = await fetch('/api/matches.php?action=reject&id=' + matchId, {
                method: 'POST'
            });
            const data = await response.json();

            if (data.success) {
                // Remove card from UI
                const card = document.querySelector(`[data-match-id="${matchId}"]`);
                card.style.animation = 'fadeOut 0.3s ease';
                setTimeout(() => card.remove(), 300);
            } else {
                alert('אירעה שגיאה');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('אירעה שגיאה');
        }
    }
</script>

<style>
    @keyframes fadeOut {
        to {
            opacity: 0;
            transform: translateX(-100%);
        }
    }
</style>

<?php include '../includes/nav.php'; ?>
<?php include '../includes/footer.php'; ?>