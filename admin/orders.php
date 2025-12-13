<?php
require_once '../config.php';
require_once '../includes/db_helper.php';
require_once '../includes/auth.php';

// Only admin or officer
if (empty($_SESSION['user_role']) || !in_array($_SESSION['user_role'], [ROLE_ADMIN, ROLE_OFFICER])) {
    redirect(SITE_URL . '/login.php');
}

$orders = getAllOrders();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Orders</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<section style="max-width:1100px;margin:40px auto;padding:20px;">
    <h1>Orders</h1>
    <?php if (empty($orders)): ?>
        <p>No orders yet.</p>
    <?php else: ?>
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr><th>Order ID</th><th>Items</th><th>Total</th><th>Payment</th><th>Status</th><th>Action</th></tr>
            </thead>
            <tbody>
            <?php foreach ($orders as $o): ?>
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
                    <td>
                            <?php echo htmlspecialchars($o['payment_method']) . ' (' . htmlspecialchars($o['payment_status']) . ')'; ?>
                            <?php if (!empty($o['payment_info'])): ?>
                                <div><a href="../<?php echo htmlspecialchars($o['payment_info']); ?>" target="_blank">View Receipt</a></div>
                            <?php endif; ?>
                        </td>
                    <td><?php echo htmlspecialchars($o['status']); ?></td>
                    <td>
                        <form method="post" action="handle_order.php" style="display:inline;">
                            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($o['id']); ?>">
                            <input type="hidden" name="action" value="mark_paid">
                            <button type="submit" class="btn-small">Mark Paid</button>
                        </form>
                        <form method="post" action="handle_order.php" style="display:inline;margin-left:6px;">
                            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($o['id']); ?>">
                            <input type="hidden" name="action" value="mark_completed">
                            <button type="submit" class="btn-small">Complete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
</body>
</html>
