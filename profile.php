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
            // Dynamic update based on role
            if ($user['role'] === 'employer') {
                $companyDetails = [
                    'company_name' => $_POST['company_name'] ?? $user['company_name'],
                    'company_website' => $_POST['company_website'] ?? '',
                    'company_location' => $_POST['company_location'] ?? '',
                    'company_size' => $_POST['company_size'] ?? '',
                    'company_cover' => $_POST['company_cover'] ?? ''
                ];

                $sql = "UPDATE users SET name=?, bio=?, photo=?, field=?, salary=?, workModel=?, 
                        company_name=?, company_website=?, company_location=?, company_size=?, company_cover=? 
                        WHERE id=?";
                $params = [
                    $name,
                    $bio,
                    $photo,
                    $field,
                    $salary,
                    $workModel,
                    $companyDetails['company_name'],
                    $companyDetails['company_website'],
                    $companyDetails['company_location'],
                    $companyDetails['company_size'],
                    $companyDetails['company_cover'],
                    $_SESSION['user_id']
                ];
            } else {
                $jobSeekerDetails = [
                    'resume_file' => $_POST['resume_file'] ?? ($user['resume_file'] ?? ''),
                    'portfolio_url' => $_POST['portfolio_url'] ?? ''
                ];

                $sql = "UPDATE users SET name=?, bio=?, photo=?, field=?, salary=?, workModel=?,
                        resume_file=?, portfolio_url=?
                        WHERE id=?";
                $params = [
                    $name,
                    $bio,
                    $photo,
                    $field,
                    $salary,
                    $workModel,
                    $jobSeekerDetails['resume_file'],
                    $jobSeekerDetails['portfolio_url'],
                    $_SESSION['user_id']
                ];
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

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

    <?php
    // Calculate profile strength
    $profileFields = [];
    if ($user['role'] === 'employer') {
        $profileFields = [
            'name' => !empty($user['name']),
            'bio' => !empty($user['bio']),
            'photo' => !empty($user['photo']),
            'company_name' => !empty($user['company_name']),
            'company_website' => !empty($user['company_website']),
            'company_location' => !empty($user['company_location']),
            'company_size' => !empty($user['company_size']),
            'company_cover' => !empty($user['company_cover']),
        ];
    } else {
        $profileFields = [
            'name' => !empty($user['name']),
            'bio' => !empty($user['bio']),
            'photo' => !empty($user['photo']),
            'field' => !empty($user['field']),
            'salary' => !empty($user['salary']),
            'workModel' => !empty($user['workModel']),
            'resume_file' => !empty($user['resume_file']),
            'portfolio_url' => !empty($user['portfolio_url']),
        ];
    }
    $filledCount = count(array_filter($profileFields));
    $totalCount = count($profileFields);
    $profileStrength = round(($filledCount / $totalCount) * 100);

    $strengthColor = $profileStrength >= 80 ? 'var(--success)' : ($profileStrength >= 50 ? 'var(--warning)' : 'var(--error)');
    $strengthLabel = $profileStrength >= 80 ? 'פרופיל חזק!' : ($profileStrength >= 50 ? 'כמעט שם...' : 'השלימו את הפרופיל');
    ?>

    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: var(--spacing-md); margin-bottom: var(--spacing-lg);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-sm);">
            <span style="font-size: 0.875rem; font-weight: 600; color: var(--secondary);">חוזק הפרופיל</span>
            <span style="font-size: 0.875rem; font-weight: 700; color: <?php echo $strengthColor; ?>;"><?php echo $profileStrength; ?>%</span>
        </div>
        <div style="background: var(--border); border-radius: var(--radius-full); height: 8px; overflow: hidden;">
            <div style="background: <?php echo $strengthColor; ?>; height: 100%; width: <?php echo $profileStrength; ?>%; border-radius: var(--radius-full); transition: width 0.5s ease;"></div>
        </div>
        <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: var(--spacing-xs);"><?php echo $strengthLabel; ?> (<?php echo $filledCount; ?>/<?php echo $totalCount; ?> שדות)</p>
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

    <form method="POST" id="profileForm">
        <div class="profile-section">
            <h3 class="profile-section-title">
                <i data-feather="user" style="width: 18px; height: 18px;"></i>
                פרטים אישיים
            </h3>

            <!-- Company Details (Employer Only) -->
            <?php if ($user['role'] === 'employer'): ?>
                <div class="form-group" style="margin-bottom: var(--spacing-md);">
                    <label class="form-label">שם החברה</label>
                    <input type="text" name="company_name" class="form-input"
                        value="<?php echo htmlspecialchars($user['company_name'] ?? ''); ?>" required placeholder="שם העסק">
                </div>

                <div class="form-groups-row" style="display: flex; gap: var(--spacing-md);">
                    <div class="form-group" style="flex: 1; margin-bottom: var(--spacing-md);">
                        <label class="form-label">אתר החברה</label>
                        <div class="input-wrapper">
                            <i data-feather="globe"></i>
                            <input type="url" name="company_website" class="form-input"
                                value="<?php echo htmlspecialchars($user['company_website'] ?? ''); ?>"
                                placeholder="https://example.com">
                        </div>
                    </div>

                    <div class="form-group" style="flex: 1; margin-bottom: var(--spacing-md);">
                        <label class="form-label">גודל חברה</label>
                        <select name="company_size" class="form-input">
                            <option value="">בחרו גודל</option>
                            <?php
                            $sizes = ['1-10', '11-50', '51-200', '201-500', '500+'];
                            $currentSize = $user['company_size'] ?? '';
                            foreach ($sizes as $s) {
                                $sel = $currentSize === $s ? 'selected' : '';
                                echo "<option value=\"$s\" $sel>$s עובדים</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: var(--spacing-md);">
                    <label class="form-label">מיקום המשרדים</label>
                    <div class="input-wrapper">
                        <i data-feather="map-pin"></i>
                        <input type="text" name="company_location" class="form-input"
                            value="<?php echo htmlspecialchars($user['company_location'] ?? ''); ?>"
                            placeholder="תל אביב, הרצליה...">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">תמונת קאבר / משרד</label>
                    <?php
                    $cover = $user['company_cover'] ?? '';
                    $hasCover = !empty($cover);
                    ?>
                    <div id="coverPreviewContainer"
                        style="margin-bottom: var(--spacing-sm); <?php echo $hasCover ? '' : 'display:none;'; ?>">
                        <img id="coverPreview" src="<?php echo htmlspecialchars($cover); ?>"
                            style="width: 100%; height: 160px; object-fit: cover; border-radius: var(--radius-md);">
                        <button type="button" onclick="removeCover()" class="btn btn-sm btn-ghost"
                            style="color: var(--error); margin-top: 5px;">
                            <i data-feather="trash-2" style="width: 14px;"></i> הסר תמונה
                        </button>
                    </div>

                    <input type="hidden" name="company_cover" id="companyCoverInput"
                        value="<?php echo htmlspecialchars($cover); ?>">

                    <button type="button" class="btn btn-secondary btn-full"
                        onclick="document.getElementById('coverFile').click()">
                        <i data-feather="image"></i> בחרו תמונת נושא
                    </button>
                    <input type="file" id="coverFile" accept="image/*" style="display: none;" onchange="uploadCover(this)">
                </div>

                <!-- Hidden name field for compatibility -->
                <input type="hidden" name="name" value="<?php echo htmlspecialchars($user['name']); ?>">

            <?php else: ?>
                <!-- Job Seeker Fields -->
                <div class="form-group" style="margin-bottom: var(--spacing-md);">
                    <label class="form-label">שם מלא</label>
                    <input type="text" name="name" class="form-input" value="<?php echo htmlspecialchars($user['name']); ?>"
                        required style="padding-right: var(--spacing-md);">
                </div>

                <div class="form-group" style="margin-bottom: var(--spacing-md);">
                    <label class="form-label">תיק עבודות / אתר</label>
                    <div class="input-wrapper">
                        <i data-feather="globe"></i>
                        <input type="url" name="portfolio_url" class="form-input"
                            value="<?php echo htmlspecialchars($user['portfolio_url'] ?? ''); ?>"
                            placeholder="https://myportfolio.com">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: var(--spacing-md);">
                    <label class="form-label">קורות חיים (PDF / Word)</label>
                    <?php
                    $resume = $user['resume_file'] ?? '';
                    $hasResume = !empty($resume);
                    ?>
                    <div id="resumePreviewContainer"
                        style="margin-bottom: var(--spacing-sm); display: flex; align-items: center; gap: 10px; <?php echo $hasResume ? '' : 'display:none;'; ?>">
                        <div
                            style="background: var(--surface-hover); padding: 8px 12px; border-radius: var(--radius-md); display: flex; align-items: center; gap: 8px; flex: 1;">
                            <i data-feather="file-text" style="color: var(--primary);"></i>
                            <a id="resumeLink" href="<?php echo htmlspecialchars($resume); ?>" target="_blank"
                                style="font-size: 0.875rem; color: var(--text-main); text-decoration: none; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                צפייה בקובץ הקיים
                            </a>
                        </div>
                        <button type="button" onclick="removeResume()" class="btn btn-sm btn-ghost"
                            style="color: var(--error);">
                            <i data-feather="trash-2"></i>
                        </button>
                    </div>

                    <input type="hidden" name="resume_file" id="resumeFileInput"
                        value="<?php echo htmlspecialchars($resume); ?>">

                    <button type="button" class="btn btn-secondary btn-full"
                        onclick="document.getElementById('cvFile').click()">
                        <i data-feather="upload-cloud"></i> העלאת קורות חיים
                    </button>
                    <input type="file" id="cvFile" accept=".pdf,.doc,.docx,application/pdf,application/msword"
                        style="display: none;" onchange="uploadCV(this)">
                </div>
            <?php endif; ?>

            <div class="form-group" style="margin-bottom: var(--spacing-md);">
                <div
                    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-sm);">
                    <label class="form-label" style="margin: 0;">על עצמי</label>
                    <button type="button" id="magicBtn" class="btn btn-sm" onclick="enhanceBio()"
                        style="background: linear-gradient(135deg, #8B5CF6, #EC4899); color: white; font-size: 0.75rem; padding: 6px 12px;">
                        <i data-feather="sparkles" style="width: 14px; height: 14px;"></i>
                        שדרג עם AI
                    </button>
                </div>
                <textarea name="bio" id="bioField" class="form-input" rows="3" placeholder="ספרו קצת על עצמכם..."
                    style="resize: none; padding-right: var(--spacing-md);"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                <p id="aiStatus"
                    style="font-size: 0.75rem; color: var(--text-light); margin-top: var(--spacing-xs); display: none;">
                </p>
            </div>

            <div class="form-group">
                <label class="form-label">תמונת פרופיל (אופציונלי)</label>
                <div
                    style="display: flex; align-items: center; gap: var(--spacing-md); margin-bottom: var(--spacing-sm);">
                    <?php
                    $defaultPhoto = 'https://ui-avatars.com/api/?name=' . urlencode($user['name']) . '&size=100&background=22C55E&color=fff&bold=true';
                    ?>
                    <img id="photoPreview" src="<?php echo htmlspecialchars($user['photo'] ?: $defaultPhoto); ?>"
                        alt="תמונת פרופיל" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover;">
                    <input type="hidden" name="photo" id="photoUrl"
                        value="<?php echo htmlspecialchars($user['photo'] ?? ''); ?>">
                    <div>
                        <p style="font-size: 0.875rem; color: var(--text-muted);">תמונה אוטומטית תיווצר מהשם אם לא תוזן
                        </p>
                    </div>
                </div>

                <!-- Photo upload -->
                <div style="margin-bottom: var(--spacing-sm);">

                    <!-- File upload -->
                    <div id="uploadSection">
                        <input type="file" id="avatarFile" accept="image/jpeg,image/png,image/gif,image/webp"
                            style="display: none;" onchange="uploadAvatar(this)">
                        <button type="button" class="btn btn-secondary btn-full"
                            onclick="document.getElementById('avatarFile').click()">
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
                            <option value="finance" <?php echo $user['field'] === 'finance' ? 'selected' : ''; ?>>פיננסים
                                וכלכלה
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
                            <option value="office" <?php echo $user['workModel'] === 'office' ? 'selected' : ''; ?>>משרד
                            </option>
                            <option value="remote" <?php echo $user['workModel'] === 'remote' ? 'selected' : ''; ?>>עבודה
                                מהבית
                            </option>
                            <option value="hybrid" <?php echo $user['workModel'] === 'hybrid' ? 'selected' : ''; ?>>היברידי
                            </option>
                            <option value="physical" <?php echo $user['workModel'] === 'physical' ? 'selected' : ''; ?>>עבודה
                                פיזית</option>
                            <option value="field" <?php echo $user['workModel'] === 'field' ? 'selected' : ''; ?>>עבודת שטח
                            </option>
                            </option>
                        </select>
                    </div>
                </div>
            <?php endif; ?>

            <button type="button" class="btn btn-primary btn-full" onclick="submitProfileForm(this)">
                <i data-feather="save" style="width: 18px; height: 18px;"></i>
                שמור שינויים
            </button>
    </form>

    <!-- Email Notifications Toggle -->
    <div class="profile-section" style="margin-top: var(--spacing-lg);">
        <h3 class="profile-section-title">
            <i data-feather="bell" style="width: 18px; height: 18px;"></i>
            התראות
        </h3>
        <div style="display: flex; justify-content: space-between; align-items: center; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: var(--spacing-md);">
            <div>
                <p style="font-weight: 500; margin: 0;">התראות במייל</p>
                <p style="font-size: 0.8rem; color: var(--text-muted); margin: 4px 0 0;">קבלת עדכונים על התאמות והודעות חדשות</p>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" id="emailToggle" <?php echo !empty($user['email_notifications']) ? 'checked' : ''; ?>
                    onchange="toggleEmailNotifications(this.checked)">
                <span class="toggle-slider"></span>
            </label>
        </div>
    </div>

    <style>
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 48px;
            height: 28px;
            cursor: pointer;
            flex-shrink: 0;
        }
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .toggle-slider {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: var(--border);
            border-radius: 28px;
            transition: 0.3s;
        }
        .toggle-slider::before {
            content: "";
            position: absolute;
            height: 22px;
            width: 22px;
            right: 3px;
            bottom: 3px;
            background: white;
            border-radius: 50%;
            transition: 0.3s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        .toggle-switch input:checked + .toggle-slider {
            background: var(--primary);
        }
        .toggle-switch input:checked + .toggle-slider::before {
            transform: translateX(-20px);
        }
    </style>

    <script>
        function submitProfileForm(btn) {
            // Basic validation
            const nameInput = document.querySelector('input[name="name"]');
            const name = nameInput ? nameInput.value.trim() : '';

            if (!name) {
                alert('שם מלא הוא שדה חובה');
                return;
            }

            // Visual feedback
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="loading-dots">שומר...</span>';

            // Force submit
            document.getElementById('profileForm').submit();
        }
        function removeResume() {
            document.getElementById('resumeFileInput').value = '';
            document.getElementById('resumePreviewContainer').style.display = 'none';
        }

        async function uploadCV(input) {
            if (!input.files || !input.files[0]) return;

            const file = input.files[0];
            const formData = new FormData();
            formData.append('cv', file);

            // Simple feedback
            const btn = input.previousElementSibling;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="loading-dots">מעלה...</span>';

            try {
                const response = await fetch('/api/upload.php?action=cv', {
                    method: 'POST',
                    body: formData
                });

                const responseText = await response.text();
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (e) {
                    console.error('Server response:', responseText);
                    throw new Error('תגובת שרת לא תקינה');
                }

                if (data.success) {
                    document.getElementById('resumeFileInput').value = data.url;
                    document.getElementById('resumeLink').href = data.url;
                    document.getElementById('resumePreviewContainer').style.display = 'flex';
                    alert('קורות החיים הועלו בהצלחה! אל תשכחו לשמור.');
                } else {
                    alert(data.error || 'שגיאה בהעלאת קובץ');
                }
            } catch (error) {
                console.error('Upload error:', error);
                alert('שגיאה בהעלאה. נסו שוב.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
                input.value = '';
            }
        }
    </script>

    <div style="margin-top: var(--spacing-xxl); text-align: center; display: flex; flex-direction: column; align-items: center; gap: var(--spacing-md);">
        <a href="logout.php" class="btn btn-ghost" style="color: var(--error);">
            <i data-feather="log-out" style="width: 18px; height: 18px;"></i>
            התנתקות
        </a>
        <button type="button" onclick="deleteAccount()" class="btn btn-ghost" style="color: var(--text-light); font-size: 0.8rem;">
            <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
            מחיקת חשבון
        </button>
    </div>
</main>

<script>
    const defaultPhoto = '<?php echo $defaultPhoto; ?>';

    function updatePhotoPreview(url) {
        const preview = document.getElementById('photoPreview');
        if (url && url.trim()) {
            preview.src = url;
            preview.onerror = function () { this.src = defaultPhoto; };
        } else {
            preview.src = defaultPhoto;
        }
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
        progressBar.style.background = 'var(--primary)';

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

            const responseText = await response.text();
            let data;
            try {
                data = JSON.parse(responseText);
            } catch (e) {
                console.error('Server response:', responseText);
                throw new Error('תגובת שרת לא תקינה');
            }

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
            console.error('Upload error:', error);
            uploadStatus.textContent = error.message || 'שגיאה בהעלאה. נסו שוב.';
            uploadStatus.style.color = 'var(--error)';
            progressBar.style.background = 'var(--error)';
        }

        input.value = '';
    }

    async function uploadCover(input) {
        if (!input.files || !input.files[0]) return;

        const file = input.files[0];
        const formData = new FormData();
        formData.append('cover', file);

        // Simple feedback for cover upload
        const btn = input.previousElementSibling; // The button that triggered this
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="loading-dots">מעלה...</span>';

        try {
            const response = await fetch('/api/upload.php?action=cover', {
                method: 'POST',
                body: formData
            });

            const responseText = await response.text();
            let data;
            try {
                data = JSON.parse(responseText);
            } catch (e) {
                console.error('Server response:', responseText);
                throw new Error('תגובת שרת לא תקינה');
            }

            if (data.success) {
                document.getElementById('companyCoverInput').value = data.url;
                document.getElementById('coverPreview').src = data.url;
                document.getElementById('coverPreviewContainer').style.display = 'block';
            } else {
                alert(data.error || 'שגיאה בהעלאת תמונת נושא');
            }
        } catch (error) {
            console.error('Upload error:', error);
            alert('שגיאה בהעלאה. נסו שוב.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
            input.value = '';
        }
    }

    function removeCover() {
        document.getElementById('companyCoverInput').value = '';
        document.getElementById('coverPreview').src = '';
        document.getElementById('coverPreviewContainer').style.display = 'none';
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

    async function toggleEmailNotifications(enabled) {
        try {
            const response = await fetch('/api/auth.php?action=toggle_email', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ enabled: enabled })
            });
            const data = await response.json();
            if (data.success) {
                Matcha.showToast(enabled ? 'התראות מייל הופעלו' : 'התראות מייל כובו', 'success');
            } else {
                // Revert toggle
                document.getElementById('emailToggle').checked = !enabled;
                Matcha.showToast(data.error || 'שגיאה', 'error');
            }
        } catch (error) {
            document.getElementById('emailToggle').checked = !enabled;
            Matcha.showToast('שגיאה בחיבור לשרת', 'error');
        }
    }

    async function deleteAccount() {
        if (!confirm('האם אתם בטוחים שברצונכם למחוק את החשבון? פעולה זו בלתי הפיכה.')) {
            return;
        }
        if (!confirm('שימו לב: כל המידע, ההתאמות וההודעות שלכם יימחקו לצמיתות. להמשיך?')) {
            return;
        }

        try {
            const response = await fetch('/api/auth.php?action=delete_account', {
                method: 'POST'
            });
            const data = await response.json();

            if (data.success) {
                alert('החשבון נמחק בהצלחה. להתראות!');
                window.location.href = '/index.php';
            } else {
                alert(data.error || 'שגיאה במחיקת החשבון');
            }
        } catch (error) {
            alert('שגיאה בחיבור לשרת');
        }
    }
</script>

<?php include 'includes/nav.php'; ?>
<?php include 'includes/footer.php'; ?>