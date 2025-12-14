<?php
/**
 * Google OAuth Helper Functions
 */

require_once __DIR__ . '/env_loader.php';

class GoogleOAuth {
    private $clientId;
    private $clientSecret;
    private $redirectUri;
    private $authUrl = 'https://accounts.google.com/o/oauth2/v2/auth';
    private $tokenUrl = 'https://oauth2.googleapis.com/token';
    private $userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo';
    
    public function __construct() {
        $this->clientId = env('GOOGLE_CLIENT_ID');
        $this->clientSecret = env('GOOGLE_CLIENT_SECRET');
        $this->redirectUri = env('GOOGLE_REDIRECT_URI');
    }
    
    /**
     * Generate the Google OAuth login URL
     */
    public function getAuthUrl($state = null) {
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => 'email profile',
            'access_type' => 'online',
            'prompt' => 'select_account'
        ];
        
        if ($state) {
            $params['state'] = $state;
        }
        
        return $this->authUrl . '?' . http_build_query($params);
    }
    
    /**
     * Exchange authorization code for access token
     */
    public function getAccessToken($code) {
        $params = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri,
            'grant_type' => 'authorization_code',
            'code' => $code
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->tokenUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);
        
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            return ['error' => $error];
        }
        
        return json_decode($response, true);
    }
    
    /**
     * Get user info using access token
     */
    public function getUserInfo($accessToken) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->userInfoUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken
        ]);
        
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            return ['error' => $error];
        }
        
        return json_decode($response, true);
    }
    
    /**
     * Complete OAuth flow - get user info from authorization code
     */
    public function authenticate($code) {
        // Get access token
        $tokenData = $this->getAccessToken($code);
        
        if (isset($tokenData['error'])) {
            return [
                'success' => false,
                'error' => $tokenData['error_description'] ?? $tokenData['error']
            ];
        }
        
        if (!isset($tokenData['access_token'])) {
            return [
                'success' => false,
                'error' => 'Failed to get access token'
            ];
        }
        
        // Get user info
        $userInfo = $this->getUserInfo($tokenData['access_token']);
        
        if (isset($userInfo['error'])) {
            return [
                'success' => false,
                'error' => $userInfo['error']['message'] ?? 'Failed to get user info'
            ];
        }
        
        return [
            'success' => true,
            'user' => [
                'google_id' => $userInfo['id'],
                'email' => $userInfo['email'],
                'name' => $userInfo['name'],
                'first_name' => $userInfo['given_name'] ?? '',
                'last_name' => $userInfo['family_name'] ?? '',
                'picture' => $userInfo['picture'] ?? '',
                'verified_email' => $userInfo['verified_email'] ?? false
            ],
            'access_token' => $tokenData['access_token']
        ];
    }
}

/**
 * Handle Google OAuth user login/registration
 */
function handleGoogleOAuthLogin($googleUser) {
    require_once __DIR__ . '/db_helper.php';
    
    $email = $googleUser['email'];
    $googleId = $googleUser['google_id'];
    
    // Check if user exists by email
    $existingUser = getUserByEmail($email);
    
    if ($existingUser) {
        // Update google_id if not set
        if (empty($existingUser['google_id'])) {
            updateUserGoogleId($existingUser['id'], $googleId);
        }
        
        // Return existing user
        return [
            'success' => true,
            'user' => $existingUser,
            'is_new' => false
        ];
    }
    
    // Create new user
    $userData = [
        'username' => generateUsernameFromEmail($email),
        'email' => $email,
        'password' => password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT), // Random password
        'first_name' => $googleUser['first_name'],
        'last_name' => $googleUser['last_name'],
        'role' => 'member',
        'status' => 'active',
        'google_id' => $googleId,
        'profile_photo' => $googleUser['picture']
    ];
    
    $userId = createUserSSO($userData);
    
    if ($userId) {
        $newUser = getUserById($userId);
        // Send welcome email for SSO-created account (only if available)
        if (function_exists('sendSSOWelcomeEmail')) {
            $sent = false;
            try {
                $sent = sendSSOWelcomeEmail($newUser['email'], $newUser['first_name'] . ' ' . $newUser['last_name']);
            } catch (Throwable $t) {
                error_log('Failed to send SSO welcome email (throwable): ' . $t->getMessage());
            }
            if ($sent) {
                error_log('SSO welcome email sent to ' . $newUser['email']);
            } else {
                error_log('SSO welcome email not sent to ' . $newUser['email']);
            }
        } else {
            error_log('sendSSOWelcomeEmail function not available.');
        }
        return [
            'success' => true,
            'user' => $newUser,
            'is_new' => true
        ];
    }
    
    return [
        'success' => false,
        'error' => 'Failed to create user account'
    ];
}

/**
 * Generate username from email
 */
function generateUsernameFromEmail($email) {
    $username = explode('@', $email)[0];
    $username = preg_replace('/[^a-zA-Z0-9]/', '', $username);
    
    // Check if username exists, append number if needed
    $baseUsername = $username;
    $counter = 1;
    
    while (getUserByUsername($username)) {
        $username = $baseUsername . $counter;
        $counter++;
    }
    
    return $username;
}
