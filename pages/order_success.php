<?php
require_once '../config.php';
require_once '../includes/db_helper.php';

$orderId = $_GET['order_id'] ?? '';
$order = null;
if ($orderId) {
    $order = getOrderById($orderId);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success - JPCS.Mart</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="inner-page">

<?php include '../includes/header.php'; ?>

<section style="max-width:800px;margin:40px auto;padding:20px;">
    <h1>Order Confirmation</h1>
    <?php if ($order): ?>
        <p>Order ID: <strong><?php echo htmlspecialchars($order['id']); ?></strong></p>
        <p>Status: <strong><?php echo htmlspecialchars(ucfirst($order['status'])); ?></strong></p>
        <p>Payment: <strong><?php echo htmlspecialchars(ucfirst($order['payment_method'])) . ' (' . htmlspecialchars(ucfirst($order['payment_status'])) . ')'; ?></strong></p>
        <h3>Items</h3>
        <ul>
            <?php foreach ($order['items'] as $it): ?>
                <li><?php echo htmlspecialchars($it['name']); ?> &times; <?php echo (int)$it['quantity']; ?> — ₱<?php echo number_format((float)$it['price'], 2); ?></li>
            <?php endforeach; ?>
        </ul>
        <p><strong>Total:</strong> ₱<?php echo number_format((float)$order['total'], 2); ?></p>
        <p>We've received your order. If you selected GCash and uploaded a receipt, it will be reviewed shortly. For Onsite/cash payments, a staff member will verify payment when collecting the order.</p>
        <a class="btn-primary" href="../pages/jpcsmart.php">Back to Shop</a>
    <?php else: ?>
        <p>Order not found. If you just placed an order and cannot see it, wait a few moments while the system completes processing.</p>
        <a class="btn-primary" href="../pages/jpcsmart.php">Back to Shop</a>
    <?php endif; ?>
</section>

<footer>
    <p>&copy; 2025 JPCS Malvar Chapter. All Rights Reserved.</p>
</footer>

</body>
</html>
