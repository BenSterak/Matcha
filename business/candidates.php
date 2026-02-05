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
               u.resume_file, u.portfolio_url, u.last_seen,
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

<script>
    // Pass PHP data to JS
    const candidatesData = <?php echo json_encode($candidates); ?>;
</script>

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
                    onclick="showCandidate(<?php echo $candidate['id']; ?>)"
                    style="padding: 0; overflow: hidden; cursor: pointer; transition: transform 0.2s;">
                    <div style="display: flex; gap: var(--spacing-md); padding: var(--spacing-md);">
                        <img src="<?php echo htmlspecialchars($candidate['photo'] ?: 'https://ui-avatars.com/api/?name=' . urlencode($candidate['name']) . '&background=2ECC71&color=fff'); ?>"
                            alt="<?php echo htmlspecialchars($candidate['name']); ?>"
                            style="width: 70px; height: 70px; border-radius: var(--radius-full); object-fit: cover;">
                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <h3 style="font-size: 1rem; font-weight: 600; color: var(--secondary); display: flex; align-items: center; gap: 6px;">
                                    <?php echo htmlspecialchars($candidate['name']); ?>
                                    <?php if (isUserOnline($candidate['last_seen'] ?? null)): ?>
                                        <span style="width: 8px; height: 8px; background: var(--success); border-radius: 50%; display: inline-block;" title="מחובר/ת"></span>
                                    <?php endif; ?>
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
                                        $workModels = [
                                            'office' => 'משרד',
                                            'remote' => 'מהבית',
                                            'hybrid' => 'היברידי',
                                            'physical' => 'עבודה פיזית',
                                            'field' => 'עבודת שטח'
                                        ];
                                        echo $workModels[$candidate['workModel']] ?? $candidate['workModel'];
                                        ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php if ($candidate['status'] === 'pending'): ?>
                        <div style="display: flex; border-top: 1px solid var(--border);" onclick="event.stopPropagation()">
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
                        <div style="display: flex; border-top: 1px solid var(--border);" onclick="event.stopPropagation()">
                            <a href="/chat.php?match=<?php echo $candidate['id']; ?>"
                                style="flex: 1; padding: var(--spacing-md); text-align: center; color: var(--primary); font-weight: 500; text-decoration: none;">
                                <i data-feather="message-circle" style="width: 16px; height: 16px;"></i>
                                צ'אט
                            </a>
                            <a href="mailto:<?php echo htmlspecialchars($candidate['email']); ?>"
                                style="flex: 1; padding: var(--spacing-md); text-align: center; color: var(--text-muted); font-weight: 500; text-decoration: none; border-right: 1px solid var(--border);">
                                <i data-feather="mail" style="width: 16px; height: 16px;"></i>
                                שליחת מייל
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Candidate Detail Modal -->
    <div id="candidateModal" class="modal-overlay" style="display: none; align-items: flex-end;">
        <div class="modal-content slide-up"
            style="width: 100%; height: 85vh; border-radius: 20px 20px 0 0; padding: 0; display: flex; flex-direction: column;">

            <!-- Sticky Header -->
            <div
                style="padding: var(--spacing-lg); border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0;">פרטי מועמד</h3>
                <button onclick="closeModal()" class="btn btn-ghost" style="padding: 5px;">
                    <i data-feather="x"></i>
                </button>
            </div>

            <!-- Scrollable Content -->
            <div id="modalBody" style="flex: 1; overflow-y: auto; padding: var(--spacing-lg);">
                <!-- Dynamic Content -->
            </div>

            <!-- Sticky Footer Action -->
            <div id="modalFooter"
                style="padding: var(--spacing-lg); border-top: 1px solid var(--border); display: flex; gap: var(--spacing-md);">
                <!-- Dynamic Buttons -->
            </div>
        </div>
    </div>
</main>

<style>
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        backdrop-filter: blur(2px);
    }

    .modal-content.slide-up {
        animation: slideUp 0.3s ease-out;
        background: var(--surface);
    }

    @keyframes slideUp {
        from {
            transform: translateY(100%);
        }

        to {
            transform: translateY(0);
        }
    }

    .candidate-card:active {
        transform: scale(0.98);
    }
</style>

