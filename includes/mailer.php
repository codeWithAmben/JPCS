<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

/**
 * Send email using PHPMailer
 * 
 * @param string $to Recipient email
 * @param string $subject Email subject
 * @param string $body Email body (HTML)
 * @param string $altBody Plain text body (optional)
 * @return array ['success' => bool, 'message' => string]
 */
function sendEmail($to, $subject, $body, $altBody = '') {
    // Check if PHPMailer class exists
    if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        error_log("PHPMailer class not found. Ensure composer dependencies are installed.");
        return ['success' => false, 'message' => 'Email system not configured'];
    }

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = defined('SMTP_HOST') ? SMTP_HOST : 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = defined('SMTP_USER') ? SMTP_USER : '';
        $mail->Password   = defined('SMTP_PASS') ? SMTP_PASS : '';
        $mail->Port       = defined('SMTP_PORT') ? SMTP_PORT : 587;
        $mail->CharSet    = 'UTF-8';

        // Encryption support (use configured value, default to STARTTLS)
        if (defined('SMTP_ENCRYPTION') && SMTP_ENCRYPTION) {
            $enc = strtolower(SMTP_ENCRYPTION);
            if ($enc === 'ssl' || $enc === 'smtps') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }
        } else {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        // Allow self-signed certs in dev environments
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ];

        // Optional SMTP debug (set MAIL_SMTP_DEBUG=1 in .env to enable temporarily)
        $smtpDebug = env('MAIL_SMTP_DEBUG', env('SMTP_DEBUG', 0));
        if ($smtpDebug) {
            $mail->SMTPDebug = \PHPMailer\PHPMailer\SMTP::DEBUG_SERVER;
            $debugLog = BASE_PATH . '/logs/smtp_debug.log';
            $mail->Debugoutput = function($str, $level) use ($debugLog) {
                $entry = date('Y-m-d H:i:s') . " | L{$level} | " . trim($str) . PHP_EOL;
                @file_put_contents($debugLog, $entry, FILE_APPEND);
            };
        }

        // Recipients
        $fromAddress = defined('SMTP_FROM_ADDRESS') ? SMTP_FROM_ADDRESS : (defined('SMTP_USER') ? SMTP_USER : 'noreply@example.com');
        $fromName = defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'JPCS Malvar';
        $mail->setFrom($fromAddress, $fromName);
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = $altBody ? $altBody : strip_tags($body);

        $mail->send();

        // Log successful send
        $logFile = BASE_PATH . '/logs/email_log.txt';
        $entry = date('Y-m-d H:i:s') . " | To: {$to} | Subject: {$subject} | Status: SENT | Info: " . ($mail->ErrorInfo ?: 'OK') . PHP_EOL;
        @file_put_contents($logFile, $entry, FILE_APPEND);

        return ['success' => true, 'message' => 'Message has been sent'];
    } catch (Exception $e) {
        // Log failure with PHPMailer error info
        $logFile = BASE_PATH . '/logs/email_log.txt';
        $entry = date('Y-m-d H:i:s') . " | To: {$to} | Subject: {$subject} | Status: FAILED | Info: " . ($mail->ErrorInfo ?: $e->getMessage()) . PHP_EOL;
        @file_put_contents($logFile, $entry, FILE_APPEND);

        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return ['success' => false, 'message' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"];
    }
}