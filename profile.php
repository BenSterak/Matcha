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
    $photo = trim($_POST['photo'] ?? '');
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
                    photo = ?,
                    field = ?,
                    salary = ?,
                    workModel = ?
                WHERE id = ?
            ");
            $stmt->execute([$name, $bio, $photo, $field, $salary, $workModel, $_SESSION['user_id']]);

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

            <div class="form-group" style="margin-bottom: var(--spacing-md);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-sm);">
                    <label class="form-label" style="margin: 0;">על עצמי</label>
                    <button type="button" id="magicBtn" class="btn btn-sm" onclick="enhanceBio()"
                            style="background: linear-gradient(135deg, #8B5CF6, #EC4899); color: white; font-size: 0.75rem; padding: 6px 12px;">
                        <i data-feather="sparkles" style="width: 14px; height: 14px;"></i>
                        שדרג עם AI
                    </button>
                </div>
                <textarea name="bio" id="bioField" class="form-input" rows="3" placeholder="ספרו קצת על עצמכם..."
                    style="resize: none; padding-right: var(--spacing-md);"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                <p id="aiStatus" style="font-size: 0.75rem; color: var(--text-light); margin-top: var(--spacing-xs); display: none;"></p>
            </div>

            <div class="form-group">
                <label class="form-label">תמונת פרופיל (אופציונלי)</label>
                <div style="display: flex; align-items: center; gap: var(--spacing-md); margin-bottom: var(--spacing-sm);">
                    <?php
                    $defaultPhoto = 'https://ui-avatars.com/api/?name=' . urlencode($user['name']) . '&size=100&background=22C55E&color=fff&bold=true';
                    ?>
                    <img id="photoPreview" src="<?php echo htmlspecialchars($user['photo'] ?: $defaultPhoto); ?>"
                         alt="תמונת פרופיל"
                         style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover;">
                    <div>
                        <p style="font-size: 0.875rem; color: var(--text-muted);">תמונה אוטומטית תיווצר מהשם אם לא תוזן</p>
                    </div>
                </div>

                <!-- Photo upload tabs -->
                <div style="display: flex; gap: var(--spacing-sm); margin-bottom: var(--spacing-sm);">
                    <button type="button" id="tabUpload" class="btn btn-sm btn-primary" onclick="showUploadTab()">
                        <i data-feather="upload" style="width: 14px; height: 14px;"></i>
                        העלאת קובץ
                    </button>
                    <button type="button" id="tabUrl" class="btn btn-sm btn-secondary" onclick="showUrlTab()">
                        <i data-feather="link" style="width: 14px; height: 14px;"></i>
                        קישור לתמונה
                    </button>
                </div>

                <!-- File upload -->
                <div id="uploadSection">
                    <input type="file" id="avatarFile" accept="image/jpeg,image/png,image/gif,image/webp"
                           style="display: none;" onchange="uploadAvatar(this)">
                    <button type="button" class="btn btn-secondary btn-full" onclick="document.getElementById('avatarFile').click()">
                        <i data-feather="image" style="width: 18px; height: 18px;"></i>
                        בחרו תמונה מהמכשיר
                    </button>
                    <p style="font-size: 0.75rem; color: var(--text-light); margin-top: var(--spacing-xs); text-align: center;">
                        JPEG, PNG, GIF, WebP - עד 5MB
                    </p>
                    <div id="uploadProgress" style="display: none; margin-top: var(--spacing-sm);">
                        <div style="background: var(--border); border-radius: var(--radius-full); height: 4px; overflow: hidden;">
                            <div id="progressBar" style="background: var(--primary); height: 100%; width: 0%; transition: width 0.3s;"></div>
                        </div>
                        <p id="uploadStatus" style="font-size: 0.75rem; color: var(--text-muted); margin-top: var(--spacing-xs); text-align: center;">מעלה...</p>
                    </div>
                </div>

                <!-- URL input (hidden by default) -->
                <div id="urlSection" style="display: none;">
                    <div class="input-wrapper">
                        <i data-feather="link"></i>
                        <input type="url" name="photo" id="photoUrl" class="form-input"
                            value="<?php echo htmlspecialchars($user['photo'] ?? ''); ?>"
                            placeholder="קישור לתמונה (לא חובה)"
                            oninput="updatePhotoPreview(this.value)">
                    </div>
                </div>
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

