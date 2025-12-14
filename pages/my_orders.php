<?php
require_once '../config.php';
require_once '../includes/db_helper.php';
require_once '../includes/functions.php';

requireLogin();
$user = getCurrentUser();
$allOrders = getAllOrders();
$myOrders = array_filter($allOrders, function($o) use($user){ return $o['user_id'] === $user['id']; });
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - JPCS.Mart</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/pages.css">
</head>
<body class="inner-page">

<?php include '../includes/header.php'; ?>

<section class="page-section">
    <h1 class="page-title">My Orders</h1>
    <?php if (empty($myOrders)): ?>
        <div class="card">
            <p>You haven't placed any orders yet.</p>
        </div>
    <?php else: ?>
        <div class="card">
        <table class="data-table" style="width:100%;border-collapse:collapse;">
            <thead>
                <tr><th>Order ID</th><th>Items</th><th>Total</th><th>Payment</th><th>Status</th></tr>
            </thead>
            <tbody>
                <?php foreach ($myOrders as $o): ?>
                    <tr style="border-top:1px solid #eee;">
                        <td data-label="Order ID"><?php echo htmlspecialchars($o['id']); ?></td>
                        <td data-label="Items">
                            <ul class="order-items">
                                <?php foreach ($o['items'] as $it): ?>
                                    <li><?php echo htmlspecialchars($it['name']) . ' × ' . (int)$it['quantity']; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                        <td data-label="Total">₱<?php echo number_format((float)$o['total'],2); ?></td>
                        <td data-label="Payment">
                            <?php $payCls = 'badge-' . preg_replace('/[^a-z0-9]+/','-', strtolower($o['payment_status'] ?? '')); ?>
                            <span class="badge <?php echo $payCls; ?>"><?php echo htmlspecialchars(ucfirst($o['payment_status'])); ?></span>
                            <div style="margin-top:6px; font-size:0.9rem; color:var(--text-muted)"><?php echo htmlspecialchars($o['payment_method']); ?></div>
                        </td>
                        <td data-label="Status">
                            <?php $statusCls = 'badge-' . preg_replace('/[^a-z0-9]+/','-', strtolower($o['status'] ?? '')); ?>
                            <span class="badge <?php echo $statusCls; ?>"><?php echo htmlspecialchars(ucfirst($o['status'])); ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
</section>

<footer>
    <p>&copy; 2025 JPCS Malvar Chapter. All Rights Reserved.</p>
</footer>
</body>
</html>
