<?php
/**
 * Matcha - Email Notification System (SendGrid)
 *
 * Uses SendGrid Web API v3 directly via cURL (no SDK required).
 * All emails are sent in Hebrew with RTL support.
 */

require_once __DIR__ . '/../config/mail.php';

/**
 * Send email via SendGrid API
 */
function sendEmail($toEmail, $toName, $subject, $htmlBody)
{
    if (SENDGRID_API_KEY === 'YOUR_SENDGRID_API_KEY_HERE') {
        error_log('SendGrid API key not configured. Email not sent to: ' . $toEmail);
        return false;
    }

    $data = [
        'personalizations' => [
            [
                'to' => [['email' => $toEmail, 'name' => $toName]],
                'subject' => $subject
            ]
        ],
        'from' => [
            'email' => MAIL_FROM_EMAIL,
            'name' => MAIL_FROM_NAME
        ],
        'content' => [
            ['type' => 'text/html', 'value' => $htmlBody]
        ]
    ];

    $ch = curl_init('https://api.sendgrid.com/v3/mail/send');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . SENDGRID_API_KEY,
            'Content-Type: application/json'
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode >= 200 && $httpCode < 300) {
        return true;
    }

    error_log("SendGrid error (HTTP $httpCode): $response");
    return false;
}

/**
 * Wrap content in a branded HTML email template
 */
function emailTemplate($title, $bodyContent, $ctaUrl = '', $ctaText = '')
{
    $cta = '';
    if ($ctaUrl && $ctaText) {
        $ctaUrl = htmlspecialchars($ctaUrl);
        $ctaText = htmlspecialchars($ctaText);
        $cta = "
        <div style='text-align: center; margin: 30px 0;'>
            <a href='{$ctaUrl}' style='display: inline-block; background: #10B981; color: white; padding: 14px 32px; border-radius: 50px; text-decoration: none; font-weight: 600; font-size: 16px;'>
                {$ctaText}
            </a>
        </div>";
    }

    return "
    <!DOCTYPE html>
    <html lang='he' dir='rtl'>
    <head><meta charset='UTF-8'></head>
    <body style='margin: 0; padding: 0; background: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Arial, sans-serif;'>
        <div style='max-width: 560px; margin: 0 auto; padding: 40px 20px;'>
            <!-- Header -->
            <div style='text-align: center; margin-bottom: 30px;'>
                <div style='display: inline-block; background: #10B981; color: white; font-size: 24px; font-weight: 700; padding: 10px 20px; border-radius: 12px;'>
                    Matcha
                </div>
            </div>

            <!-- Card -->
            <div style='background: white; border-radius: 16px; padding: 32px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);'>
                <h2 style='margin: 0 0 16px; color: #1a1a1a; font-size: 20px;'>{$title}</h2>
                <div style='color: #4a5568; line-height: 1.7; font-size: 15px;'>
                    {$bodyContent}
                </div>
                {$cta}
            </div>

            <!-- Footer -->
            <div style='text-align: center; margin-top: 30px; color: #a0aec0; font-size: 12px; line-height: 1.6;'>
                <p style='margin: 0;'>&copy; " . date('Y') . " Matcha. כל הזכויות שמורות.</p>
                <p style='margin: 4px 0 0;'>
                    לא רוצים לקבל התראות? <a href='" . APP_URL . "/profile.php' style='color: #10B981;'>עדכנו את ההגדרות</a>
                </p>
            </div>
        </div>
    </body>
    </html>";
}

/**
 * Check if user wants email notifications
 */
