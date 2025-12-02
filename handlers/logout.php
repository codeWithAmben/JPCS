<?php
require_once '../config.php';

logoutUser();
setFlash('success', 'You have been logged out successfully');
redirect(SITE_URL . '/login.php');
?>
