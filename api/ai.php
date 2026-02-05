<?php
/**
 * AI API Endpoint
 * Handles AI-powered features using Google Gemini API
 */

header('Content-Type: application/json; charset=utf-8');
session_start();
require_once '../config/db.php';

// IMPORTANT: Replace with your actual Gemini API key
// Get your API key from: https://makersuite.google.com/app/apikey
define('GEMINI_API_KEY', 'AIzaSyBPstyEzy4qYTctl-td_MqDcmdAKvBGS-o');
define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent');

// Check authentication
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized'], JSON_UNESCAPED_UNICODE);
    exit;
}

$userId = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';

// Helper function to call Gemini API
function callGemini($prompt)
{
    if (GEMINI_API_KEY === 'YOUR_GEMINI_API_KEY_HERE') {
        return ['error' => 'API key not configured. Please set your Gemini API key in api/ai.php'];
    }

    $data = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.7,
            'maxOutputTokens' => 500,
        ]
    ];

    $ch = curl_init(GEMINI_API_URL . '?key=' . GEMINI_API_KEY);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_TIMEOUT => 30
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        return ['error' => 'Gemini API error: HTTP ' . $httpCode];
    }

    $result = json_decode($response, true);

    if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        return ['text' => $result['candidates'][0]['content']['parts'][0]['text']];
    }

    return ['error' => 'Unexpected API response'];
}

