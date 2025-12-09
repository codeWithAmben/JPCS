<?php
/**
 * Email Verification Page
 * Handles both link verification and code verification
 */
require_once 'config.php';

$message = '';
$messageType = '';
$showCodeForm = false;
$email = '';

// Handle verification link (token in URL)
if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = sanitize($_GET['token']);
    $result = verifyToken($token);
    
    if ($result['success']) {
        $message = $result['message'];
        $messageType = 'success';
    } else {
        $message = $result['message'];
        $messageType = 'error';
        $showCodeForm = true;
    }
}

// Handle code verification form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action === 'verify_code') {
            $email = sanitize($_POST['email'] ?? '');
            $code = sanitize($_POST['code'] ?? '');
            
            if (empty($email) || empty($code)) {
                $message = 'Please enter both email and verification code.';
                $messageType = 'error';
                $showCodeForm = true;
            } else {
                $result = verifyCode($email, $code);
                
                if ($result['success']) {
                    $message = $result['message'];
                    $messageType = 'success';
                } else {
                    $message = $result['message'];
                    $messageType = 'error';
                    $showCodeForm = true;
                }
            }
        } elseif ($action === 'resend') {
            $email = sanitize($_POST['email'] ?? '');
            
            if (empty($email)) {
                $message = 'Please enter your email address.';
                $messageType = 'error';
            } else {
                $result = resendVerification($email);
                $message = $result['message'];
                $messageType = $result['success'] ? 'success' : 'error';
            }
            $showCodeForm = true;
        }
    }
}

// Check for email parameter (from registration redirect)
if (isset($_GET['email'])) {
    $email = sanitize($_GET['email']);
    $showCodeForm = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/login.css">
    <style>
        .verify-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 40px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        
        .verify-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .verify-header .icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
        
        .verify-header h1 {
            color: #1a237e;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .verify-header p {
            color: #666;
        }
        
        .message {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            text-align: center;
        }
        
        .message.success {
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            color: #2e7d32;
            border: 1px solid #a5d6a7;
        }
        
        .message.error {
            background: linear-gradient(135deg, #ffebee, #ffcdd2);
            color: #c62828;
            border: 1px solid #ef9a9a;
        }
        
        .code-form {
            margin-top: 25px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .form-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #3949ab;
        }
        
        .code-input {
            text-align: center;
            font-size: 24px !important;
            letter-spacing: 8px;
            font-family: monospace;
        }
        
        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #1a237e, #3949ab);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(57, 73, 171, 0.4);
        }
        
        .btn-secondary {
            background: #f5f5f5;
            color: #333;
            margin-top: 10px;
        }
        
        .btn-secondary:hover {
            background: #e0e0e0;
            box-shadow: none;
        }
        
        .divider {
            display: flex;
            align-items: center;
            margin: 25px 0;
            color: #999;
        }
        
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #ddd;
        }
        
        .divider span {
            padding: 0 15px;
            font-size: 14px;
        }
        
        .success-actions {
            text-align: center;
            margin-top: 25px;
        }
        
        .success-actions a {
            display: inline-block;
            padding: 15px 30px;
            background: linear-gradient(135deg, #1a237e, #3949ab);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: transform 0.2s;
        }
        
        .success-actions a:hover {
            transform: translateY(-2px);
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: #3949ab;
            text-decoration: none;
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
        
        .timer {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-top: 15px;
        }
        
        .resend-form {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .resend-form p {
            text-align: center;
            color: #666;
            margin-bottom: 15px;
        }
    </style>
</head>
<body class="inner-page">

<header>
    <img src="assets/images/LOGO.png" class="logo" alt="JPCS Logo">
    <nav class="desktop-nav">
        <a href="index.php">Home</a>
        <a href="pages/about.php">About</a>
        <a href="login.php">Login</a>
    </nav>
</header>

<div class="verify-container">
    <div class="verify-header">
        <?php if ($messageType === 'success'): ?>
            <div class="icon">✅</div>
            <h1>Email Verified!</h1>
        <?php else: ?>
            <div class="icon">📧</div>
            <h1>Verify Your Email</h1>
            <p>Enter the 6-digit code sent to your email</p>
        <?php endif; ?>
    </div>
    
    <?php if ($message): ?>
        <div class="message <?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    
    <?php if ($messageType === 'success'): ?>
        <div class="success-actions">
            <a href="login.php">🔐 Continue to Login</a>
        </div>
    <?php endif; ?>
    
    <?php if ($showCodeForm || (!$message && !isset($_GET['token']))): ?>
        <form method="POST" class="code-form">
            <input type="hidden" name="action" value="verify_code">
            
            <div class="form-group">
                <label for="email">📧 Email Address</label>
                <input type="email" id="email" name="email" required 
                       placeholder="your.email@example.com"
                       value="<?php echo htmlspecialchars($email); ?>">
            </div>
            
            <div class="form-group">
                <label for="code">🔢 Verification Code</label>
                <input type="text" id="code" name="code" required 
                       class="code-input"
                       placeholder="000000"
                       maxlength="6"
                       pattern="[0-9]{6}">
            </div>
            
            <button type="submit" class="btn">✓ Verify Email</button>
        </form>
        
        <div class="resend-form">
            <p>Didn't receive the code?</p>
            <form method="POST">
                <input type="hidden" name="action" value="resend">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <button type="submit" class="btn btn-secondary">📤 Resend Verification Email</button>
            </form>
        </div>
    <?php endif; ?>
    
    <div class="back-link">
        <a href="index.php">← Back to Home</a>
    </div>
</div>

<?php include 'includes/tawk_chat.php'; ?>

</body>
</html>
