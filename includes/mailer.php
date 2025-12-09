<?php
/**
 * PHPMailer Configuration and Helper
 * Handles all email sending through SMTP
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require_once BASE_PATH . '/vendor/autoload.php';

/**
 * Get configured PHPMailer instance
 */
function getMailer() {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = getenv('MAIL_HOST') ?: 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = getenv('MAIL_USERNAME') ?: '';
        $mail->Password   = getenv('MAIL_PASSWORD') ?: '';
        $mail->SMTPSecure = getenv('MAIL_ENCRYPTION') === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = (int)(getenv('MAIL_PORT') ?: 587);
        
        // Default sender
        $mail->setFrom(
            getenv('MAIL_FROM_ADDRESS') ?: SITE_EMAIL,
            getenv('MAIL_FROM_NAME') ?: SITE_NAME
        );
        
        // Email settings
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        
        // Debug mode (set to 0 for production)
        $mail->SMTPDebug = 0; // 0 = off, 1 = client messages, 2 = client and server messages
        
    } catch (Exception $e) {
        error_log("Mailer configuration error: " . $e->getMessage());
    }
    
    return $mail;
}

/**
 * Send an email using PHPMailer
 * 
 * @param string $to Recipient email
 * @param string $subject Email subject
 * @param string $htmlBody HTML email body
 * @param string $textBody Plain text alternative (optional)
 * @param array $options Additional options (cc, bcc, attachments, replyTo)
 * @return array ['success' => bool, 'message' => string]
 */
function sendEmail($to, $subject, $htmlBody, $textBody = '', $options = []) {
    $mail = getMailer();
    
    try {
        // Recipients
        $mail->addAddress($to);
        
        // CC recipients
        if (!empty($options['cc'])) {
            foreach ((array)$options['cc'] as $cc) {
                $mail->addCC($cc);
            }
        }
        
        // BCC recipients
        if (!empty($options['bcc'])) {
            foreach ((array)$options['bcc'] as $bcc) {
                $mail->addBCC($bcc);
            }
        }
        
        // Reply-To
        if (!empty($options['replyTo'])) {
            $mail->addReplyTo($options['replyTo']);
        }
        
        // Attachments
        if (!empty($options['attachments'])) {
            foreach ((array)$options['attachments'] as $attachment) {
                if (is_array($attachment)) {
                    $mail->addAttachment($attachment['path'], $attachment['name'] ?? '');
                } else {
                    $mail->addAttachment($attachment);
                }
            }
        }
        
        // Content
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;
        $mail->AltBody = $textBody ?: strip_tags($htmlBody);
        
        // Send
        $mail->send();
        
        // Log success
        logEmail($to, $subject, true);
        
        return ['success' => true, 'message' => 'Email sent successfully'];
        
    } catch (Exception $e) {
        $errorMessage = $mail->ErrorInfo;
        
        // Log failure
        logEmail($to, $subject, false, $errorMessage);
        
        return ['success' => false, 'message' => 'Failed to send email: ' . $errorMessage];
    }
}

/**
 * Log email attempts
 */
