<?php
/**
 * Common Functions
 * Utility functions used throughout the application
 */

/**
 * Sanitize input data
 */
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Alias for sanitize function
 */
function sanitizeInput($data) {
    return sanitize($data);
}

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Validate phone number (Philippine format)
 */
function validatePhone($phone) {
    $pattern = '/^(\+63|0)?[0-9]{10}$/';
    return preg_match($pattern, str_replace([' ', '-', '(', ')'], '', $phone));
}

/**
 * Generate unique ID
 */
function generateUniqueId($prefix = '') {
    return $prefix . uniqid() . bin2hex(random_bytes(4));
}

/**
 * Format date for display
 */
function formatDate($date, $format = 'F j, Y') {
    if (empty($date) || $date === null) {
        return '';
    }
    $timestamp = strtotime($date);
    if ($timestamp === false || $timestamp === -1) {
        return '';
    }
    return date($format, $timestamp);
}

/**
 * Format time for display
 */
function formatTime($time, $format = 'g:i A') {
    if (empty($time) || $time === null) {
        return '';
    }
    $timestamp = strtotime($time);
    if ($timestamp === false || $timestamp === -1) {
        return '';
    }
    return date($format, $timestamp);
}

/**
 * Generate password hash
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

/**
 * Verify password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Redirect to page
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current user data
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    return getUserById($_SESSION['user_id']);
}

/**
 * Check if user has role
 */
function hasRole($role) {
    if (!isLoggedIn()) {
        return false;
    }
    $user = getCurrentUser();
    return $user && $user['role'] === $role;
}

/**
 * Require login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        redirect(SITE_URL . '/login.php');
    }
}

/**
 * Require admin role
 */
function requireAdmin() {
    requireLogin();
    if (!hasRole(ROLE_ADMIN) && !hasRole(ROLE_OFFICER)) {
        redirect(SITE_URL . '/member/dashboard.php');
    }
}

/**
 * Set flash message
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type, // success, error, warning, info
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 */
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Display flash message HTML
 */
function displayFlash() {
    $flash = getFlash();
    if ($flash) {
        $alertClass = [
            'success' => 'alert-success',
            'error' => 'alert-danger',
            'warning' => 'alert-warning',
            'info' => 'alert-info'
        ];
        $class = $alertClass[$flash['type']] ?? 'alert-info';
        
        echo '<div class="alert ' . $class . ' alert-dismissible fade show" role="alert">';
        echo htmlspecialchars($flash['message']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        echo '</div>';
    }
}

/**
 * Upload file
 */
function uploadFile($file, $destination) {
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'message' => 'Invalid file upload'];
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload error: ' . $file['error']];
    }
    
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return ['success' => false, 'message' => 'File too large. Maximum size: ' . (MAX_UPLOAD_SIZE / 1048576) . 'MB'];
    }
    
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    
    if (!in_array($mimeType, ALLOWED_IMAGE_TYPES)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $filepath = $destination . '/' . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => false, 'message' => 'Failed to save file'];
    }
    
    return ['success' => true, 'filename' => $filename, 'filepath' => $filepath];
}

/**
 * Handle file upload with proper directory creation and path handling
 */
function handleFileUpload($file, $subfolder = '') {
    // Check if file was uploaded
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'message' => 'Invalid file upload'];
    }
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload error: ' . $file['error']];
    }
    
    // Check file size (5MB max)
    $maxSize = 5 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => 'File too large. Maximum size: 5MB'];
    }
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    
    if (!in_array($mimeType, $allowedTypes)) {
        return ['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and GIF are allowed'];
    }
    
    // Create upload directory if it doesn't exist
    $uploadDir = '../uploads/' . ($subfolder ? $subfolder . '/' : '');
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $filepath = $uploadDir . $filename;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => false, 'message' => 'Failed to save file'];
    }
    
    // Return relative path for database storage
    $relativePath = 'uploads/' . ($subfolder ? $subfolder . '/' : '') . $filename;
    
    return ['success' => true, 'filename' => $filename, 'path' => $relativePath];
}

// sendEmail function moved to includes/mailer.php (uses PHPMailer)

/**
 * Get pagination data
 */
function paginate($totalItems, $currentPage = 1, $perPage = ITEMS_PER_PAGE) {
    $totalPages = ceil($totalItems / $perPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $perPage;
    
    return [
        'total_items' => $totalItems,
        'total_pages' => $totalPages,
        'current_page' => $currentPage,
        'per_page' => $perPage,
        'offset' => $offset,
        'has_previous' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages
    ];
}

/**
 * Initialize database XML files if they don't exist
 */
function initializeDatabase() {
    $databases = [
        DB_USERS => '<users></users>',
        DB_MEMBERS => '<members></members>',
        DB_EVENTS => '<events></events>',
        DB_ANNOUNCEMENTS => '<announcements></announcements>',
        DB_PRODUCTS => '<products></products>',
        DB_GALLERY => '<gallery></gallery>',
        DB_REGISTRATIONS => '<registrations></registrations>',
        DB_NEWSLETTER => '<newsletter></newsletter>',
        DB_INQUIRIES => '<inquiries></inquiries>',
        DB_ORDERS => '<orders></orders>'
    ];
    
    foreach ($databases as $file => $content) {
        if (!file_exists($file)) {
            file_put_contents($file, '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . $content);
        }
    }
    
    // Create default admin if users.xml is empty
    createDefaultAdmin();
}
?>
