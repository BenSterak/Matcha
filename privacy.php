<?php
$pageTitle = 'מדיניות פרטיות';
session_start();
?>
<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>מדיניות פרטיות - Matcha</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>
<div class="app-container" style="max-width: 800px;">
    <header class="header">
        <a href="javascript:history.back()" class="header-icon-btn">
            <i data-feather="arrow-right"></i>
        </a>
        <h1 style="font-size: 1.125rem; font-weight: 600;">מדיניות פרטיות</h1>
        <div style="width: 44px;"></div>
    </header>

    <main style="padding: var(--spacing-lg); line-height: 1.8; color: var(--text-main);">
        <p style="color: var(--text-muted); margin-bottom: var(--spacing-lg);">עדכון אחרון: <?php echo date('d/m/Y'); ?></p>

        <h2 style="font-size: 1.25rem; margin-bottom: var(--spacing-md);">1. מידע שאנו אוספים</h2>
        <p>אנו אוספים את המידע הבא:</p>
        <ul style="padding-right: var(--spacing-lg); margin-bottom: var(--spacing-md);">
            <li>פרטים אישיים: שם, כתובת אימייל, מספר טלפון</li>
            <li>פרטי פרופיל: ביוגרפיה, תחום עיסוק, שכר מבוקש, מודל עבודה מועדף</li>
            <li>קורות חיים ותיק עבודות (אם הועלו)</li>
            <li>נתוני שימוש: החלקות, התאמות, הודעות</li>
            <li>עוגיות (Cookies) לניהול סשן</li>
        </ul>

        <h2 style="font-size: 1.25rem; margin: var(--spacing-lg) 0 var(--spacing-md);">2. כיצד אנו משתמשים במידע</h2>
        <p>המידע משמש אותנו למטרות הבאות:</p>
        <ul style="padding-right: var(--spacing-lg); margin-bottom: var(--spacing-md);">
            <li>ניהול החשבון וזיהוי המשתמש</li>
            <li>התאמת משרות למועמדים ולהפך</li>
            <li>שליחת עדכונים והתראות</li>
            <li>שיפור השירות וחוויית המשתמש</li>
        </ul>

        <h2 style="font-size: 1.25rem; margin: var(--spacing-lg) 0 var(--spacing-md);">3. שיתוף מידע</h2>
        <p>אנו לא מוכרים או משתפים מידע אישי עם צדדים שלישיים, למעט:</p>
        <ul style="padding-right: var(--spacing-lg); margin-bottom: var(--spacing-md);">
            <li>מעסיקים שהתקבלה עמם התאמה (Match)</li>
            <li>ספקי שירות טכניים הנחוצים להפעלת הפלטפורמה</li>
            <li>כנדרש על פי חוק</li>
        </ul>

        <h2 style="font-size: 1.25rem; margin: var(--spacing-lg) 0 var(--spacing-md);">4. עוגיות (Cookies)</h2>
        <p>אנו משתמשים בעוגיות לניהול סשן (Session) בלבד. העוגיות הכרחיות לתפקוד המערכת ומאפשרות לכם להישאר מחוברים.</p>

        <h2 style="font-size: 1.25rem; margin: var(--spacing-lg) 0 var(--spacing-md);">5. אבטחת מידע</h2>
        <p>אנו נוקטים באמצעי אבטחה סבירים להגנה על המידע שלכם, לרבות הצפנת סיסמאות ושימוש בפרוטוקול HTTPS.</p>

        <h2 style="font-size: 1.25rem; margin: var(--spacing-lg) 0 var(--spacing-md);">6. זכויות המשתמש</h2>
        <p>הנכם זכאים ל:</p>
        <ul style="padding-right: var(--spacing-lg); margin-bottom: var(--spacing-md);">
            <li>עיון במידע האישי שלכם (דרך דף הפרופיל)</li>
            <li>תיקון מידע שגוי</li>
            <li>מחיקת החשבון וכל המידע הנלווה</li>
        </ul>

        <h2 style="font-size: 1.25rem; margin: var(--spacing-lg) 0 var(--spacing-md);">7. יצירת קשר</h2>
        <p>לשאלות בנוגע למדיניות הפרטיות או לבקשות הנוגעות למידע אישי, ניתן לפנות אלינו: support@matcha.co.il</p>
    </main>
</div>
<script>feather.replace();</script>
</body>
</html>
