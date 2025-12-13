<?php
require_once '../config.php';
require_once '../includes/db_helper.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Collect data
$payment_method = sanitize($_POST['payment_method'] ?? 'onsite');
$cartJson = $_POST['cart'] ?? '[]';
$cart = json_decode($cartJson, true) ?: [];

$user_id = $_SESSION['user_id'] ?? null;
$name = sanitize($_POST['name'] ?? '');
$email = sanitize($_POST['email'] ?? '');
$phone = sanitize($_POST['phone'] ?? '');

if (!$user_id && (empty($name) || empty($email))) {
    echo json_encode(['success' => false, 'message' => 'Please provide name and email or login first']);
    exit;
}

if (empty($cart)) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit;
}

// Validate items and compute total
$total = 0;
$items = [];
foreach ($cart as $it) {
    $product = getProductById($it['product_id']);
    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found: ' . htmlspecialchars($it['product_id'])]);
        exit;
    }
    $qty = max(1, (int)($it['quantity'] ?? 1));
    if ((int)$product['stock'] < $qty) {
        echo json_encode(['success' => false, 'message' => 'Not enough stock for ' . htmlspecialchars($product['name'])]);
        exit;
    }
    $price = (float)$product['price'];
    $items[] = ['product_id' => $product['id'], 'name' => $product['name'], 'price' => $price, 'quantity' => $qty];
    $total += $price * $qty;
}

// Handle receipt upload (GCASH)
$payment_info = '';
if ($payment_method === 'gcash' && isset($_FILES['gcash_receipt']) && $_FILES['gcash_receipt']['error'] === UPLOAD_ERR_OK) {
    $upload = handleFileUpload($_FILES['gcash_receipt'], 'payments');
    if ($upload['success']) {
        $payment_info = $upload['path'];
    }
}

// Decide payment_status: mark 'paid' if gcash receipt uploaded or method onsite (optional)
$payment_status = 'pending';
if ($payment_method === 'onsite') {
    // Optionally, mark as pending; here we keep 'pending' until staff marks paid
    $payment_status = 'pending';
}

$orderData = [
    'user_id' => $user_id ?? '',
    'items' => $items,
    'total' => number_format($total, 2, '.', ''),
    'payment_method' => $payment_method,
    'payment_status' => $payment_status,
    'payment_info' => $payment_info,
    'status' => 'processing'
];

$orderId = createOrder($orderData);
if (!$orderId) {
    echo json_encode(['success' => false, 'message' => 'Failed to create order']);
    exit;
}

// Note: Stock is decremented when payment is confirmed (admin/webhook marks 'paid')

// Optionally, send confirmation email
// TODO: Implement mailer for order confirmation

// Return success and redirect to order success page
echo json_encode(['success' => true, 'message' => 'Order created', 'order_id' => $orderId, 'redirect' => SITE_URL . '/pages/order_success.php?order_id=' . urlencode($orderId)]);
exit;
?>
