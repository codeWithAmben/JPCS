<?php
// End-to-end test: register -> verify -> login
chdir(__DIR__ . '/../handlers');

// Prepare registration payload
$email = 'e2e_test_' . time() . '@example.com';
$password = 'Password123!';
$_POST = [
    'first_name' => 'E2E',
    'last_name' => 'Tester',
    'email' => $email,
    'phone' => '09170000000',
    'birthdate' => '2000-01-01',
    'gender' => 'Other',
    'address' => 'Test Address',
    'school' => 'Test School',
    'course' => 'Testing',
    'year_level' => '1',
    'student_id' => 'E2E' . rand(1000,9999),
    'password' => $password
];
$_SERVER['REQUEST_METHOD'] = 'POST';

ob_start();
require 'register.php';
$out = ob_get_clean();
echo "Register handler output:\n" . $out . "\n";
$result = json_decode($out, true);
if (!$result || !$result['success']) {
    echo "Registration failed\n";
    exit(1);
}

// Find the latest verification token for this email
require_once __DIR__ . '/../config.php';
$xml = simplexml_load_file(DB_VERIFICATIONS);
$token = null;
foreach ($xml->verification as $v) {
    if ((string)$v->email === $email) {
        $token = (string)$v->token;
    }
}
if (!$token) {
    echo "No verification token found for $email\n";
    exit(1);
}

echo "Found token: $token\n";

// Verify token
require_once INCLUDES_PATH . '/email_verification.php';
$verifyResult = verifyToken($token);
echo "verifyToken result: "; var_export($verifyResult); echo "\n";

if (!$verifyResult['success']) {
    echo "Verification failed\n";
    exit(1);
}

// Try to login
require_once INCLUDES_PATH . '/auth.php';
$login = loginUser($email, $password);
echo "loginUser result: "; var_export($login); echo "\n";

if ($login['success']) {
    echo "E2E Verification test: SUCCESS\n";
    exit(0);
} else {
    echo "E2E Verification test: LOGIN FAILED\n";
    exit(2);
}
