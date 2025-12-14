<?php
// Run this script from the command line or browser to test email sending
// Usage: php scripts/test_email.php your-email@example.com

require_once __DIR__ . '/../config.php';

echo "Testing Email Configuration...\n";
echo "SMTP Host: " . (defined('SMTP_HOST') ? SMTP_HOST : 'Not set') . "\n";
echo "SMTP User: " . (defined('SMTP_USER') ? SMTP_USER : 'Not set') . "\n";

$to = isset($argv[1]) ? $argv[1] : 'test@example.com'; // Default or command line arg

echo "Sending test email to: $to\n";

if (function_exists('sendEmail')) {
    $result = sendEmail($to, 'Test Email from JPCS', '<h1>It Works!</h1><p>This is a test email from the JPCS system to verify SMTP configuration.</p>');
    if ($result['success']) {
        echo "SUCCESS: Email sent successfully.\n";
    } else {
        echo "ERROR: Failed to send email. " . $result['message'] . "\n";
    }
} else {
    echo "ERROR: sendEmail function not found. Check includes/mailer.php.\n";
}
?>