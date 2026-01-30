<?php
session_start();
require_once 'config/db.php';

// If user is already logged in, redirect
if (isset($_SESSION['user_id'])) {
    header('Location: /feed.php');
    exit;
}

$step = $_GET['step'] ?? 1;
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'step1') {
        // Step 1: Email, Password, Role
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $role = $_POST['role'] ?? '';

        if (empty($email) || empty($password) || empty($role)) {
            $error = 'אנא מלאו את כל השדות';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'אנא הזינו כתובת אימייל תקינה';
        } elseif (strlen($password) < 6) {
            $error = 'הסיסמה חייבת להכיל לפחות 6 תווים';
        } elseif ($password !== $confirmPassword) {
            $error = 'הסיסמאות אינן תואמות';
        } else {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = 'כתובת האימייל כבר קיימת במערכת';
            } else {
                // Store in session for step 2
                $_SESSION['register'] = [
                    'email' => $email,
                    'password' => $password,
                    'role' => $role
                ];
                header('Location: register.php?step=2');
                exit;
            }
        }
    } elseif ($action === 'step2') {
        // Step 2: Profile details
        if (!isset($_SESSION['register'])) {
            header('Location: register.php');
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        $field = trim($_POST['field'] ?? '');
        $salary = intval($_POST['salary'] ?? 0);
        $workModel = $_POST['work_model'] ?? '';
        $companyName = trim($_POST['company_name'] ?? '');

        if (empty($name)) {
            $error = 'אנא הזינו שם';
            $step = 2;
        } else {
            try {
                $registerData = $_SESSION['register'];
                $hashedPassword = password_hash($registerData['password'], PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("
                    INSERT INTO users (email, password, name, bio, role, field, salary, workModel, company_name, createdAt)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ");

                $stmt->execute([
                    $registerData['email'],
                    $hashedPassword,
                    $name,
                    $bio,
                    $registerData['role'],
                    $field,
                    $salary,
                    $workModel,
                    $registerData['role'] === 'employer' ? $companyName : null
                ]);

                $userId = $pdo->lastInsertId();

                // Clear registration session
                unset($_SESSION['register']);

                // Log in the user
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_role'] = $registerData['role'];
                $_SESSION['user_name'] = $name;
                $_SESSION['new_user'] = true; // Flag for welcome message

                // Redirect based on role
                if ($registerData['role'] === 'employer') {
                    header('Location: /business/dashboard.php');
                } else {
                    header('Location: /feed.php');
                }
                exit;

            } catch (PDOException $e) {
                $error = 'אירעה שגיאה. אנא נסו שוב.';
                $step = 2;
            }
        }
    }
}

// If step 2 but no register session, go back to step 1
if ($step == 2 && !isset($_SESSION['register'])) {
    header('Location: register.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="he" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#2ECC71">
    <title>הרשמה - Matcha</title>

    <!-- Google Analytics - Replace GA_MEASUREMENT_ID with your actual ID -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', 'GA_MEASUREMENT_ID');

        // Track registration page view
        gtag('event', 'page_view', {
            page_title: 'Registration - Step <?php echo $step; ?>',
            page_location: window.location.href
        });
    </script>

    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .step-indicator {
            display: flex;
            justify-content: center;
            gap: var(--spacing-md);
            margin-bottom: var(--spacing-xl);
        }

        .step-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--border);
        }

        .step-dot.active {
            background: var(--primary);
        }

        .step-dot.completed {
            background: var(--primary-dark);
        }
    </style>
</head>

<body>
    <div class="app-container auth-page">
        <a href="<?php echo $step == 2 ? 'register.php' : 'index.php'; ?>" class="auth-back-btn">
            <i data-feather="arrow-right"></i>
            <?php echo $step == 2 ? 'חזרה לשלב הקודם' : 'חזרה לדף הבית'; ?>
        </a>

        <div class="auth-content">
            <div class="auth-logo-container">
                <img src="assets/images/ICON.jpeg" alt="Matcha Logo" class="auth-logo">
                <h1 class="auth-title">
                    <?php echo $step == 1 ? 'הרשמה' : 'פרטים אישיים'; ?>
                </h1>
                <p class="auth-subtitle">
                    <?php echo $step == 1 ? 'צרו חשבון חדש והתחילו לחפש' : 'ספרו לנו קצת על עצמכם'; ?>
                </p>
            </div>

            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step-dot <?php echo $step == 1 ? 'active' : 'completed'; ?>"></div>
                <div class="step-dot <?php echo $step == 2 ? 'active' : ''; ?>"></div>
            </div>

            <?php if ($step == 1): ?>
                <!-- Step 1: Account Details -->
                <form method="POST" class="auth-form">
                    <input type="hidden" name="action" value="step1">

                    <?php if ($error): ?>
                        <div class="auth-error">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label class="form-label">אימייל</label>
                        <div class="input-wrapper">
                            <i data-feather="mail"></i>
                            <input type="email" name="email" class="form-input" placeholder="your@email.com"
                                value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">סיסמה</label>
                        <div class="input-wrapper">
                            <i data-feather="lock"></i>
                            <input type="password" name="password" class="form-input" placeholder="לפחות 6 תווים" required
                                minlength="6">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">אימות סיסמה</label>
                        <div class="input-wrapper">
                            <i data-feather="lock"></i>
                            <input type="password" name="confirm_password" class="form-input"
                                placeholder="הזינו שוב את הסיסמה" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">אני...</label>
                        <div class="role-selection">
                            <label class="role-card">
                                <input type="radio" name="role" value="jobseeker" style="display:none" required>
                                <div class="role-icon">
                                    <i data-feather="search"></i>
                                </div>
                                <div class="role-info">
                                    <h3>מחפש/ת עבודה</h3>
                                    <p>רוצה למצוא את המשרה הבאה שלי</p>
                                </div>
                            </label>
                            <label class="role-card">
                                <input type="radio" name="role" value="employer" style="display:none">
                                <div class="role-icon">
                                    <i data-feather="briefcase"></i>
                                </div>
                                <div class="role-info">
                                    <h3>מעסיק/ה</h3>
                                    <p>רוצה לפרסם משרות ולמצוא עובדים</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-full">
                        המשך
                        <i data-feather="arrow-left"></i>
                    </button>
                </form>

            <?php else: ?>
                <!-- Step 2: Profile Details -->
                <form method="POST" class="auth-form">
                    <input type="hidden" name="action" value="step2">

                    <?php if ($error): ?>
                        <div class="auth-error">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($_SESSION['register']['role'] === 'employer'): ?>
                        <!-- Employer Fields -->
                        <div class="form-group">
                            <label class="form-label">שם החברה</label>
                            <div class="input-wrapper">
                                <i data-feather="building"></i>
                                <input type="text" name="company_name" class="form-input" placeholder="שם החברה שלכם" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">שם איש קשר</label>
                            <div class="input-wrapper">
                                <i data-feather="user"></i>
                                <input type="text" name="name" class="form-input" placeholder="השם שלכם" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">על החברה</label>
                            <textarea name="bio" class="form-input" placeholder="ספרו קצת על החברה..." rows="3"
                                style="resize: none; padding-right: var(--spacing-md);"></textarea>
                        </div>

                    <?php else: ?>
                        <!-- Job Seeker Fields -->
                        <div class="form-group">
                            <label class="form-label">שם מלא</label>
                            <div class="input-wrapper">
                                <i data-feather="user"></i>
                                <input type="text" name="name" class="form-input" placeholder="השם שלכם" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">על עצמי</label>
                            <textarea name="bio" class="form-input" placeholder="ספרו קצת על עצמכם..." rows="3"
                                style="resize: none; padding-right: var(--spacing-md);"></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">תחום עיסוק</label>
                            <div class="input-wrapper">
                                <i data-feather="briefcase"></i>
                                <select name="field" class="form-input"
                                    style="padding-right: calc(var(--spacing-md) + 24px + var(--spacing-sm));">
                                    <option value="">בחרו תחום</option>
                                    <option value="tech">הייטק / טכנולוגיה</option>
                                    <option value="marketing">שיווק ופרסום</option>
                                    <option value="sales">מכירות</option>
                                    <option value="finance">פיננסים וכלכלה</option>
                                    <option value="design">עיצוב</option>
                                    <option value="hr">משאבי אנוש</option>
                                    <option value="operations">תפעול ולוגיסטיקה</option>
                                    <option value="other">אחר</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">שכר מבוקש (ברוטו חודשי)</label>
                            <div class="input-wrapper">
                                <span class="input-icon-text">₪</span>
                                <input type="number" name="salary" class="form-input" placeholder="למשל: 15000" min="0">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">מודל עבודה מועדף</label>
                            <div class="input-wrapper">
                                <i data-feather="home"></i>
                                <select name="work_model" class="form-input"
                                    style="padding-right: calc(var(--spacing-md) + 24px + var(--spacing-sm));">
                                    <option value="">בחרו העדפה</option>
                                    <option value="office">משרד</option>
                                    <option value="remote">עבודה מהבית</option>
                                    <option value="hybrid">היברידי</option>
                                    <option value="physical">עבודה פיזית</option>
                                    <option value="field">עבודת שטח</option>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-primary btn-full">
                        סיום הרשמה
                        <i data-feather="check"></i>
                    </button>
                </form>
            <?php endif; ?>

            <div class="auth-divider">
                <span>או</span>
            </div>

            <p class="auth-register-link">
                יש לכם כבר חשבון? <a href="login.php">התחברו</a>
            </p>
        </div>
    </div>

    <script>
        feather.replace();

        // Role card selection
        document.querySelectorAll('.role-card').forEach(card => {
            card.addEventListener('click', function () {
                document.querySelectorAll('.role-card').forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                this.querySelector('input[type="radio"]').checked = true;
            });
        });
    </script>
</body>

</html>