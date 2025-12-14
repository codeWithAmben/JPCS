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
    echo json_encode(['success' => true, 'message' => 'Subscribed successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to subscribe. Try again later.']);
}

exit;

