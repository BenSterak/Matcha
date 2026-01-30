<?php
$pageTitle = isset($_GET['id']) ? 'עריכת משרה' : 'פרסום משרה';
require_once '../includes/header.php';
requireRole('employer');

$user = getCurrentUser();
$job = null;
$error = '';
$success = '';

// Load existing job for editing
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ? AND business_id = ?");
    $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
    $job = $stmt->fetch();

    if (!$job) {
        header('Location: /business/jobs.php');
        exit;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug logging
    file_put_contents('debug_post.log', date('Y-m-d H:i:s') . " - POST received: " . print_r($_POST, true) . "\n", FILE_APPEND);

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $salaryRange = trim($_POST['salary_range'] ?? '');
    $type = $_POST['type'] ?? '';
    $image = trim($_POST['image'] ?? '');

    if (empty($title) || empty($description)) {
        $error = 'כותרת ותיאור הם שדות חובה';
    } else {
        try {
            if ($job) {
                // Update existing job
                $stmt = $pdo->prepare("
                    UPDATE jobs SET
                        title = ?,
                        description = ?,
                        location = ?,
                        salaryRange = ?,
                        type = ?,
                        image = ?
                    WHERE id = ? AND business_id = ?
                ");
                $stmt->execute([
                    $title,
                    $description,
                    $location,
                    $salaryRange,
                    $type,
                    $image,
                    $job['id'],
                    $_SESSION['user_id']
                ]);

                $success = 'המשרה עודכנה בהצלחה!';

                // Refresh job data
                $stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ?");
                $stmt->execute([$job['id']]);
                $job = $stmt->fetch();
            } else {
                // Create new job
                $stmt = $pdo->prepare("
                    INSERT INTO jobs (title, company, description, location, salaryRange, type, image, business_id, createdAt)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([
                    $title,
                    $user['company_name'] ?? $user['name'],
                    $description,
                    $location,
                    $salaryRange,
                    $type,
                    $image,
                    $_SESSION['user_id']
                ]);

                header('Location: /business/jobs.php');
                exit;
            }
        } catch (PDOException $e) {
            $error = 'אירעה שגיאה. אנא נסו שוב.';
        }
    }
}
?>

<!-- Header -->
<header class="header">
    <a href="/business/jobs.php" class="header-icon-btn">
        <i data-feather="arrow-right"></i>
    </a>
    <h1 style="font-size: 1.125rem; font-weight: 600;">
        <?php echo $job ? 'עריכת משרה' : 'פרסום משרה חדשה'; ?>
    </h1>
    <div style="width: 44px;"></div>
</header>

<main class="profile-page">
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

    <form method="POST" id="jobForm">
        <div class="profile-section">
            <h3 class="profile-section-title">
                <i data-feather="briefcase" style="width: 18px; height: 18px;"></i>
                פרטי המשרה
            </h3>

            <div class="form-group" style="margin-bottom: var(--spacing-md);">
                <label class="form-label">כותרת המשרה *</label>
                <input type="text" name="title" class="form-input" id="inputTitle"
                    value="<?php echo htmlspecialchars($_POST['title'] ?? $job['title'] ?? ''); ?>"
                    placeholder="למשל: מפתח/ת Full Stack" required style="padding-right: var(--spacing-md);">
            </div>

            <div class="form-group" style="margin-bottom: var(--spacing-md);">
                <label class="form-label">תיאור התפקיד *</label>
                <textarea name="description" class="form-input" id="inputDescription" rows="5"
                    placeholder="תארו את התפקיד, הדרישות והיתרונות..." required
                    style="resize: none; padding-right: var(--spacing-md);"><?php echo htmlspecialchars($_POST['description'] ?? $job['description'] ?? ''); ?></textarea>
            </div>

            <div class="form-group" style="margin-bottom: var(--spacing-md);">
                <label class="form-label">מיקום</label>
                <div class="input-wrapper">
                    <i data-feather="map-pin"></i>
                    <input type="text" name="location" class="form-input"
                        value="<?php echo htmlspecialchars($_POST['location'] ?? $job['location'] ?? ''); ?>"
                        placeholder="למשל: תל אביב">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: var(--spacing-md);">
                <label class="form-label">טווח שכר</label>
                <div class="input-wrapper">
                    <i data-feather="dollar-sign"></i>
                    <input type="text" name="salary_range" class="form-input"
                        value="<?php echo htmlspecialchars($_POST['salary_range'] ?? $job['salaryRange'] ?? ''); ?>"
                        placeholder="למשל: 15,000-20,000 ₪">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: var(--spacing-md);">
                <label class="form-label">סוג משרה</label>
                <select name="type" class="form-input" style="padding-right: var(--spacing-md);">
                    <option value="">בחרו סוג</option>
                    <?php
                    $currentType = $_POST['type'] ?? $job['type'] ?? '';
                    $types = ['משרה מלאה', 'משרה חלקית', 'פרילנס', 'היברידי', 'עבודה מהבית', 'עבודה פיזית', 'עבודת שטח'];
                    foreach ($types as $t) {
                        $selected = $currentType === $t ? 'selected' : '';
                        echo "<option value=\"$t\" $selected>$t</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">תמונה (אופציונלי)</label>
                <div
                    style="display: flex; align-items: center; gap: var(--spacing-md); margin-bottom: var(--spacing-sm);">
                    <?php
                    $previewImage = $_POST['image'] ?? $job['image'] ?? '';
                    $defaultImage = 'https://ui-avatars.com/api/?name=' . urlencode($user['company_name'] ?? $user['name']) . '&size=100&background=22C55E&color=fff&bold=true';
                    ?>
                    <img id="imagePreview" src="<?php echo htmlspecialchars($previewImage ?: $defaultImage); ?>"
                        alt="תצוגה מקדימה"
                        style="width: 60px; height: 60px; border-radius: var(--radius-md); object-fit: cover;">
                    <p style="font-size: 0.875rem; color: var(--text-muted);">תמונה אוטומטית תיווצר אם לא תועלה תמונה
                    </p>
                </div>

                <!-- File upload -->
                <div style="margin-bottom: var(--spacing-sm);">

                    <!-- File upload -->
                    <div id="uploadSection">
                        <input type="file" id="jobImageFile" accept="image/jpeg,image/png,image/gif,image/webp"
                            style="display: none;" onchange="uploadJobImage(this)">
                        <button type="button" class="btn btn-secondary btn-full"
                            onclick="document.getElementById('jobImageFile').click()">
                            <i data-feather="image" style="width: 18px; height: 18px;"></i>
                            בחרו תמונה מהמכשיר
                        </button>
                        <p
                            style="font-size: 0.75rem; color: var(--text-light); margin-top: var(--spacing-xs); text-align: center;">
                            JPEG, PNG, GIF, WebP - עד 5MB
                        </p>
                        <div id="uploadProgress" style="display: none; margin-top: var(--spacing-sm);">
                            <div
                                style="background: var(--border); border-radius: var(--radius-full); height: 4px; overflow: hidden;">
                                <div id="progressBar"
                                    style="background: var(--primary); height: 100%; width: 0%; transition: width 0.3s;">
                                </div>
                            </div>
                            <p id="uploadStatus"
                                style="font-size: 0.75rem; color: var(--text-muted); margin-top: var(--spacing-xs); text-align: center;">
                                מעלה...</p>
                        </div>
                    </div>



                    <!-- Hidden input for form submission -->
                    <input type="hidden" name="image" id="imageHidden"
                        value="<?php echo htmlspecialchars($previewImage); ?>">
                </div>
            </div>

            <button type="button" class="btn btn-primary btn-full" onclick="submitJobForm(this)">
                <i data-feather="<?php echo $job ? 'save' : 'plus'; ?>" style="width: 18px; height: 18px;"></i>
                <?php echo $job ? 'שמור שינויים' : 'פרסום משרה'; ?>
            </button>

            <?php if ($job): ?>
                <button type="button" class="btn btn-ghost btn-full"
                    style="color: var(--error); margin-top: var(--spacing-md);" onclick="deleteJob()">
                    <i data-feather="trash-2" style="width: 18px; height: 18px;"></i>
                    מחיקת משרה
                </button>
            <?php endif; ?>
    </form>
</main>

<?php if ($job): ?>
    <script>
        function deleteJob() {
            if (confirm('האם אתם בטוחים שברצונכם למחוק את המשרה? פעולה זו לא ניתנת לביטול.')) {
                fetch('/api/jobs.php?action=delete&id=<?php echo $job['id']; ?>', {
                    method: 'DELETE'
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = '/business/jobs.php';
                        } else {
                            alert('אירעה שגיאה במחיקת המשרה');
                        }
                    });
            }
        }
    </script>
<?php endif; ?>

<script>
    function submitJobForm(btn) {
        // Basic validation
        const title = document.getElementById('inputTitle').value.trim();
        const description = document.getElementById('inputDescription').value.trim();

        if (!title || !description) {
            alert('אנא מלאו את שדות החובה (כותרת ותיאור)');
            return;
        }

        // Visual feedback
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="loading-dots">שומר...</span>';

        // Force submit
        document.getElementById('jobForm').submit();
    }


    const defaultImage = '<?php echo $defaultImage; ?>';

    function updateImagePreview(url) {
        const preview = document.getElementById('imagePreview');
        if (url && url.trim()) {
            preview.src = url;
            preview.onerror = function () { this.src = defaultImage; };
        } else {
            preview.src = defaultImage;
        }
        // Update hidden field
        document.getElementById('imageHidden').value = url;
    }



    async function uploadJobImage(input) {
        if (!input.files || !input.files[0]) return;

        const file = input.files[0];
        const formData = new FormData();
        formData.append('image', file);

        const progressDiv = document.getElementById('uploadProgress');
        const progressBar = document.getElementById('progressBar');
        const uploadStatus = document.getElementById('uploadStatus');

        progressDiv.style.display = 'block';
        progressBar.style.width = '0%';
        progressBar.style.background = 'var(--primary)';
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

            const response = await fetch('/api/upload.php?action=job', {
                method: 'POST',
                body: formData
            });

            clearInterval(progressInterval);
            progressBar.style.width = '100%';

            const data = await response.json();

            if (data.success) {
                uploadStatus.textContent = 'התמונה הועלתה בהצלחה!';
                uploadStatus.style.color = 'var(--success)';
                document.getElementById('imagePreview').src = data.url + '?t=' + Date.now();

                document.getElementById('imageHidden').value = data.url;

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
</script>

<?php include '../includes/nav.php'; ?>
<?php include '../includes/footer.php'; ?>