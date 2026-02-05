<?php
$pageTitle = 'Matcha - השמת עובדים חכמה';
session_start();

// If user is already logged in, redirect to feed
if (isset($_SESSION['user_id'])) {
    header('Location: /feed.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="he" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#10B981">
    <meta name="description" content="Matcha - פלטפורמת הגיוס החדשה שמחברת בין מעסיקים למועמדים באמצעות AI">
    <title>Matcha - מהפכת הגיוס כבר כאן</title>

    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', 'GA_MEASUREMENT_ID');
    </script>

    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/landing.css?v=<?php echo time(); ?>">
    <script src="https://unpkg.com/feather-icons"></script>
</head>

<body class="landing-page">

    <!-- Navbar -->
    <nav class="landing-nav">
        <div class="nav-brand">
            <img src="assets/images/ICON.jpeg" alt="Logo">
            <span>Matcha</span>
        </div>
        <div>
            <a href="login.php" class="btn btn-secondary btn-sm">כניסה</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="landing-hero">
        <div class="hero-content">
            <div class="hero-text">
                <div class="hero-badge">
                    ✨ הגיוס של המחר
                </div>
                <h1 class="hero-title">למצוא עבודה ב-Swipe אחד.</h1>
                <p class="hero-subtitle">
                    המערכת החכמה שמחברת בין כישרונות למעסיקים. בלי קורות חיים מסורבלים, בלי המתנה. פשוט התאמה מושלמת.
                </p>

                <div class="hero-actions">
                    <a href="register.php" class="btn btn-primary btn-lg">
                        <i data-feather="user-plus"></i>
                        יצירת חשבון בחינם
                    </a>
                    <a href="#features" class="btn btn-secondary btn-lg btn-landing-outline">
                        <i data-feather="info"></i>
                        איך זה עובד?
                    </a>
                </div>

                <div class="hero-social-proof">
                    <div class="avatar-group">
                        <img src="https://ui-avatars.com/api/?name=Or&background=random">
                        <img src="https://ui-avatars.com/api/?name=Gal&background=random">
                        <img src="https://ui-avatars.com/api/?name=Dan&background=random">
                    </div>
                    <span>הצטרפו ל-1,000+ מועמדים</span>
                </div>
            </div>

            <!-- Phone Mockup -->
            <div class="hero-mockup">
                <div class="mockup-frame">
                    <div class="mockup-screen"
                        style="background-image: url('https://images.unsplash.com/photo-1586281380349-632531db7ed4?w=600&q=80');">
                        <!-- Overlay UI Simulation -->
                        <div class="mockup-overlay">
                            <div class="mockup-card">
                                <h4>מפתח/ת Full Stack</h4>
                                <p>תל אביב • 30k-40k</p>
                            </div>
                            <div class="mockup-actions">
                                <div class="mockup-btn">
                                    <i data-feather="x" width="30" height="30"></i>
                                </div>
                                <div class="mockup-btn like">
                                    <i data-feather="heart" width="30" height="30"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Features Section -->
    <section id="features" class="landing-features">
        <div class="section-header">
            <h2>למה לבחור ב-Matcha?</h2>
            <p>הפלטפורמה שלנו משנה את חוקי המשחק באמצעות טכנולוגיה מתקדמת וחווית משתמש
                ממכרת.</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i data-feather="cpu"></i>
                </div>
                <h3>התאמה מבוססת AI</h3>
                <p>האלגוריתם שלנו לומד את ההעדפות שלכם ומציג לכם רק משרות או מועמדים שבאמת רלוונטיים.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i data-feather="zap"></i>
                </div>
                <h3>מהירות שיא</h3>
                <p>שכחו מתהליכים ארוכים ומייגעים. אצלנו הכל קורה ברגע - Swipe אחד ואתם בדרך לראיון.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i data-feather="message-circle"></i>
                </div>
                <h3>תקשורת ישירה</h3>
                <p>נוצר חיבור (Match)? נפתח צ'אט מיידי בין המעסיק למועמד. בלי מתווכים ובלי עיכובים.</p>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="landing-cta">
        <div class="cta-content">
            <h2>מוכנים למצוא את ההתאמה המושלמת?</h2>
            <p>אלפי מועמדים ומעסיקים
                כבר כאן. ההצטרפות חינם ולוקחת פחות דקה.</p>
            <a href="register.php" class="btn btn-primary btn-lg btn-cta-light">
                התחילו עכשיו
                <i data-feather="arrow-left"></i>
            </a>
        </div>
    </section>

    <footer class="landing-footer">
        <div class="footer-links">
            <a href="/terms.php">תנאי שימוש</a>
            <a href="/privacy.php">מדיניות פרטיות</a>
            <a href="/accessibility.php">נגישות</a>
        </div>
        <p>&copy; <?php echo date('Y'); ?> Matcha. כל הזכויות שמורות.</p>
    </footer>

    <script>
        feather.replace();
    </script>
</body>

</html>