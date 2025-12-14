<?php
require_once __DIR__ . '/../config.php';

$email = 'unverified_' . time() . '@example.com';
$password = 'Password123!';

// Create user with verification
$result = createUserWithVerification([
    'email' => $email,
    'password' => $password,
    'first_name' => 'Unverified',
    'last_name' => 'User',
    'phone' => '09170000000'
]);

var_export($result);

auth_result:
$login = loginUser($email, $password);
var_export($login);

echo PHP_EOL;