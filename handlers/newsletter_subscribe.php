<?php
require_once '../config.php';
require_once '../includes/db_helper.php';

header('Content-Type: application/json');

$email = trim($_POST['email'] ?? '');
if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please provide a valid email.']);
    exit;
}

if (addNewsletterSubscriber($email)) {
    // Send welcome email
    $subject = "Welcome to JPCS Malvar Newsletter";
    $body = "
        <h2>Welcome to JPCS Malvar Chapter!</h2>
        <p>Thank you for subscribing to our newsletter.</p>
        <p>You will now receive updates about our latest events, workshops, and announcements.</p>
        <br>
        <p>Best regards,<br>JPCS Malvar Team</p>
    ";
    
    // Attempt to send email (non-blocking for user response if possible, but here synchronous)
    sendEmail($email, $subject, $body);

    echo json_encode(['success' => true, 'message' => 'Subscribed successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to subscribe. Try again later.']);
}

exit;
