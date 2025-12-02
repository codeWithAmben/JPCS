<?php
/**
 * Initiate Google SSO Login
 * Redirects user to Google OAuth consent screen
 */

require_once 'config.php';
require_once 'includes/google_oauth.php';

// Check if already logged in
if (isLoggedIn()) {
    $role = $_SESSION['role'] ?? 'member';
    if ($role === 'admin') {
        redirect('admin/dashboard.php');
    } else {
        redirect('member/dashboard.php');
    }
    exit;
}

// Generate state token for CSRF protection
$state = bin2hex(random_bytes(16));
$_SESSION['oauth_state'] = $state;

// Initialize Google OAuth and redirect
$google = new GoogleOAuth();
$authUrl = $google->getAuthUrl($state);

header('Location: ' . $authUrl);
exit;
