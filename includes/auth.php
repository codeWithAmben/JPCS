<?php
/**
 * Authentication Functions
 */

/**
 * Login user with email and password
 */
function loginUser($email, $password) {
    $user = getUserByEmail($email);
    
    if (!$user) {
        return ['success' => false, 'message' => 'Invalid email or password'];
    }
    
    // Check if account is pending verification
    if ($user['status'] === 'pending') {
        return [
            'success' => false, 
            'message' => 'Please verify your email before logging in. Check your email (including spam) for the verification link or the 6-digit code.',
            'needs_verification' => true,
            'email' => $email
        ];
    }
    
    if ($user['status'] !== 'active') {
        return ['success' => false, 'message' => 'Account is not active'];
    }
    
    // Check if email is verified (extra check for safety)
    if (isset($user['email_verified']) && $user['email_verified'] !== 'true') {
        return [
            'success' => false, 
            'message' => 'Please verify your email before logging in. Check your email (including spam) for the verification link or the 6-digit code.',
            'needs_verification' => true,
            'email' => $email
        ];
    }
    
    if (!verifyPassword($password, $user['password'])) {
        return ['success' => false, 'message' => 'Invalid email or password'];
    }
    
    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['login_time'] = time();
    
    return ['success' => true, 'user' => $user];
}

/**
 * Logout user
 */
function logoutUser() {
    session_unset();
    session_destroy();
    session_start();
}

/**
 * Handle SSO Login (Google OAuth)
 */
function handleSSOLogin($idToken) {
    // Verify the ID token with Google
    // This is a simplified example - use official Google API client library
    
    $url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . $idToken;
    $response = file_get_contents($url);
    $userData = json_decode($response, true);
    
    if (!$userData || !isset($userData['email'])) {
        return ['success' => false, 'message' => 'Invalid SSO token'];
    }
    
    $email = $userData['email'];
    $name = $userData['name'] ?? $email;
    
    // Check if user exists
    $user = getUserByEmail($email);
    
    if (!$user) {
        // Create new user
        $userId = createUser($email, bin2hex(random_bytes(16)), $name, ROLE_MEMBER);
        if (!$userId) {
            return ['success' => false, 'message' => 'Failed to create user account'];
        }
        $user = getUserById($userId);
    }
    
    if ($user['status'] !== 'active') {
        return ['success' => false, 'message' => 'Account is not active'];
    }
    
    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['login_time'] = time();
    $_SESSION['sso_login'] = true;
    
    return ['success' => true, 'user' => $user];
}

/**
 * Check if session is valid
 */
function isSessionValid() {
    if (!isLoggedIn()) {
        return false;
    }
    
    // Check session timeout (4 hours)
    $timeout = 4 * 60 * 60;
    if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > $timeout) {
        logoutUser();
        return false;
    }
    
    return true;
}
/**
 * Create a new user
 * @param string $email
 * @param string $password
 * @param string $name
 * @param string $role
 * @return int|false Returns user ID on success, false on failure
 */
function createUser($email, $password, $name, $role) {
    // Example implementation, replace with your actual database logic
    // Assuming you have a PDO connection $pdo
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO users (email, password, name, role, status, email_verified) VALUES (?, ?, ?, ?, 'active', 'true')");
    if ($stmt->execute([$email, password_hash($password, PASSWORD_DEFAULT), $name, $role])) {
        return $pdo->lastInsertId();
    }
    return false;
}
?>