<script>
    const defaultPhoto = '<?php echo $defaultPhoto; ?>';

    function updatePhotoPreview(url) {
        const preview = document.getElementById('photoPreview');
        if (url && url.trim()) {
            preview.src = url;
            preview.onerror = function() { this.src = defaultPhoto; };
        } else {
            preview.src = defaultPhoto;
        }
    }

    function showUploadTab() {
        document.getElementById('uploadSection').style.display = 'block';
        document.getElementById('urlSection').style.display = 'none';
        document.getElementById('tabUpload').className = 'btn btn-sm btn-primary';
        document.getElementById('tabUrl').className = 'btn btn-sm btn-secondary';
    }

    function showUrlTab() {
        document.getElementById('uploadSection').style.display = 'none';
        document.getElementById('urlSection').style.display = 'block';
        document.getElementById('tabUpload').className = 'btn btn-sm btn-secondary';
        document.getElementById('tabUrl').className = 'btn btn-sm btn-primary';
    }

    async function uploadAvatar(input) {
        if (!input.files || !input.files[0]) return;

        const file = input.files[0];
        const formData = new FormData();
        formData.append('avatar', file);

        const progressDiv = document.getElementById('uploadProgress');
        const progressBar = document.getElementById('progressBar');
        const uploadStatus = document.getElementById('uploadStatus');

        progressDiv.style.display = 'block';
        progressBar.style.width = '0%';
        uploadStatus.textContent = 'מעלה...';
        uploadStatus.style.color = 'var(--text-muted)';

        try {
            // Simulate progress
            let progress = 0;
            const progressInterval = setInterval(() => {
                progress += 10;
                if (progress <= 90) {
                    progressBar.style.width = progress + '%';
                }
            }, 100);

            const response = await fetch('/api/upload.php?action=avatar', {
                method: 'POST',
                body: formData
            });

            clearInterval(progressInterval);
            progressBar.style.width = '100%';

            const data = await response.json();

            if (data.success) {
                uploadStatus.textContent = 'התמונה הועלתה בהצלחה!';
                uploadStatus.style.color = 'var(--success)';
                document.getElementById('photoPreview').src = data.url + '?t=' + Date.now();
                document.getElementById('photoUrl').value = data.url;

                setTimeout(() => {
                    progressDiv.style.display = 'none';
                }, 2000);
            } else {
                uploadStatus.textContent = data.error || 'שגיאה בהעלאה';
                uploadStatus.style.color = 'var(--error)';
                progressBar.style.background = 'var(--error)';
            }
        } catch (error) {
            uploadStatus.textContent = 'שגיאה בהעלאה. נסו שוב.';
            uploadStatus.style.color = 'var(--error)';
            progressBar.style.background = 'var(--error)';
        }

        input.value = '';
    }

    async function enhanceBio() {
        const bioField = document.getElementById('bioField');
        const magicBtn = document.getElementById('magicBtn');
        const aiStatus = document.getElementById('aiStatus');
        const fieldSelect = document.querySelector('select[name="field"]');
        const nameInput = document.querySelector('input[name="name"]');

        const currentBio = bioField.value.trim();
        const field = fieldSelect ? fieldSelect.value : '';
        const name = nameInput ? nameInput.value : '';

        // Disable button and show loading
        magicBtn.disabled = true;
        magicBtn.innerHTML = '<span class="loading-dots">מייצר...</span>';
        aiStatus.style.display = 'block';
        aiStatus.textContent = 'ה-AI עובד על השדרוג...';
        aiStatus.style.color = 'var(--primary)';

        try {
            const response = await fetch('/api/ai.php?action=enhance_bio', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ bio: currentBio, field, name })
            });

            const data = await response.json();

            if (data.success) {
                bioField.value = data.enhanced_bio;
                aiStatus.textContent = 'הטקסט שודרג בהצלחה! אל תשכחו לשמור.';
                aiStatus.style.color = 'var(--success)';

                // Highlight the textarea briefly
                bioField.style.borderColor = 'var(--primary)';
                bioField.style.boxShadow = '0 0 0 3px var(--primary-glow)';
                setTimeout(() => {
                    bioField.style.borderColor = '';
                    bioField.style.boxShadow = '';
                }, 2000);
            } else {
                aiStatus.textContent = data.error || 'שגיאה בשדרוג הטקסט';
                aiStatus.style.color = 'var(--error)';
            }
        } catch (error) {
            aiStatus.textContent = 'שגיאה בחיבור לשרת';
            aiStatus.style.color = 'var(--error)';
        }

        // Re-enable button
        magicBtn.disabled = false;
        magicBtn.innerHTML = '<i data-feather="sparkles" style="width: 14px; height: 14px;"></i> שדרג עם AI';
        feather.replace();

        // Hide status after 5 seconds
        setTimeout(() => {
            aiStatus.style.display = 'none';
        }, 5000);
    }
</script>

<?php include 'includes/nav.php'; ?>
<?php include 'includes/footer.php'; ?>