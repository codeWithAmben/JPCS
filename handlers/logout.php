<?php
require_once '../config.php';

logoutUser();
// Prevent caching and force a fresh page load to avoid stale JS/CSS state
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
setFlash('success', 'You have been logged out successfully');
// Remove session cookie
if (ini_get('session.use_cookies')) {
	$params = session_get_cookie_params();
	setcookie(session_name(), '', time() - 42000,
		$params['path'], $params['domain'], $params['secure'], $params['httponly']
	);
}
redirect(SITE_URL . '/login.php');
?>