function logEmail($to, $subject, $success, $error = '') {
    $logDir = BASE_PATH . '/logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $logFile = $logDir . '/email_log.txt';
    $status = $success ? 'SUCCESS' : 'FAILED';
    $logEntry = sprintf(
        "[%s] %s | To: %s | Subject: %s%s\n",
        date('Y-m-d H:i:s'),
        $status,
        $to,
        $subject,
        $error ? " | Error: $error" : ''
    );
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

/**
 * Test email configuration
 */
function testEmailConfiguration() {
    $testEmail = getenv('MAIL_USERNAME') ?: SITE_EMAIL;
    
    $result = sendEmail(
        $testEmail,
        'JPCS Email Configuration Test',
        '<h1>✅ Email Configuration Working!</h1><p>Your PHPMailer setup is configured correctly.</p>',
        'Email Configuration Working! Your PHPMailer setup is configured correctly.'
    );
    
    return $result;
}

/**
 * Send verification email using PHPMailer
 */
function sendVerificationEmailPHPMailer($email, $token, $code, $name = '') {
    $verificationLink = SITE_URL . '/verify.php?token=' . $token;
    
    $subject = '✉️ Verify Your Email - ' . SITE_NAME;
    
    $htmlBody = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background: #f4f4f4; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .email-wrapper { background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
            .header { background: linear-gradient(135deg, #1a237e, #3949ab); padding: 40px 30px; text-align: center; }
            .header h1 { color: white; margin: 0; font-size: 28px; }
            .header .icon { font-size: 48px; margin-bottom: 15px; }
            .content { padding: 40px 30px; }
            .greeting { font-size: 18px; margin-bottom: 20px; }
            .code-box { 
                background: linear-gradient(135deg, #1a237e, #3949ab); 
                color: white; 
                font-size: 36px; 
                letter-spacing: 12px; 
                padding: 25px; 
                text-align: center; 
                border-radius: 12px; 
                margin: 25px 0; 
                font-family: "Courier New", monospace;
                font-weight: bold;
            }
            .btn { 
                display: inline-block; 
                background: linear-gradient(135deg, #ff6a00, #ff8c42); 
                color: white !important; 
                padding: 18px 40px; 
                text-decoration: none; 
                border-radius: 50px; 
                margin: 20px 0;
                font-weight: bold;
                font-size: 16px;
                box-shadow: 0 4px 15px rgba(255, 106, 0, 0.4);
            }
            .btn:hover { opacity: 0.9; }
            .btn-container { text-align: center; margin: 30px 0; }
            .divider { 
                border: none;
                height: 1px;
                background: linear-gradient(to right, transparent, #ddd, transparent);
                margin: 30px 0;
            }
            .footer { 
                text-align: center; 
                padding: 25px; 
                background: #f9f9f9;
                color: #666; 
                font-size: 13px;
                border-top: 1px solid #eee;
            }
            .footer a { color: #3949ab; text-decoration: none; }
            .warning { 
                background: #fff3e0; 
                border-left: 4px solid #ff9800; 
                padding: 15px 20px; 
                margin: 20px 0;
                border-radius: 0 8px 8px 0;
            }
            .option-title { 
                color: #1a237e; 
                font-size: 16px; 
                font-weight: bold;
                margin: 20px 0 10px 0;
            }
            @media only screen and (max-width: 600px) {
                .code-box { font-size: 28px; letter-spacing: 8px; }
                .btn { padding: 15px 30px; }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="email-wrapper">
                <div class="header">
                    <div class="icon">📧</div>
                    <h1>Verify Your Email</h1>
                </div>
                <div class="content">
                    <p class="greeting">Hello' . ($name ? ' <strong>' . htmlspecialchars($name) . '</strong>' : '') . '! 👋</p>
                    
                    <p>Thank you for registering with <strong>' . SITE_NAME . '</strong>! To complete your registration and activate your account, please verify your email address.</p>
                    
                    <p class="option-title">🔗 Option 1: Click the Button</p>
                    <div class="btn-container">
                        <a href="' . $verificationLink . '" class="btn">✓ Verify My Email</a>
                    </div>
                    
                    <hr class="divider">
                    
                    <p class="option-title">🔢 Option 2: Enter This Code</p>
                    <p>If the button doesn\'t work, enter this verification code on the verification page:</p>
                    <div class="code-box">' . $code . '</div>
                    
                    <div class="warning">
                        <strong>⏰ Important:</strong> This verification link and code will expire in <strong>24 hours</strong>.
                    </div>
                    
                    <p style="color: #666; font-size: 14px;">If you didn\'t create an account with us, you can safely ignore this email.</p>
                </div>
                <div class="footer">
                    <p><strong>' . SITE_NAME . '</strong></p>
                    <p>Batangas State University TNEU - JPLPC Malvar</p>
                    <p>📧 <a href="mailto:' . SITE_EMAIL . '">' . SITE_EMAIL . '</a></p>
                    <p style="margin-top: 15px; font-size: 11px; color: #999;">
                        This is an automated message. Please do not reply to this email.
                    </p>
                </div>
            </div>
        </div>
    </body>
    </html>';
    
    $textBody = "Hello" . ($name ? " $name" : "") . "!\n\n";
    $textBody .= "Thank you for registering with " . SITE_NAME . "!\n\n";
    $textBody .= "Please verify your email address to activate your account.\n\n";
    $textBody .= "Option 1: Click this link:\n$verificationLink\n\n";
    $textBody .= "Option 2: Enter this code on the verification page:\n$code\n\n";
    $textBody .= "This verification expires in 24 hours.\n\n";
    $textBody .= "If you didn't create an account, please ignore this email.\n\n";
    $textBody .= "---\n" . SITE_NAME . "\nBatangas State University TNEU - JPLPC Malvar\nEmail: " . SITE_EMAIL;
    
    return sendEmail($email, $subject, $htmlBody, $textBody);
}
