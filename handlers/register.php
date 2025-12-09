<?php
/**
 * Registration Handler with Email Verification
 * Creates user account with pending status and sends verification email
 */
require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Validate input
$requiredFields = ['first_name', 'last_name', 'email', 'phone', 'birthdate', 'gender', 
                   'address', 'school', 'course', 'year_level', 'student_id', 'password'];

$data = [];
$errors = [];

foreach ($requiredFields as $field) {
    $value = sanitize($_POST[$field] ?? '');
    if (empty($value)) {
        $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
    }
    $data[$field] = $value;
}

// Optional fields
$data['middle_name'] = sanitize($_POST['middle_name'] ?? '');
$data['alt_phone'] = sanitize($_POST['alt_phone'] ?? '');
$data['skills'] = sanitize($_POST['skills'] ?? '');
$data['motivation'] = sanitize($_POST['motivation'] ?? '');

// Validate email
if (!validateEmail($data['email'])) {
    $errors[] = 'Invalid email format';
}

// Check if email already exists
$existingUser = getUserByEmail($data['email']);
if ($existingUser) {
    // Check if unverified - allow re-registration
    if (isset($existingUser['status']) && $existingUser['status'] === 'pending') {
        // Resend verification
        $result = resendVerification($data['email']);
        echo json_encode([
            'success' => true,
            'message' => 'A verification email has been resent to your email address.',
            'redirect' => SITE_URL . '/verify.php?email=' . urlencode($data['email'])
        ]);
        exit;
    }
    $errors[] = 'An account with this email already exists';
}

// Validate password strength
if (strlen($data['password']) < 8) {
    $errors[] = 'Password must be at least 8 characters long';
}

// Validate phone
if (!validatePhone($data['phone'])) {
    $errors[] = 'Invalid phone number format';
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// Create user account with verification
$result = createUserWithVerification([
    'email' => $data['email'],
    'password' => $data['password'],
    'first_name' => $data['first_name'],
    'last_name' => $data['last_name'],
    'phone' => $data['phone']
]);

if (!$result['success']) {
    echo json_encode(['success' => false, 'message' => $result['message']]);
    exit;
}

// Create member record with pending status
$data['user_id'] = $result['user_id'];
$memberId = createMember($data, $result['user_id']);

if (!$memberId) {
    // Member record failed but user account exists - that's okay, they can complete profile later
    error_log("Member record creation failed for user: " . $result['user_id']);
}

echo json_encode([
    'success' => true,
    'message' => $result['message'],
    'redirect' => SITE_URL . '/verify.php?email=' . urlencode($data['email']),
    'member_id' => $memberId ?? null,
    // Include code for testing purposes (remove in production)
    'verification_code' => $result['verification_code'] ?? null
]);
?>