switch ($action) {
    case 'enhance_bio':
        // Enhance user bio with AI
        $input = json_decode(file_get_contents('php://input'), true);
        $currentBio = trim($input['bio'] ?? '');
        $field = trim($input['field'] ?? '');
        $name = trim($input['name'] ?? '');

        if (empty($currentBio) && empty($field)) {
            echo json_encode([
                'success' => false,
                'error' => 'אנא הזינו טקסט קצר על עצמכם או בחרו תחום עיסוק'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Build prompt
        $fieldNames = [
            'tech' => 'הייטק וטכנולוגיה',
            'marketing' => 'שיווק ופרסום',
            'sales' => 'מכירות',
            'finance' => 'פיננסים וכלכלה',
            'design' => 'עיצוב',
            'hr' => 'משאבי אנוש',
            'operations' => 'תפעול ולוגיסטיקה',
            'other' => ''
        ];

        $fieldHeb = $fieldNames[$field] ?? '';

        $prompt = "אתה עוזר לכתוב תיאור עצמי (bio) לפרופיל של מחפש עבודה באפליקציית גיוס.

";
        if ($currentBio) {
            $prompt .= "הטקסט הנוכחי: \"$currentBio\"\n\n";
        }
        if ($fieldHeb) {
            $prompt .= "התחום: $fieldHeb\n\n";
        }
        if ($name) {
            $prompt .= "השם: $name\n\n";
        }

        $prompt .= "כתוב תיאור עצמי משודרג בעברית שיהיה:
- מקצועי אך אישי וחם
- קצר וקולע (2-4 משפטים, עד 150 מילים)
- ימשוך את תשומת הלב של מעסיקים
- יבליט נקודות חוזק
- בגוף ראשון

החזר רק את הטקסט המשודרג, ללא הסברים נוספים.";

        $result = callGemini($prompt);

        if (isset($result['error'])) {
            // Generate a fallback enhanced bio without API
            $templates = [
                'tech' => "אני איש/אשת מקצוע בתחום הטכנולוגיה עם תשוקה לפתרון בעיות מורכבות. בעל/ת יכולת למידה מהירה, חשיבה אנליטית ויכולת עבודה בצוות. מחפש/ת אתגר חדש שיאפשר לי להתפתח ולתרום.",
                'marketing' => "אני איש/אשת שיווק יצירתי/ת עם ניסיון בבניית קמפיינים מוצלחים. בעל/ת חשיבה אסטרטגית, יצירתיות וכישורי תקשורת מעולים. מחפש/ת הזדמנות להוביל פרויקטים משמעותיים.",
                'sales' => "אני איש/אשת מכירות עם יכולת בניית קשרים ארוכי טווח עם לקוחות. בעל/ת כישורי משא ומתן, יכולת שכנוע וגישה מכוונת תוצאות. מחפש/ת אתגר חדש בסביבה דינמית.",
                'finance' => "אני איש/אשת פיננסים עם עין לפרטים ויכולת אנליטית גבוהה. בעל/ת ניסיון בניתוח נתונים, תכנון תקציבי וקבלת החלטות מבוססות מידע. מחפש/ת תפקיד שיאתגר אותי.",
                'design' => "אני מעצב/ת עם עין חדה לאסתטיקה ופתרונות יצירתיים. בעל/ת ניסיון בעיצוב חוויות משתמש, עבודה עם לקוחות וניהול פרויקטים. מחפש/ת הזדמנות ליצור עיצובים משמעותיים.",
                'hr' => "אני איש/אשת משאבי אנוש עם תשוקה לאנשים ופיתוח ארגוני. בעל/ת כישורי תקשורת בינאישית, יכולת גיוס וליווי עובדים. מחפש/ת תפקיד שיאפשר לי להשפיע על תרבות ארגונית.",
                'operations' => "אני איש/אשת תפעול עם יכולת ארגון וניהול תהליכים. בעל/ת חשיבה מערכתית, יכולת פתרון בעיות ועמידה בלוחות זמנים. מחפש/ת אתגר בסביבה דינמית.",
                'other' => "אני בעל/ת מוטיבציה גבוהה ורצון להתפתח מקצועית. מביא/ה איתי יכולת למידה מהירה, גישה חיובית ונכונות לתרום. מחפש/ת הזדמנות להוכיח את עצמי ולצמוח."
            ];

            $enhancedBio = '';
            if (!empty($currentBio)) {
                // Enhance existing bio
                $enhancedBio = $currentBio;
                if (strlen($currentBio) < 50) {
                    $enhancedBio .= " מחפש/ת הזדמנות להתפתח ולתרום בסביבת עבודה מאתגרת ודינמית.";
                }
            } elseif (!empty($field) && isset($templates[$field])) {
                $enhancedBio = $templates[$field];
            } else {
                $enhancedBio = $templates['other'];
            }

            echo json_encode([
                'success' => true,
                'enhanced_bio' => $enhancedBio
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        echo json_encode([
            'success' => true,
            'enhanced_bio' => trim($result['text'])
        ], JSON_UNESCAPED_UNICODE);
        break;

    case 'icebreaker':
        // Generate icebreaker message for chat
        $input = json_decode(file_get_contents('php://input'), true);
        $matchId = intval($input['matchId'] ?? 0);

        if (!$matchId) {
            echo json_encode(['success' => false, 'error' => 'Match ID required'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        try {
            // Get match context (job title + candidate/employer bio)
            $stmt = $pdo->prepare("
                SELECT j.title as jobTitle, j.description as jobDesc, j.company,
                       u.name as candidateName, u.bio as candidateBio, u.field as candidateField
                FROM matches m
                JOIN jobs j ON m.jobId = j.id
                JOIN users u ON m.userId = u.id
                WHERE m.id = ? AND m.status = 'matched'
            ");
            $stmt->execute([$matchId]);
            $matchData = $stmt->fetch();

            if (!$matchData) {
                echo json_encode(['success' => false, 'error' => 'Match not found'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // Check user role
            $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $role = $stmt->fetchColumn();

            if ($role === 'employer') {
                $prompt = "אתה עוזר למעסיק לפתוח שיחה עם מועמד שהתאמתם ביניהם באפליקציית גיוס.

פרטי המשרה: {$matchData['jobTitle']}
שם המועמד: {$matchData['candidateName']}
על המועמד: {$matchData['candidateBio']}
תחום: {$matchData['candidateField']}

כתוב הודעת פתיחה קצרה (1-2 משפטים) בעברית שתהיה:
- חמה ומקצועית
- מזמינה ולא פורמלית מדי
- מתייחסת למשרה הספציפית
- מעודדת תגובה

החזר רק את ההודעה, ללא הסברים.";
            } else {
                $prompt = "אתה עוזר למועמד לפתוח שיחה עם מעסיק שהתאמתם ביניהם באפליקציית גיוס.

המשרה: {$matchData['jobTitle']}
החברה: {$matchData['company']}

כתוב הודעת פתיחה קצרה (1-2 משפטים) בעברית שתהיה:
- מקצועית אך חמה
- מביעה התלהבות אמיתית מהמשרה
- לא מוגזמת או חנפנית
- מעודדת תגובה

החזר רק את ההודעה, ללא הסברים.";
            }

            $result = callGemini($prompt);

            if (isset($result['error'])) {
                // Fallback icebreakers
                $fallbacks = $role === 'employer'
                    ? [
                        "היי {$matchData['candidateName']}, שמחנו לראות את הפרופיל שלך! נשמח לשוחח על המשרה {$matchData['jobTitle']}.",
                        "שלום {$matchData['candidateName']}! הפרופיל שלך תפס את העין שלנו. רוצה לשמוע עוד על התפקיד?",
                        "היי! ראינו את הפרופיל שלך ואנחנו חושבים שיכולה להיות כאן התאמה מעולה. מתי נוח לדבר?"
                    ]
                    : [
                        "היי! שמח/ה מאוד על ההתאמה. המשרה {$matchData['jobTitle']} נשמעת ממש מעניינת!",
                        "שלום! תודה על ההתאמה. אשמח לשמוע עוד על התפקיד ועל הצוות.",
                        "היי! ההתאמה שמחה אותי. אשמח לדבר ולשמוע עוד על מה שאתם מחפשים."
                    ];
                $icebreaker = $fallbacks[array_rand($fallbacks)];
            } else {
                $icebreaker = trim($result['text']);
            }

            echo json_encode([
                'success' => true,
                'icebreaker' => $icebreaker
            ], JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => 'Database error'], JSON_UNESCAPED_UNICODE);
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid action'], JSON_UNESCAPED_UNICODE);
}
