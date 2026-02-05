<?php
$pageTitle = 'תנאי שימוש';
$showNav = false;
session_start();
?>
<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>תנאי שימוש - Matcha</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>
<div class="app-container" style="max-width: 800px;">
    <header class="header">
        <a href="javascript:history.back()" class="header-icon-btn">
            <i data-feather="arrow-right"></i>
        </a>
        <h1 style="font-size: 1.125rem; font-weight: 600;">תנאי שימוש</h1>
        <div style="width: 44px;"></div>
    </header>

    <main style="padding: var(--spacing-lg); line-height: 1.8; color: var(--text-main);">
        <p style="color: var(--text-muted); margin-bottom: var(--spacing-lg);">עדכון אחרון: <?php echo date('d/m/Y'); ?></p>

        <h2 style="font-size: 1.25rem; margin-bottom: var(--spacing-md);">1. כללי</h2>
        <p>ברוכים הבאים ל-Matcha (להלן: "הפלטפורמה"). השימוש בפלטפורמה מותנה בקבלת תנאי שימוש אלה. בעצם השימוש בפלטפורמה, הנך מסכים/ה לתנאים אלה במלואם.</p>

        <h2 style="font-size: 1.25rem; margin: var(--spacing-lg) 0 var(--spacing-md);">2. הגבלת גיל</h2>
        <p>השימוש בפלטפורמה מיועד לאנשים בני 18 ומעלה בלבד. בהרשמה לפלטפורמה הנך מצהיר/ה כי גילך 18 שנים לפחות.</p>

        <h2 style="font-size: 1.25rem; margin: var(--spacing-lg) 0 var(--spacing-md);">3. השימוש בפלטפורמה</h2>
        <p>הפלטפורמה מיועדת לחיבור בין מחפשי עבודה למעסיקים. המשתמשים מתחייבים לעשות שימוש הוגן ולהזין מידע אמיתי ומדויק.</p>

        <h2 style="font-size: 1.25rem; margin: var(--spacing-lg) 0 var(--spacing-md);">4. תוכן המשתמש</h2>
        <p>המשתמש/ת אחראי/ת באופן בלעדי לכל תוכן שהוא/היא מעלה לפלטפורמה, לרבות פרטי פרופיל, קורות חיים, הודעות ותמונות. אין להעלות תוכן פוגעני, מטעה או בלתי חוקי.</p>

        <h2 style="font-size: 1.25rem; margin: var(--spacing-lg) 0 var(--spacing-md);">5. הגבלת אחריות</h2>
        <p>הפלטפורמה אינה אחראית לאינטראקציות בין משתמשים, לתוצאות של ראיונות עבודה, או להתאמה בין מועמדים למעסיקים. השירות ניתן "כמות שהוא" (AS IS).</p>

        <h2 style="font-size: 1.25rem; margin: var(--spacing-lg) 0 var(--spacing-md);">6. פרטיות</h2>
        <p>איסוף ושימוש במידע אישי כפופים ל<a href="/privacy.php" style="color: var(--primary);">מדיניות הפרטיות</a> של הפלטפורמה.</p>

        <h2 style="font-size: 1.25rem; margin: var(--spacing-lg) 0 var(--spacing-md);">7. ביטול חשבון</h2>
        <p>המשתמש/ת רשאי/ת למחוק את חשבונו/ה בכל עת דרך דף הפרופיל. מחיקת החשבון תמחק את כל המידע האישי מהמערכת.</p>

        <h2 style="font-size: 1.25rem; margin: var(--spacing-lg) 0 var(--spacing-md);">8. שינויים בתנאים</h2>
        <p>אנו שומרים לעצמנו את הזכות לעדכן תנאי שימוש אלה מעת לעת. שינויים מהותיים יפורסמו בפלטפורמה.</p>

        <h2 style="font-size: 1.25rem; margin: var(--spacing-lg) 0 var(--spacing-md);">9. יצירת קשר</h2>
        <p>לשאלות בנוגע לתנאי השימוש ניתן לפנות אלינו בכתובת: support@matcha.co.il</p>
    </main>
</div>
<script>feather.replace();</script>
</body>
</html>
