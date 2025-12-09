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
    <link rel="stylesheet" href="css/style.css">
    <style>
        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 1000px;
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }
        
        .login-left {
            background: linear-gradient(135deg, #ff6a00, #ff8c42);
            padding: 60px 40px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-left h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            font-weight: 900;
        }
        
        .login-left p {
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        
        .login-right {
            padding: 60px 40px;
        }
        
        .login-right h2 {
            color: #333;
            margin-bottom: 10px;
            font-size: 2rem;
        }
        
        .login-subtitle {
            color: #666;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #ff6a00;
        }
        
        .btn-login {
            width: 100%;
            padding: 14px;
            background: #ff6a00;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            margin-bottom: 20px;
        }
        
        .btn-login:hover {
            background: #e05e00;
            transform: translateY(-2px);
        }
        
        .divider {
            text-align: center;
            margin: 25px 0;
            position: relative;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 40%;
            height: 1px;
            background: #ddd;
        }
        
        .divider::after {
            content: '';
            position: absolute;
            right: 0;
            top: 50%;
            width: 40%;
            height: 1px;
            background: #ddd;
        }
        
        .divider span {
            color: #999;
            font-weight: 600;
        }
        
        .sso-buttons {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .btn-sso {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            transition: 0.3s;
            font-weight: 600;
            color: #333;
        }
        
        .btn-sso:hover {
            border-color: #ff6a00;
            background: #fff8f2;
        }
        
        .btn-sso img {
            width: 20px;
            height: 20px;
        }
        
        .btn-google {
            text-decoration: none;
            font-size: 0.95rem;
        }
        
        .btn-google:hover {
            border-color: #4285F4;
            background: #f8f9ff;
        }
        
        .btn-google svg {
            flex-shrink: 0;
        }
        
        .login-footer {
            margin-top: 25px;
            text-align: center;
            color: #666;
        }
        
        .login-footer a {
            color: #ff6a00;
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-footer a:hover {
            text-decoration: underline;
        }
        
        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-danger {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }
        
        .alert-success {
            background: #efe;
            color: #3c3;
            border: 1px solid #cfc;
        }
        
        @media (max-width: 768px) {
            .login-container {
                grid-template-columns: 1fr;
            }
            
            .login-left {
                padding: 40px 30px;
            }
            
            .login-right {
                padding: 40px 30px;
            }
        }
    </style>
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-left">
            <img src="assets/images/LOGO.png" style="width: 80px; margin-bottom: 20px;" alt="JPCS Logo">
            <h1>Welcome to JPCS Malvar Chapter</h1>
            <p>Login to access your member dashboard, register for events, and stay connected with the community.</p>
            <p><strong>Not a member yet?</strong> Join us and be part of something great!</p>
            <a href="pages/membership.php" style="color: white; text-decoration: underline;">Learn about membership →</a>
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
                <a href="sso_login.php" class="btn-sso btn-google">
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
                <p>Don't have an account? <a href="pages/registration.php">Register here</a></p>
                <p><a href="index.php">← Back to home</a></p>
            </div>
        </div>
    </div>

<?php include 'includes/tawk_chat.php'; ?>

</body>
</html>
