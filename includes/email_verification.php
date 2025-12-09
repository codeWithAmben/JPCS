<?php
/**
 * Email Verification System
 * Handles email verification workflow: send link/code → verify → activate
 */

/**
 * Generate a secure verification token
 */
function generateVerificationToken() {
    return bin2hex(random_bytes(32));
}

/**
 * Generate a 6-digit verification code
 */
function generateVerificationCode() {
    return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

/**
 * Create verification record in database
 */
function createVerificationRecord($userId, $email, $type = 'registration') {
    $token = generateVerificationToken();
    $code = generateVerificationCode();
    $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
    
    $xml = loadXML(DB_VERIFICATIONS);
    if (!$xml) {
        // Create new XML file if it doesn't exist
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><verifications></verifications>');
    }
    
    // Remove any existing verification for this user/email
    $toRemove = [];
    $i = 0;
    foreach ($xml->verification as $verification) {
        if ((string)$verification->email === $email && (string)$verification->type === $type) {
            $toRemove[] = $i;
        }
        $i++;
    }
    // Remove in reverse order to maintain indexes
    foreach (array_reverse($toRemove) as $index) {
        unset($xml->verification[$index]);
    }
    
    // Add new verification record
    $verification = $xml->addChild('verification');
    $verification->addChild('id', generateUniqueId('ver_'));
    $verification->addChild('user_id', $userId);
    $verification->addChild('email', $email);
    $verification->addChild('token', $token);
    $verification->addChild('code', $code);
    $verification->addChild('type', $type);
    $verification->addChild('verified', 'false');
    $verification->addChild('created_at', date('Y-m-d H:i:s'));
    $verification->addChild('expires_at', $expiresAt);
    
    if (saveXML($xml, DB_VERIFICATIONS)) {
        return [
            'token' => $token,
            'code' => $code,
            'expires_at' => $expiresAt
        ];
    }
    
    return false;
}

/**
 * Verify token from email link
 */
function verifyToken($token) {
    $xml = loadXML(DB_VERIFICATIONS);
    if (!$xml) return ['success' => false, 'message' => 'Verification system unavailable'];
    
    foreach ($xml->verification as $verification) {
        if ((string)$verification->token === $token) {
            // Check if already verified
            if ((string)$verification->verified === 'true') {
                return ['success' => false, 'message' => 'Email already verified'];
            }
            
            // Check if expired
            if (strtotime((string)$verification->expires_at) < time()) {
                return ['success' => false, 'message' => 'Verification link has expired. Please request a new one.'];
            }
            
            // Mark as verified
            $verification->verified = 'true';
            $verification->verified_at = date('Y-m-d H:i:s');
            
            // Activate user account
            $userId = (string)$verification->user_id;
            if (activateUserAccount($userId)) {
                saveXML($xml, DB_VERIFICATIONS);
                return [
                    'success' => true,
                    'message' => 'Email verified successfully! Your account is now active.',
                    'user_id' => $userId
                ];
            }
            
            return ['success' => false, 'message' => 'Failed to activate account'];
        }
    }
    
    return ['success' => false, 'message' => 'Invalid verification token'];
}

/**
 * Verify code entered by user
 */
function verifyCode($email, $code) {
    $xml = loadXML(DB_VERIFICATIONS);
    if (!$xml) return ['success' => false, 'message' => 'Verification system unavailable'];
    
    foreach ($xml->verification as $verification) {
        if ((string)$verification->email === $email && (string)$verification->code === $code) {
            // Check if already verified
            if ((string)$verification->verified === 'true') {
                return ['success' => false, 'message' => 'Email already verified'];
            }
            
            // Check if expired
            if (strtotime((string)$verification->expires_at) < time()) {
                return ['success' => false, 'message' => 'Verification code has expired. Please request a new one.'];
            }
            
            // Mark as verified
            $verification->verified = 'true';
            $verification->verified_at = date('Y-m-d H:i:s');
            
            // Activate user account
            $userId = (string)$verification->user_id;
            if (activateUserAccount($userId)) {
                saveXML($xml, DB_VERIFICATIONS);
                return [
                    'success' => true,
                    'message' => 'Email verified successfully! Your account is now active.',
                    'user_id' => $userId
                ];
            }
            
            return ['success' => false, 'message' => 'Failed to activate account'];
        }
    }
    
    return ['success' => false, 'message' => 'Invalid verification code'];
}

/**
 * Activate user account after verification
 */
function activateUserAccount($userId) {
    $xml = loadXML(DB_USERS);
    if (!$xml) return false;
    
    foreach ($xml->user as $user) {
        if ((string)$user->id === $userId) {
            $user->status = 'active';
            $user->email_verified = 'true';
            $user->email_verified_at = date('Y-m-d H:i:s');
            return saveXML($xml, DB_USERS);
        }
    }
    return false;
}

/**
 * Check if user's email is verified
 */
function isEmailVerified($userId) {
    $xml = loadXML(DB_USERS);
    if (!$xml) return false;
    
    foreach ($xml->user as $user) {
        if ((string)$user->id === $userId) {
            return (string)$user->email_verified === 'true';
        }
    }
    return false;
}

/**
 * Get pending verification by email
 */
function getPendingVerification($email) {
    $xml = loadXML(DB_VERIFICATIONS);
    if (!$xml) return null;
    
    foreach ($xml->verification as $verification) {
        if ((string)$verification->email === $email && 
            (string)$verification->verified === 'false' &&
            strtotime((string)$verification->expires_at) > time()) {
            return [
                'id' => (string)$verification->id,
                'user_id' => (string)$verification->user_id,
                'email' => (string)$verification->email,
                'code' => (string)$verification->code,
                'expires_at' => (string)$verification->expires_at
            ];
        }
    }
    return null;
}

/**
 * Send verification email with link and code
 * Uses PHPMailer for reliable email delivery
 */
function sendVerificationEmail($email, $token, $code, $name = '') {
    // Use PHPMailer if available
    if (function_exists('sendVerificationEmailPHPMailer')) {
        $result = sendVerificationEmailPHPMailer($email, $token, $code, $name);
        return $result['success'];
    }
    
    // Fallback to basic mail() function
    $verificationLink = SITE_URL . '/verify.php?token=' . $token;
    
    $subject = 'Verify Your Email - ' . SITE_NAME;
    
    $message = '
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #1a237e, #3949ab); padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .header h1 { color: white; margin: 0; }
            .content { background: #f9f9f9; padding: 30px; border: 1px solid #ddd; }
            .code-box { background: #1a237e; color: white; font-size: 32px; letter-spacing: 8px; padding: 20px; text-align: center; border-radius: 8px; margin: 20px 0; font-family: monospace; }
            .btn { display: inline-block; background: #3949ab; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
            .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
            .divider { border-top: 1px solid #ddd; margin: 20px 0; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>📧 Email Verification</h1>
            </div>
            <div class="content">
                <p>Hello' . ($name ? ' <strong>' . htmlspecialchars($name) . '</strong>' : '') . ',</p>
                <p>Thank you for registering with <strong>' . SITE_NAME . '</strong>! Please verify your email address to activate your account.</p>
                
                <h3>Option 1: Click the Verification Link</h3>
                <p style="text-align: center;">
                    <a href="' . $verificationLink . '" class="btn" style="color: white;">✓ Verify My Email</a>
                </p>
                
                <div class="divider"></div>
                
                <h3>Option 2: Enter Verification Code</h3>
                <p>Enter this code on the verification page:</p>
                <div class="code-box">' . $code . '</div>
                
                <div class="divider"></div>
                
                <p><strong>⏰ This verification expires in 24 hours.</strong></p>
                <p>If you did not create an account, please ignore this email.</p>
            </div>
            <div class="footer">
                <p>&copy; ' . date('Y') . ' ' . SITE_NAME . ' | Batangas State University TNEU - JPLPC Malvar</p>
                <p>Email: ' . SITE_EMAIL . '</p>
            </div>
        </div>
    </body>
    </html>';
    
    // Email headers
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: ' . SITE_NAME . ' <' . SITE_EMAIL . '>',
        'Reply-To: ' . SITE_EMAIL,
        'X-Mailer: PHP/' . phpversion()
    ];
    
    // Send email
    $sent = @mail($email, $subject, $message, implode("\r\n", $headers));
    
    // Log email attempt (for debugging)
    $logFile = BASE_PATH . '/logs/email_log.txt';
    $logDir = dirname($logFile);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $logEntry = date('Y-m-d H:i:s') . " | To: $email | Subject: $subject | Status: " . ($sent ? 'SENT' : 'FAILED') . "\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    
    return $sent;
}

/**
 * Resend verification email
 */
function resendVerification($email) {
    // Check if there's an existing pending verification
    $pending = getPendingVerification($email);
    
    if ($pending) {
        // Get user info
        $xml = loadXML(DB_USERS);
        $name = '';
        foreach ($xml->user as $user) {
            if ((string)$user->id === $pending['user_id']) {
                $name = (string)$user->name;
                break;
            }
        }
        
        // Create new verification record (refreshes token and code)
        $verification = createVerificationRecord($pending['user_id'], $email, 'registration');
        if ($verification) {
            sendVerificationEmail($email, $verification['token'], $verification['code'], $name);
            return ['success' => true, 'message' => 'Verification email resent successfully'];
        }
    }
    
    return ['success' => false, 'message' => 'No pending verification found for this email'];
}

/**
 * Create user with pending verification status
 */
function createUserWithVerification($data) {
    $xml = loadXML(DB_USERS);
    if (!$xml) {
        // Create new XML file if it doesn't exist
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><users></users>');
    }
    
    // Check if user exists
    if (getUserByEmail($data['email'])) {
        return ['success' => false, 'message' => 'Email already registered'];
    }
    
    $userId = generateUniqueId('user_');
    $name = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
    
    $user = $xml->addChild('user');
    $user->addChild('id', $userId);
    $user->addChild('email', $data['email']);
    $user->addChild('password', hashPassword($data['password']));
    $user->addChild('first_name', $data['first_name'] ?? '');
    $user->addChild('last_name', $data['last_name'] ?? '');
    $user->addChild('name', $name);
    $user->addChild('phone', $data['phone'] ?? '');
    $user->addChild('role', ROLE_MEMBER);
    $user->addChild('status', 'pending'); // Pending until email verified
    $user->addChild('email_verified', 'false');
    $user->addChild('created_at', date('Y-m-d H:i:s'));
    
    if (!saveXML($xml, DB_USERS)) {
        return ['success' => false, 'message' => 'Failed to create account'];
    }
    
    // Create verification record
    $verification = createVerificationRecord($userId, $data['email'], 'registration');
    if (!$verification) {
        return ['success' => false, 'message' => 'Failed to create verification'];
    }
    
    // Send verification email
    sendVerificationEmail($data['email'], $verification['token'], $verification['code'], $name);
    
    return [
        'success' => true,
        'message' => 'Account created! Please check your email to verify your account.',
        'user_id' => $userId,
        'verification_code' => $verification['code'] // For testing/display purposes
    ];
}
