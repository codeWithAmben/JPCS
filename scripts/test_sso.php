<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/google_oauth.php';

$fake = [
    'google_id' => 'test_google_'.rand(1000,9999),
    'email' => 'sso_test_'.time().'@example.com',
    'name' => 'SSO Test',
    'first_name' => 'SSO',
    'last_name' => 'Test',
    'picture' => ''
];

$result = handleGoogleOAuthLogin($fake);
var_export($result);

echo PHP_EOL;