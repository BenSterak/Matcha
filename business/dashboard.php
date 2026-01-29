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

<main class="main-content">
    <div class="dashboard-header text-center mb-xl">
        <h1>
            שלום,
            <?php echo htmlspecialchars($user['name']); ?>! 👋
        </h1>
        <p>
            <?php echo htmlspecialchars($user['company_name'] ?? 'החברה שלי'); ?>
        </p>
    </div>

    <!-- Stats Cards -->
    <div class="grid-dashboard mb-xl">
        <a href="/business/jobs.php" class="card text-center d-flex flex-column items-center p-lg">
            <div class="stat-number text-primary">
                <?php echo $jobsCount; ?>
            </div>
            <div class="stat-label">משרות פעילות</div>
        </a>

        <a href="/business/candidates.php" class="card text-center d-flex flex-column items-center p-lg">
            <div class="stat-number text-warning">
                <?php echo $pendingCount; ?>
            </div>
            <div class="stat-label">ממתינים לאישור</div>
        </a>

        <div class="card text-center d-flex flex-column items-center p-lg">
            <div class="stat-number text-success">
                <?php echo $matchedCount; ?>
            </div>
            <div class="stat-label">התאמות</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card p-lg mb-lg">
        <h3 class="mb-md">
            <i data-feather="zap"></i>
            פעולות מהירות
        </h3>

        <div class="d-flex flex-column gap-2">
            <a href="/business/job-edit.php" class="btn btn-primary w-full">
                <i data-feather="plus"></i>
                פרסום משרה חדשה
            </a>

            <a href="/business/candidates.php" class="btn btn-secondary w-full">
                <i data-feather="users"></i>
                צפייה במועמדים
            </a>

            <a href="/business/jobs.php" class="btn btn-secondary w-full">
                <i data-feather="briefcase"></i>
                ניהול משרות
            </a>
        </div>
    </div>

    <!-- Tips -->
    <div class="card p-lg bg-primary-light border-primary">
        <h3 class="text-primary-dark mb-sm">
            <i data-feather="info"></i>
            טיפ
        </h3>
        <p class="text-secondary mb-0">
            ככל שתוסיפו יותר פרטים למשרה (תמונה, תיאור מפורט, תגיות), כך תקבלו יותר מועמדים מתאימים!
        </p>
    </div>
</main>

<style>
    .mb-xl {
        margin-bottom: var(--spacing-xl);
    }

    .mb-lg {
        margin-bottom: var(--spacing-lg);
    }

    .mb-md {
        margin-bottom: var(--spacing-md);
    }

    .mb-sm {
        margin-bottom: var(--spacing-sm);
    }

    .mb-0 {
        margin-bottom: 0;
    }

    .p-lg {
        padding: var(--spacing-lg);
    }

    .flex-column {
        flex-direction: column;
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        line-height: 1;
        margin-bottom: var(--spacing-xs);
    }

    .stat-label {
        color: var(--text-muted);
        font-size: 0.875rem;
        font-weight: 500;
    }

    .text-warning {
        color: var(--warning);
    }

    .text-success {
        color: var(--success);
    }

    .bg-primary-light {
        background-color: var(--primary-light);
    }

    .border-primary {
        border: 1px solid var(--primary-glow);
    }

    .text-primary-dark {
        color: var(--primary-dark);
    }
</style>

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