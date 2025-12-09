<?php
/**
 * JPCS Malvar Chapter - Configuration File
 * Core configuration and settings
 */

// Error Reporting (Set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Asia/Manila');

// Site Configuration
define('SITE_NAME', 'JPCS Malvar Chapter');
define('SITE_URL', 'http://localhost/JPCS');
define('SITE_EMAIL', 'jpcs.malvar@g.batstate-u.edu.ph');

// Paths
define('BASE_PATH', __DIR__);
define('INCLUDES_PATH', BASE_PATH . '/includes');
define('DATABASE_PATH', BASE_PATH . '/database');
define('UPLOADS_PATH', BASE_PATH . '/uploads');

// Database Files (XML)
define('DB_USERS', DATABASE_PATH . '/users.xml');
define('DB_MEMBERS', DATABASE_PATH . '/members.xml');
define('DB_EVENTS', DATABASE_PATH . '/events.xml');
define('DB_ANNOUNCEMENTS', DATABASE_PATH . '/announcements.xml');
define('DB_PRODUCTS', DATABASE_PATH . '/products.xml');
define('DB_GALLERY', DATABASE_PATH . '/gallery.xml');
define('DB_REGISTRATIONS', DATABASE_PATH . '/registrations.xml');
define('DB_NEWSLETTER', DATABASE_PATH . '/newsletter.xml');
define('DB_INQUIRIES', DATABASE_PATH . '/inquiries.xml');
define('DB_OFFICERS', DATABASE_PATH . '/officers.xml');
define('DB_EVENT_REGISTRATIONS', DATABASE_PATH . '/event_registrations.xml');
define('DB_VERIFICATIONS', DATABASE_PATH . '/verifications.xml');

// Session Configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
session_name('JPCS_SESSION');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// User Roles
define('ROLE_ADMIN', 'admin');
define('ROLE_OFFICER', 'officer');
define('ROLE_MEMBER', 'member');

// SSO Configuration (Example - Google OAuth)
define('SSO_CLIENT_ID', 'your-google-client-id.apps.googleusercontent.com');
define('SSO_CLIENT_SECRET', 'your-google-client-secret');
define('SSO_REDIRECT_URI', SITE_URL . '/handlers/sso_callback.php');

// Default Admin Account
define('DEFAULT_ADMIN_EMAIL', 'admin@jpcs-malvar.edu.ph');
define('DEFAULT_ADMIN_PASSWORD', 'Admin@2025'); // Change this in production!

// Membership Fee
define('MEMBERSHIP_FEE', 500);

// File Upload Settings
define('MAX_UPLOAD_SIZE', 5242880); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif']);

// Pagination
define('ITEMS_PER_PAGE', 10);

// Include required files
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/db_helper.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/mailer.php';
require_once INCLUDES_PATH . '/email_verification.php';

// Initialize database if not exists
initializeDatabase();
?>
