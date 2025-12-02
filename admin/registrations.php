<?php
require_once '../config.php';
requireAdmin();

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_status') {
        if (updateRegistrationStatus($_POST['id'], $_POST['status'])) {
            setFlash('Registration status updated successfully!', 'success');
        } else {
            setFlash('Failed to update registration status.', 'error');
        }
        redirect('registrations.php');
    }
}

$registrations = getAllRegistrations();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Registrations - Admin</title>
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
                <h1>Registration Applications</h1>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>School</th>
                                    <th>Course</th>
                                    <th>Year</th>
                                    <th>Submitted</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($registrations)): ?>
                                    <?php foreach ($registrations as $reg): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($reg['first_name'] . ' ' . $reg['last_name']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($reg['email']); ?></td>
                                        <td><?php echo htmlspecialchars($reg['phone']); ?></td>
                                        <td><?php echo htmlspecialchars($reg['school']); ?></td>
                                        <td><?php echo htmlspecialchars($reg['course']); ?></td>
                                        <td><?php echo htmlspecialchars($reg['year_level']); ?></td>
                                        <td><?php echo formatDate($reg['submitted_date']); ?></td>
                                        <td>
                                            <?php 
                                            $status = $reg['status'];
                                            $badgeClass = $status === 'approved' ? 'badge-success' : 
                                                        ($status === 'pending' ? 'badge-warning' : 'badge-danger');
                                            echo '<span class="badge ' . $badgeClass . '">' . strtoupper($status) . '</span>';
                                            ?>
                                        </td>
                                        <td class="actions">
                                            <?php if ($reg['status'] === 'pending'): ?>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="update_status">
                                                <input type="hidden" name="id" value="<?php echo $reg['id']; ?>">
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="btn-sm" style="background: #27ae60;">Approve</button>
                                            </form>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="update_status">
                                                <input type="hidden" name="id" value="<?php echo $reg['id']; ?>">
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="btn-sm btn-danger">Reject</button>
                                            </form>
                                            <?php else: ?>
                                                <span style="color: #95a5a6;">Processed</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" style="text-align: center; padding: 40px;">No registrations found.</td>
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