<script>
    function filterByJob(jobId) {
        if (jobId) {
            window.location.href = '/business/candidates.php?job=' + jobId;
        } else {
            window.location.href = '/business/candidates.php';
        }
    }

    function showCandidate(id) {
        const candidate = candidatesData.find(c => c.id == id);
        if (!candidate) return;

        const modal = document.getElementById('candidateModal');
        const body = document.getElementById('modalBody');
        const footer = document.getElementById('modalFooter');

        const esc = Matcha.escapeHtml;
        const workModelLabels = {
            'remote': 'מהבית', 'hybrid': 'היברידי', 'office': 'משרד',
            'physical': 'עבודה פיזית', 'field': 'עבודת שטח'
        };
        const onlineDot = candidate.last_seen && (Date.now() - new Date(candidate.last_seen).getTime()) < 300000
            ? '<span style="width:8px;height:8px;background:var(--success);border-radius:50%;display:inline-block;" title="מחובר/ת"></span>' : '';

        // Populate Body
        body.innerHTML = `
            <div style="display: flex; align-items: center; gap: var(--spacing-md); margin-bottom: var(--spacing-lg);">
                <img src="${esc(candidate.photo) || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(candidate.name)}"
                     style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;">
                <div>
                    <h2 style="margin: 0; font-size: 1.25rem; display: flex; align-items: center; gap: 6px;">
                        ${esc(candidate.name)} ${onlineDot}
                    </h2>
                    <p style="color: var(--primary); margin: 0;">${esc(candidate.jobTitle)}</p>
                </div>
            </div>

            <div style="margin-bottom: var(--spacing-lg);">
                <h4 style="margin-bottom: var(--spacing-sm);">על עצמי</h4>
                <p style="color: var(--text-muted); line-height: 1.6;">${esc(candidate.bio) || 'אין מידע נוסף'}</p>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--spacing-md); margin-bottom: var(--spacing-lg);">
                <div style="background: var(--background); padding: var(--spacing-md); border-radius: var(--radius-md);">
                    <div style="color: var(--text-light); font-size: 0.8rem;">שכר ציפייה</div>
                    <div style="font-weight: 600;">${candidate.expectedSalary ? parseInt(candidate.expectedSalary).toLocaleString() + ' ₪' : '-'}</div>
                </div>
                <div style="background: var(--background); padding: var(--spacing-md); border-radius: var(--radius-md);">
                    <div style="color: var(--text-light); font-size: 0.8rem;">תחום</div>
                    <div style="font-weight: 600;">${esc(candidate.field) || '-'}</div>
                </div>
                <div style="background: var(--background); padding: var(--spacing-md); border-radius: var(--radius-md);">
                    <div style="color: var(--text-light); font-size: 0.8rem;">מודל עבודה</div>
                    <div style="font-weight: 600;">${workModelLabels[candidate.workModel] || 'משרד'}</div>
                </div>
            </div>

            <h4 style="margin-bottom: var(--spacing-sm);">מסמכים וקישורים</h4>
            <div style="display: flex; flex-direction: column; gap: var(--spacing-sm);">
                ${candidate.resume_file ? `
                    <a href="${esc(candidate.resume_file)}" target="_blank" class="btn btn-secondary w-full" style="justify-content: flex-start;">
                        <i data-feather="file-text"></i> צפייה בקורות חיים
                    </a>
                ` : ''}
                ${candidate.portfolio_url ? `
                    <a href="${esc(candidate.portfolio_url)}" target="_blank" class="btn btn-secondary w-full" style="justify-content: flex-start;">
                        <i data-feather="globe"></i> תיק עבודות / אתר
                    </a>
                ` : ''}
                ${!candidate.resume_file && !candidate.portfolio_url ? '<p style="color: var(--text-muted);">אין קישורים נוספים</p>' : ''}
            </div>
        `;

        // Populate Footer Buttons
        if (candidate.status === 'pending') {
            footer.innerHTML = `
                <button onclick="rejectCandidate(${candidate.id}); closeModal()" class="btn w-full" style="background: var(--surface); color: var(--nope); border: 1px solid var(--nope);">
                    <i data-feather="x"></i> לא מתאים
                </button>
                <button onclick="approveCandidate(${candidate.id}); closeModal()" class="btn btn-primary w-full" style="background: var(--like);">
                    <i data-feather="check"></i> מאשר!
                </button>
            `;
        } else {
            footer.innerHTML = `
                <a href="/chat.php?match=${candidate.id}" class="btn btn-primary w-full" style="background: var(--primary);">
                    <i data-feather="message-circle"></i> מעבר לצ'אט
                </a>
                <a href="mailto:${Matcha.escapeHtml(candidate.email)}" class="btn w-full" style="background: var(--surface); color: var(--primary); border: 1px solid var(--border);">
                    <i data-feather="mail"></i> שליחת מייל
                </a>
            `;
        }

        modal.style.display = 'flex';
        feather.replace();
    }

    function closeModal() {
        document.getElementById('candidateModal').style.display = 'none';
    }

    // Close modal when clicking outside
    document.getElementById('candidateModal').addEventListener('click', function (e) {
        if (e.target === this) closeModal();
    });

    async function approveCandidate(matchId) {
        // ... existing approve logic ...
        try {
            const response = await fetch('/api/matches.php?action=approve&id=' + matchId, {
                method: 'POST'
            });
            const data = await response.json();

            if (data.success) {
                // Update UI in list
                const card = document.querySelector(`[data-match-id="${matchId}"]`);
                if (card) {
                    const statusEl = card.querySelector('.match-status');
                    statusEl.textContent = 'התאמה!';
                    statusEl.classList.remove('pending');
                    statusEl.classList.add('matched');

                    // Update buttons in list
                    const buttonsContainer = card.querySelector('[style*="border-top"]');
                    if (buttonsContainer) {
                        buttonsContainer.onclick = function (e) { e.stopPropagation(); }; // Ensure stopPropagation remains
                        buttonsContainer.innerHTML = `
                            <a href="/chat.php?match=${matchId}" style="flex: 1; padding: var(--spacing-md); text-align: center; color: var(--primary); font-weight: 500; text-decoration: none;">
                                <i data-feather="message-circle" style="width: 16px; height: 16px;"></i>
                                צ'אט
                            </a>
                        `;
                    }
                }
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
        // ... existing reject logic ...
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
                if (card) {
                    card.style.animation = 'fadeOut 0.3s ease';
                    setTimeout(() => card.remove(), 300);
                }
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