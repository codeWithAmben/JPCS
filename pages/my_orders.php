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
</head>
<body class="inner-page">

<?php include '../includes/header.php'; ?>

<section style="max-width:1000px;margin:40px auto;padding:20px;">
    <h1>My Orders</h1>
    <?php if (empty($myOrders)): ?>
        <p>You haven't placed any orders yet.</p>
    <?php else: ?>
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr><th>Order ID</th><th>Items</th><th>Total</th><th>Payment</th><th>Status</th></tr>
            </thead>
            <tbody>
                <?php foreach ($myOrders as $o): ?>
                    <tr style="border-top:1px solid #eee;">
                        <td><?php echo htmlspecialchars($o['id']); ?></td>
                        <td>
                            <ul style="margin:0;padding-left:16px;">
                                <?php foreach ($o['items'] as $it): ?>
                                    <li><?php echo htmlspecialchars($it['name']) . ' × ' . (int)$it['quantity']; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                        <td>₱<?php echo number_format((float)$o['total'],2); ?></td>
                        <td><?php echo htmlspecialchars($o['payment_method']) . ' (' . htmlspecialchars($o['payment_status']) . ')'; ?></td>
                        <td><?php echo htmlspecialchars($o['status']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<footer>
    <p>&copy; 2025 JPCS Malvar Chapter. All Rights Reserved.</p>
</footer>
</body>
</html>