function shouldSendEmail($pdo, $userId)
{
    $stmt = $pdo->prepare("SELECT email_notifications FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetch();
    return $result && $result['email_notifications'];
}

// ============================================================
// Notification Functions
// ============================================================

/**
 * Notify employer when a candidate swipes right on their job
 */
function notifyNewCandidate($pdo, $jobId, $candidateUserId)
{
    try {
        // Get job & employer info
        $stmt = $pdo->prepare("
            SELECT j.title as job_title, j.business_id, u.name as employer_name, u.email as employer_email
            FROM jobs j
            JOIN users u ON j.business_id = u.id
            WHERE j.id = ?
        ");
        $stmt->execute([$jobId]);
        $job = $stmt->fetch();
        if (!$job) return;

        if (!shouldSendEmail($pdo, $job['business_id'])) return;

        // Get candidate info
        $stmt = $pdo->prepare("SELECT name, field FROM users WHERE id = ?");
        $stmt->execute([$candidateUserId]);
        $candidate = $stmt->fetch();
        if (!$candidate) return;

        $candidateName = htmlspecialchars($candidate['name']);
        $jobTitle = htmlspecialchars($job['job_title']);

        $body = "
            <p>מועמד/ת חדש/ה התעניין/ה במשרה שלך!</p>
            <div style='background: #f0fdf4; padding: 16px; border-radius: 12px; margin: 16px 0;'>
                <p style='margin: 0; font-weight: 600; color: #1a1a1a;'>{$candidateName}</p>
                <p style='margin: 4px 0 0; color: #6b7280; font-size: 14px;'>מעוניין/ת במשרת {$jobTitle}</p>
            </div>
            <p>היכנסו למערכת כדי לבדוק את הפרופיל ולאשר או לדחות.</p>
        ";

        $html = emailTemplate(
            'מועמד/ת חדש/ה למשרה שלך!',
            $body,
            APP_URL . '/business/candidates.php',
            'צפייה במועמדים'
        );

        sendEmail($job['employer_email'], $job['employer_name'], "Matcha: מועמד/ת חדש/ה למשרת $jobTitle", $html);
    } catch (\Exception $e) {
        error_log('Email notification error (newCandidate): ' . $e->getMessage());
    }
}

/**
 * Notify jobseeker when employer approves their match
 */
function notifyMatchApproved($pdo, $matchId)
{
    try {
        $stmt = $pdo->prepare("
            SELECT m.userId, j.title as job_title, j.company,
                   u.name as candidate_name, u.email as candidate_email
            FROM matches m
            JOIN jobs j ON m.jobId = j.id
            JOIN users u ON m.userId = u.id
            WHERE m.id = ?
        ");
        $stmt->execute([$matchId]);
        $match = $stmt->fetch();
        if (!$match) return;

        if (!shouldSendEmail($pdo, $match['userId'])) return;

        $jobTitle = htmlspecialchars($match['job_title']);
        $company = htmlspecialchars($match['company']);

        $body = "
            <p>יש לך Match חדש!</p>
            <div style='background: #fce7f3; padding: 16px; border-radius: 12px; margin: 16px 0; text-align: center;'>
                <div style='font-size: 32px; margin-bottom: 8px;'>🎉</div>
                <p style='margin: 0; font-weight: 600; color: #1a1a1a;'>{$jobTitle}</p>
                <p style='margin: 4px 0 0; color: #6b7280; font-size: 14px;'>{$company}</p>
            </div>
            <p>המעסיק אישר את ההתאמה! עכשיו אפשר להתחיל לשוחח.</p>
        ";

        $html = emailTemplate(
            'יש Match! 🎉',
            $body,
            APP_URL . '/chat.php',
            'שלחו הודעה'
        );

        sendEmail($match['candidate_email'], $match['candidate_name'], "Matcha: יש לך Match חדש! 🎉", $html);
    } catch (\Exception $e) {
        error_log('Email notification error (matchApproved): ' . $e->getMessage());
    }
}

/**
 * Notify user when they receive a new chat message (only if offline)
 */
function notifyNewMessage($pdo, $matchId, $senderId, $messageContent)
{
    try {
        // Get match details to find the recipient
        $stmt = $pdo->prepare("
            SELECT m.userId, j.business_id
            FROM matches m
            JOIN jobs j ON m.jobId = j.id
            WHERE m.id = ?
        ");
        $stmt->execute([$matchId]);
        $match = $stmt->fetch();
        if (!$match) return;

        // Determine recipient (the other party)
        $recipientId = ($senderId == $match['userId']) ? $match['business_id'] : $match['userId'];

        if (!shouldSendEmail($pdo, $recipientId)) return;

        // Check if recipient is online (active in last 5 minutes) - skip notification if online
        $stmt = $pdo->prepare("SELECT name, email, last_seen FROM users WHERE id = ?");
        $stmt->execute([$recipientId]);
        $recipient = $stmt->fetch();
        if (!$recipient) return;

        if (!empty($recipient['last_seen'])) {
            $lastSeen = strtotime($recipient['last_seen']);
            if ((time() - $lastSeen) < 300) {
                return; // User is online, no need for email
            }
        }

        // Get sender name
        $stmt = $pdo->prepare("SELECT name FROM users WHERE id = ?");
        $stmt->execute([$senderId]);
        $sender = $stmt->fetch();
        if (!$sender) return;

        $senderName = htmlspecialchars($sender['name']);
        $preview = htmlspecialchars(mb_substr($messageContent, 0, 100, 'UTF-8'));
        if (mb_strlen($messageContent, 'UTF-8') > 100) {
            $preview .= '...';
        }

        $body = "
            <p>קיבלת הודעה חדשה!</p>
            <div style='background: #f3f4f6; padding: 16px; border-radius: 12px; margin: 16px 0;'>
                <p style='margin: 0 0 8px; font-weight: 600; color: #1a1a1a;'>{$senderName}</p>
                <p style='margin: 0; color: #4a5568; font-size: 14px;'>\"{$preview}\"</p>
            </div>
            <p>היכנסו לצ'אט כדי להשיב.</p>
        ";

        $html = emailTemplate(
            'הודעה חדשה מ-' . $senderName,
            $body,
            APP_URL . '/chat.php?match=' . $matchId,
            'פתח צ\'אט'
        );

        sendEmail($recipient['email'], $recipient['name'], "Matcha: הודעה חדשה מ-$senderName", $html);
    } catch (\Exception $e) {
        error_log('Email notification error (newMessage): ' . $e->getMessage());
    }
}
