<?php
require_once 'config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    $user = getCurrentUser();
    if (hasRole(ROLE_ADMIN) || hasRole(ROLE_OFFICER)) {
        redirect(SITE_URL . '/admin/dashboard.php');
    } else {
        redirect(SITE_URL . '/member/dashboard.php');
    }
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        setFlash('error', 'Please provide both email and password');
    } else {
        $result = loginUser($email, $password);
        
        if ($result['success']) {
            $redirectUrl = $_SESSION['redirect_after_login'] ?? SITE_URL . '/member/dashboard.php';
            unset($_SESSION['redirect_after_login']);
            
            // Redirect based on role
            if (hasRole(ROLE_ADMIN) || hasRole(ROLE_OFFICER)) {
                $redirectUrl = SITE_URL . '/admin/dashboard.php';
            }
            
            redirect($redirectUrl);
        } else {
            // Check if needs email verification
            if (isset($result['needs_verification']) && $result['needs_verification']) {
                redirect(SITE_URL . '/verify.php?email=' . urlencode($result['email']));
            }
            setFlash('error', $result['message']);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/style.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/login.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-left">
            <img src="<?php echo SITE_URL; ?>/assets/images/LOGO.png" alt="JPCS Logo" class="login-logo">
            <h1>Welcome to JPCS Malvar Chapter</h1>
            <p>Login to access your member dashboard, register for events, and stay connected with the community.</p>
            <p><strong>Not a member yet?</strong> Join us and be part of something great!</p>
            <a href="<?php echo SITE_URL; ?>/pages/membership.php" class="login-learn-link">Learn about membership →</a>
        </div>
        
        <div class="login-right">
            <h2>Login</h2>
            <p class="login-subtitle">Enter your credentials to continue</p>
            
            <?php displayFlash(); ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required placeholder="your.email@example.com">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="••••••••">
                </div>
                
                <button type="submit" class="btn-login">Login</button>
            </form>
            
            <div class="divider"><span>OR</span></div>
            
            <div class="sso-buttons">
                <a href="<?php echo SITE_URL; ?>/sso_login.php" class="btn-sso btn-google">
                    <svg width="20" height="20" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Continue with Google
                </a>
            </div>
            
            <div class="login-footer">
                <p>Don't have an account? <a href="<?php echo SITE_URL; ?>/pages/registration.php">Register here</a></p>
                <p><a href="<?php echo SITE_URL; ?>/index.php">← Back to home</a></p>
            </div>
        </div>
    </div>

<?php include 'includes/tawk_chat.php'; ?>

</body>
</html>

<script>
// Ensure any open mobile navs / dropdowns are reset when arriving at login page
document.addEventListener('DOMContentLoaded', function() {
    const dropdown = document.getElementById('dropdownMenu');
    if (dropdown) dropdown.classList.remove('active');
    const mobileNav = document.getElementById('mobileNav');
    if (mobileNav) mobileNav.classList.remove('active');
    const mobileNavOverlay = document.getElementById('mobileNavOverlay');
    if (mobileNavOverlay) mobileNavOverlay.classList.remove('active');
    const hamburger = document.getElementById('hamburger');
    if (hamburger) hamburger.classList.remove('active');
    // Reset body overflow just in case
    document.body.style.overflow = '';
    // Make sure page is scrollable on small screens
    document.body.style.overflowY = 'auto';
    // Focus first input so virtual keyboards appear properly on mobile
    const email = document.getElementById('email');
    if (email) {
        try { email.focus(); } catch(e) {}
    }
});
</script>
