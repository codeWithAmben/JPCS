<?php
require_once '../config.php';
require_once '../includes/db_helper.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

try {
    if (!isLoggedIn()) {
        http_response_code(401); // Unauthorized
        echo json_encode(['success' => false, 'message' => 'You must be logged in to register.']);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405); // Method Not Allowed
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        exit;
    }

    $eventId = sanitizeInput($_POST['event_id'] ?? '');
    $userId = $_SESSION['user_id'];

    // 1. Input Validation
    if (empty($eventId) || strpos($eventId, 'evt_') !== 0) {
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'message' => 'A valid Event ID is required.']);
        exit;
    }

    // Check if event exists
    $event = getEventById($eventId);
    if (!$event) {
        http_response_code(404); // Not Found
        echo json_encode(['success' => false, 'message' => 'Event not found.']);
        exit;
    }

    // Check if already registered
    if (isUserRegisteredForEvent($userId, $eventId)) {
        http_response_code(409); // Conflict
        echo json_encode(['success' => false, 'message' => 'You are already registered for this event.']);
        exit;
    }

    // Get member info
    $member = getMemberByUserId($userId);
    $memberId = $member ? $member['id'] : '';

    // Handle Payment
    $registrationFee = (float)($event['registration_fee'] ?? 0);
    $paymentProof = '';

    if ($registrationFee > 0) {
        if (!isset($_FILES['payment_proof']) || $_FILES['payment_proof']['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Payment proof is required for paid events.']);
            exit;
        }

        // 3. File Upload Security (handled by improved function)
        $upload = handleFileUpload($_FILES['payment_proof'], 'payments');
        if (!$upload['success']) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Failed to upload payment proof: ' . $upload['message']]);
            exit;
        }
        $paymentProof = $upload['path'];
    }

    // Register
    $regId = registerForEvent($eventId, $userId, $memberId, $registrationFee, $paymentProof);

    if ($regId) {
        echo json_encode(['success' => true, 'message' => 'Registration successful! Your registration is now pending for verification.']);
    } else {
        // 2. Error Handling
        error_log("Failed to save event registration for user_id: {$userId} and event_id: {$eventId}");
        http_response_code(500); // Internal Server Error
        echo json_encode(['success' => false, 'message' => 'An internal error occurred. Failed to save registration. Please try again later.']);
    }
} catch (Exception $e) {
    error_log("Unhandled exception in event_register.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred.']);
}