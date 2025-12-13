<?php
require_once '../config.php';
require_once '../includes/db_helper.php';

// Webhook should be protected with a secret
$secret = env('GCASH_WEBHOOK_SECRET', '');
$provided = $_SERVER['HTTP_X_GCASH_SIGNATURE'] ?? '';

if (!$secret || !$provided) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Signature missing or webhook secret not set']);
    exit;
}

$payload = file_get_contents('php://input');
$expected = hash_hmac('sha256', $payload, $secret);
if (!hash_equals($expected, $provided)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Signature mismatch']);
    exit;
}

$data = json_decode($payload, true);
if (!$data || !isset($data['order_id']) || !isset($data['status'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid payload']);
    exit;
}

$orderId = $data['order_id'];
$status = $data['status']; // e.g., 'paid'

if ($status === 'paid') {
    $order = getOrderById($orderId);
    if ($order && ($order['payment_status'] ?? '') !== 'paid') {
        // Check stock availability
        $stockProblem = false;
        foreach ($order['items'] as $it) {
            $product = getProductById($it['product_id']);
            if ($product && (int)$product['stock'] < (int)$it['quantity']) {
                $stockProblem = true;
                break;
            }
        }
        if ($stockProblem) {
            // Put order on hold for manual resolution
            updateOrder($orderId, ['status' => 'on-hold']);
            echo json_encode(['success' => false, 'message' => 'Insufficient stock, order put on-hold']);
            exit;
        }

        updateOrder($orderId, ['payment_status' => 'paid', 'status' => 'processing']);
        // Decrement stock
        foreach ($order['items'] as $it) {
            $product = getProductById($it['product_id']);
            if ($product) {
                $newStock = max(0, (int)$product['stock'] - (int)$it['quantity']);
                updateProduct($product['id'], ['stock' => $newStock]);
            }
        }
    }
    echo json_encode(['success' => true]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Unsupported status']);
exit;
?>
