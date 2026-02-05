<?php
$pageTitle = 'הצהרת נגישות';
session_start();
?>
<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>הצהרת נגישות - Matcha</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>
<div class="app-container" style="max-width: 800px;">
    <header class="header">
        <a href="javascript:history.back()" class="header-icon-btn">
            <i data-feather="arrow-right"></i>
        </a>
        <h1 style="font-size: 1.125rem; font-weight: 600;">הצהרת נגישות</h1>
        <div style="width: 44px;"></div>
    </header>

    <main style="padding: var(--spacing-lg); line-height: 1.8; color: var(--text-main);">
        <p style="color: var(--text-muted); margin-bottom: var(--spacing-lg);">עדכון אחרון: <?php echo date('d/m/Y'); ?></p>

        <h2 style="font-size: 1.25rem; margin-bottom: var(--spacing-md);">מחויבות לנגישות</h2>
        <p>Matcha מחויבת להנגשת הפלטפורמה לכלל האוכלוסייה, לרבות אנשים עם מוגבלויות, בהתאם לתקנות שוויון זכויות לאנשים עם מוגבלות (התאמות נגישות לשירות), התשע"ג-2013 ובהתאם לתקן הישראלי 5568.</p>

        <h2 style="font-size: 1.25rem; margin: var(--spacing-lg) 0 var(--spacing-md);">פעולות שננקטו</h2>
        <ul style="padding-right: var(--spacing-lg); margin-bottom: var(--spacing-md);">
            <li>תמיכה בממשק RTL (ימין לשמאל)</li>
            <li>גדלי טקסט ניתנים להתאמה</li>
            <li>ניגודיות צבעים בהתאם לתקן WCAG 2.1</li>
            <li>תמיכה בניווט מקלדת</li>
            <li>תגיות Alt לתמונות</li>
            <li>מבנה HTML סמנטי</li>
        </ul>

        <h2 style="font-size: 1.25rem; margin: var(--spacing-lg) 0 var(--spacing-md);">רכז נגישות</h2>
        <p>לפניות בנושא נגישות ניתן לפנות לרכז/ת הנגישות שלנו:</p>
        <div style="background: var(--background); padding: var(--spacing-lg); border-radius: var(--radius-lg); margin-top: var(--spacing-md);">
            <p><strong>אימייל:</strong> accessibility@matcha.co.il</p>
            <p><strong>טלפון:</strong> 03-0000000</p>
        </div>

        <h2 style="font-size: 1.25rem; margin: var(--spacing-lg) 0 var(--spacing-md);">דיווח על בעיית נגישות</h2>
        <p>אם נתקלתם בבעיית נגישות באתר, נשמח לשמוע ולטפל בהקדם. אנא פנו אלינו בכתובת המייל או הטלפון המצוינים לעיל.</p>
    </main>
</div>
<script>feather.replace();</script>
</body>
</html>
