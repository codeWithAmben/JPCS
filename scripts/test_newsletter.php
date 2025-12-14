<?php
// Simple CLI test runner for newsletter_subscribe handler
$_POST = ['email' => 'cli_test_' . time() . '@example.com'];
ob_start();
// Simulate running from the handlers directory so relative requires work
chdir(__DIR__ . '/../handlers');
require 'newsletter_subscribe.php';
$output = ob_get_clean();
echo "Handler output: \n" . $output . "\n";