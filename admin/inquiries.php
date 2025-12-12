<?php
require_once '../config.php';
requireAdmin();

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_status') {
        if (updateInquiryStatus($_POST['id'], $_POST['status'])) {
            setFlash('Inquiry status updated successfully!', 'success');
        } else {
            setFlash('Failed to update inquiry status.', 'error');
        }
        redirect('inquiries.php');
    }
}

$inquiries = getAllInquiries();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Inquiries - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/topbar.php'; ?>
        
        <div class="content-wrapper">
            <?php displayFlash(); ?>
            
            <div class="page-header">
                <h1>Help Desk Inquiries</h1>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Subject</th>
                                    <th>Message</th>
                                    <th>Submitted</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($inquiries)): ?>
                                    <?php foreach ($inquiries as $inquiry): ?>
                                    <tr>
                                        <td data-label="Name"><strong><?php echo htmlspecialchars($inquiry['name']); ?></strong></td>
                                        <td data-label="Email"><?php echo htmlspecialchars($inquiry['email']); ?></td>
                                        <td data-label="Subject"><?php echo htmlspecialchars($inquiry['subject']); ?></td>
                                        <td data-label="Message"><?php echo substr(htmlspecialchars($inquiry['message']), 0, 100) . '...'; ?></td>
                                        <td data-label="Submitted"><?php echo formatDate($inquiry['submitted_date']); ?></td>
                                        <td data-label="Status">
                                            <?php 
                                            $status = $inquiry['status'];
                                            $badgeClass = $status === 'replied' ? 'badge-success' : 
                                                        ($status === 'pending' ? 'badge-warning' : 'badge-info');
                                            echo '<span class="badge ' . $badgeClass . '">' . strtoupper($status) . '</span>';
                                            ?>
                                        </td>
                                        <td class="actions">
                                            <?php if ($inquiry['status'] === 'pending'): ?>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="update_status">
                                                <input type="hidden" name="id" value="<?php echo $inquiry['id']; ?>">
                                                <input type="hidden" name="status" value="replied">
                                                <button type="submit" class="btn-sm btn-info">Mark Replied</button>
                                            </form>
                                            <?php else: ?>
                                                <span style="color: #95a5a6;">Completed</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" style="text-align: center; padding: 40px;">No inquiries found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
