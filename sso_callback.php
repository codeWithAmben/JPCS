<?php
/**
 * Google OAuth Callback Handler
 * Handles the redirect from Google after authentication
 */

require_once 'config.php';
require_once 'includes/google_oauth.php';

// Check for errors from Google
if (isset($_GET['error'])) {
    setFlash('Google login failed: ' . htmlspecialchars($_GET['error']), 'error');
    redirect('login.php');
    exit;
}

// Check for authorization code
if (!isset($_GET['code'])) {
    setFlash('Invalid OAuth response', 'error');
    redirect('login.php');
    exit;
}

$code = $_GET['code'];

try {
    // Initialize Google OAuth
    $google = new GoogleOAuth();
    
    // Authenticate with the code
    $result = $google->authenticate($code);
    
    if (!$result['success']) {
        setFlash('Authentication failed: ' . $result['error'], 'error');
        redirect('login.php');
        exit;
    }
    
    $googleUser = $result['user'];
    
    // Handle Google OAuth login (create or update user)
    $loginResult = handleGoogleOAuthLogin($googleUser);
    
    if (!$loginResult['success']) {
        setFlash('Failed to process login: ' . $loginResult['error'], 'error');
        redirect('login.php');
        exit;
    }
    
    $user = $loginResult['user'];
    
    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['first_name'] = $user['first_name'] ?? '';
    $_SESSION['last_name'] = $user['last_name'] ?? '';
    $_SESSION['logged_in'] = true;
    $_SESSION['login_method'] = 'google';
    
    // Show welcome message
    if ($loginResult['is_new']) {
        setFlash('Welcome to JPCS Malvar! Your account has been created successfully.', 'success');
    } else {
        setFlash('Welcome back, ' . htmlspecialchars($user['first_name'] ?: $user['username']) . '!', 'success');
    }
    
    // Redirect based on role
    switch ($user['role']) {
        case 'admin':
            redirect('admin/dashboard.php');
            break;
        case 'officer':
            redirect('member/dashboard.php');
            break;
        case 'member':
        default:
            redirect('member/dashboard.php');
            break;
    }
    
} catch (Exception $e) {
    error_log('SSO Error: ' . $e->getMessage());
    setFlash('An error occurred during login. Please try again.', 'error');
    redirect('login.php');
}
