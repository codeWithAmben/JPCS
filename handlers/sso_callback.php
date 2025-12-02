<?php
require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$idToken = $data['credential'] ?? '';

if (empty($idToken)) {
    echo json_encode(['success' => false, 'message' => 'No credential provided']);
    exit;
}

$result = handleSSOLogin($idToken);

if ($result['success']) {
    $user = $result['user'];
    $redirect = SITE_URL . '/member/dashboard.php';
    
    if ($user['role'] === ROLE_ADMIN || $user['role'] === ROLE_OFFICER) {
        $redirect = SITE_URL . '/admin/dashboard.php';
    }
    
    echo json_encode([
        'success' => true,
        'redirect' => $redirect,
        'user' => [
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ]
    ]);
} else {
    echo json_encode($result);
}
?>
