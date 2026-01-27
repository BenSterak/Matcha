<?php
$pageTitle = 'הפרופיל שלי';
require_once 'includes/header.php';
requireAuth();

$user = getCurrentUser();
$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $field = trim($_POST['field'] ?? '');
    $salary = intval($_POST['salary'] ?? 0);
    $workModel = $_POST['work_model'] ?? '';

    if (empty($name)) {
        $error = 'שם הוא שדה חובה';
    } else {
        try {
            $stmt = $pdo->prepare("
                UPDATE users SET
                    name = ?,
                    bio = ?,
                    field = ?,
                    salary = ?,
                    workModel = ?
                WHERE id = ?
            ");
            $stmt->execute([$name, $bio, $field, $salary, $workModel, $_SESSION['user_id']]);

            $success = 'הפרופיל עודכן בהצלחה!';
            $user = getCurrentUser(); // Refresh user data
        } catch (PDOException $e) {
            $error = 'אירעה שגיאה בעדכון הפרופיל';
        }
    }
}
?>

<!-- Header -->
<header class="header">
    <a href="<?php echo $user['role'] === 'employer' ? '/business/dashboard.php' : '/feed.php'; ?>"
        class="header-icon-btn">
        <i data-feather="arrow-right"></i>
    </a>
    <h1 style="font-size: 1.125rem; font-weight: 600;">הפרופיל שלי</h1>
    <a href="logout.php" class="header-icon-btn" style="color: var(--error);">
        <i data-feather="log-out"></i>
    </a>
</header>

<!-- Profile Content -->
<main class="profile-page">
    <div class="profile-header">
        <img src="<?php echo htmlspecialchars($user['photo'] ?: 'https://ui-avatars.com/api/?name=' . urlencode($user['name']) . '&background=2ECC71&color=fff'); ?>"
            alt="<?php echo htmlspecialchars($user['name']); ?>" class="profile-avatar">
        <h2 class="profile-name">
            <?php echo htmlspecialchars($user['name']); ?>
        </h2>
        <p class="profile-email">
            <?php echo htmlspecialchars($user['email']); ?>
        </p>
    </div>

    <?php if ($success): ?>
        <div
            style="background: var(--success-light); color: var(--success); padding: var(--spacing-md); border-radius: var(--radius-md); margin-bottom: var(--spacing-md); text-align: center;">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="auth-error" style="margin-bottom: var(--spacing-md);">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="profile-section">
            <h3 class="profile-section-title">
                <i data-feather="user" style="width: 18px; height: 18px;"></i>
                פרטים אישיים
            </h3>

            <div class="form-group" style="margin-bottom: var(--spacing-md);">
                <label class="form-label">שם מלא</label>
                <input type="text" name="name" class="form-input" value="<?php echo htmlspecialchars($user['name']); ?>"
                    required style="padding-right: var(--spacing-md);">
            </div>

            <div class="form-group">
                <label class="form-label">על עצמי</label>
                <textarea name="bio" class="form-input" rows="3" placeholder="ספרו קצת על עצמכם..."
                    style="resize: none; padding-right: var(--spacing-md);"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
            </div>
        </div>

        <?php if ($user['role'] === 'jobseeker'): ?>
            <div class="profile-section">
                <h3 class="profile-section-title">
                    <i data-feather="briefcase" style="width: 18px; height: 18px;"></i>
                    העדפות עבודה
                </h3>

                <div class="form-group" style="margin-bottom: var(--spacing-md);">
                    <label class="form-label">תחום עיסוק</label>
                    <select name="field" class="form-input" style="padding-right: var(--spacing-md);">
                        <option value="">בחרו תחום</option>
                        <option value="tech" <?php echo $user['field'] === 'tech' ? 'selected' : ''; ?>>הייטק / טכנולוגיה
                        </option>
                        <option value="marketing" <?php echo $user['field'] === 'marketing' ? 'selected' : ''; ?>>שיווק
                            ופרסום</option>
                        <option value="sales" <?php echo $user['field'] === 'sales' ? 'selected' : ''; ?>>מכירות</option>
                        <option value="finance" <?php echo $user['field'] === 'finance' ? 'selected' : ''; ?>>פיננסים וכלכלה
                        </option>
                        <option value="design" <?php echo $user['field'] === 'design' ? 'selected' : ''; ?>>עיצוב</option>
                        <option value="hr" <?php echo $user['field'] === 'hr' ? 'selected' : ''; ?>>משאבי אנוש</option>
                        <option value="operations" <?php echo $user['field'] === 'operations' ? 'selected' : ''; ?>>תפעול
                            ולוגיסטיקה</option>
                        <option value="other" <?php echo $user['field'] === 'other' ? 'selected' : ''; ?>>אחר</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: var(--spacing-md);">
                    <label class="form-label">שכר מבוקש (ברוטו חודשי)</label>
                    <input type="number" name="salary" class="form-input" value="<?php echo intval($user['salary']); ?>"
                        min="0" placeholder="למשל: 15000" style="padding-right: var(--spacing-md);">
                </div>

                <div class="form-group">
                    <label class="form-label">מודל עבודה מועדף</label>
                    <select name="work_model" class="form-input" style="padding-right: var(--spacing-md);">
                        <option value="">בחרו העדפה</option>
                        <option value="office" <?php echo $user['workModel'] === 'office' ? 'selected' : ''; ?>>משרד</option>
                        <option value="remote" <?php echo $user['workModel'] === 'remote' ? 'selected' : ''; ?>>עבודה מהבית
                        </option>
                        <option value="hybrid" <?php echo $user['workModel'] === 'hybrid' ? 'selected' : ''; ?>>היברידי
                        </option>
                    </select>
                </div>
            </div>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary btn-full">
            <i data-feather="save" style="width: 18px; height: 18px;"></i>
            שמור שינויים
        </button>
    </form>

    <div style="margin-top: var(--spacing-xxl); text-align: center;">
        <a href="logout.php" class="btn btn-ghost" style="color: var(--error);">
            <i data-feather="log-out" style="width: 18px; height: 18px;"></i>
            התנתקות
        </a>
    </div>
</main>

<?php include 'includes/nav.php'; ?>
<?php include 'includes/footer.php'; ?>