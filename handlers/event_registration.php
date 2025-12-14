<?php
require_once '../config.php';
requireLogin();

$action = $_POST['action'] ?? '';
$redirect = $_POST['redirect'] ?? rtrim(SITE_URL, '/') . '/member/events.php';

$user = getCurrentUser();
$member = getMemberByUserId($user['id']);

if ($action === 'register') {
    $eventId = $_POST['event_id'] ?? '';
    
    if (!$member) {
        setFlash('Please complete your membership registration first.', 'error');
        header('Location: ' . $redirect);
        exit;
    }
    
    if (empty($eventId)) {
        setFlash('Invalid event.', 'error');
        header('Location: ' . $redirect);
        exit;
    }
    
    $event = getEventById($eventId);
    if (!$event) {
        setFlash('Event not found.', 'error');
        header('Location: ' . $redirect);
        exit;
    }
    
    // Check if event is full
    $registrations = getEventRegistrationsByEventId($eventId);
    if ($event['max_participants'] > 0 && count($registrations) >= $event['max_participants']) {
        setFlash('Sorry, this event is full.', 'error');
        header('Location: ' . $redirect);
        exit;
    }
    
    // Check if already registered
    if (isUserRegisteredForEvent($user['id'], $eventId)) {
        setFlash('You are already registered for this event.', 'error');
        header('Location: ' . $redirect);
        exit;
    }
    
    // Check if event date has passed
    if (strtotime($event['date']) < time()) {
        setFlash('This event has already passed.', 'error');
        header('Location: ' . $redirect);
        exit;
    }
    
    // Register for event
    $registrationFee = floatval($event['registration_fee']);
    $registrationId = registerForEvent($eventId, $user['id'], $member['id'], $registrationFee);
    
    if ($registrationId) {
        $message = 'Successfully registered for ' . htmlspecialchars($event['title']) . '!';
        if ($registrationFee > 0) {
            $message .= ' Payment of ₱' . number_format($registrationFee, 2) . ' is required.';
        }
        setFlash($message, 'success');
    } else {
        setFlash('Failed to register for event.', 'error');
    }
    
} elseif ($action === 'cancel') {
    $registrationId = $_POST['registration_id'] ?? '';
    
    if (empty($registrationId)) {
        setFlash('Invalid registration.', 'error');
        header('Location: ' . $redirect);
        exit;
    }
    
    if (cancelEventRegistration($registrationId, $user['id'])) {
        setFlash('Event registration cancelled successfully.', 'success');
    } else {
        setFlash('Failed to cancel registration. You may have already attended this event.', 'error');
    }
    
} else {
    setFlash('Invalid action.', 'error');
}

header('Location: ' . $redirect);
exit;
