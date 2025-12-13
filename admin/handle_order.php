<?php
require_once '../config.php';
require_once '../includes/db_helper.php';
require_once '../includes/auth.php';

// Only admin/officer
if (empty($_SESSION['user_role']) || !in_array($_SESSION['user_role'], [ROLE_ADMIN, ROLE_OFFICER])) {
    redirect(SITE_URL . '/login.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(SITE_URL . '/admin/orders.php');
}

$orderId = $_POST['order_id'] ?? '';
$action = $_POST['action'] ?? '';

if (empty($orderId) || empty($action)) {
    redirect(SITE_URL . '/admin/orders.php');
}

if ($action === 'mark_paid') {
    $order = getOrderById($orderId);
    if ($order && ($order['payment_status'] ?? '') !== 'paid') {
        // Check stock availability again before marking paid
        $stockProblem = false;
        foreach ($order['items'] as $it) {
            $product = getProductById($it['product_id']);
            if ($product && (int)$product['stock'] < (int)$it['quantity']) {
                $stockProblem = true;
                break;
            }
        }
        if ($stockProblem) {
            setFlash('error', 'Cannot mark paid: insufficient stock for one or more items.');
        } else {
            updateOrder($orderId, ['payment_status' => 'paid']);
            // Decrement stock for items
            foreach ($order['items'] as $it) {
                $product = getProductById($it['product_id']);
                if ($product) {
                    $newStock = max(0, (int)$product['stock'] - (int)$it['quantity']);
                    updateProduct($product['id'], ['stock' => $newStock]);
                }
            }
            setFlash('success', 'Order marked as paid and stock updated.');
        }
    }
}

if ($action === 'mark_completed') {
    updateOrder($orderId, ['status' => 'completed']);
}

redirect(SITE_URL . '/admin/orders.php');
?>