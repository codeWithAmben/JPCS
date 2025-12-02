<?php
require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Validate input
$requiredFields = ['first_name', 'last_name', 'email', 'phone', 'birthdate', 'gender', 
                   'address', 'school', 'course', 'year_level', 'student_id'];

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
if (getUserByEmail($data['email'])) {
    $errors[] = 'An account with this email already exists';
}

// Validate phone
if (!validatePhone($data['phone'])) {
    $errors[] = 'Invalid phone number format';
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// Create user account
$password = bin2hex(random_bytes(8)); // Generate temporary password
$fullName = $data['first_name'] . ' ' . $data['last_name'];
$userId = createUser($data['email'], $password, $fullName, ROLE_MEMBER);

if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'Failed to create user account']);
    exit;
}

// Create member record
$memberId = createMember($data, $userId);

if (!$memberId) {
    deleteUser($userId); // Rollback
    echo json_encode(['success' => false, 'message' => 'Failed to create member record']);
    exit;
}

// Send welcome email with temporary password
sendEmail(
    $data['email'],
    'Welcome to JPCS Malvar Chapter',
    "Your temporary password is: $password\nPlease login and change your password."
);

echo json_encode([
    'success' => true,
    'message' => 'Registration successful! Check your email for login credentials.',
    'member_id' => $memberId
]);
?>
