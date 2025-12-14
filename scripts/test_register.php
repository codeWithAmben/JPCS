<?php
// Simple test runner for registration handler
chdir(__DIR__ . '/../handlers');

$_POST = [
    'first_name' => 'Test',
    'last_name' => 'User',
    'email' => 'test_user_' . time() . '@example.com',
    'phone' => '09171234567',
    'birthdate' => '2000-01-01',
    'gender' => 'Other',
    'address' => '123 Test St',
    'school' => 'Test University',
    'course' => 'Testing',
    'year_level' => '1',
    'student_id' => 'STU' . rand(1000,9999),
    'password' => 'Password123!'
];

// Simulate POST request
$_SERVER['REQUEST_METHOD'] = 'POST';

ob_start();
require 'register.php';
$out = ob_get_clean();
echo "Handler output:\n" . $out . "\n";