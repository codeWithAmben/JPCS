<?php
require_once '../config.php';
requireAdmin();

$action = $_GET['action'] ?? 'list';
$announcementId = $_GET['id'] ?? null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $data = [
                    'title' => sanitize($_POST['title']),
                    'content' => sanitize($_POST['content']),
                    'badge' => sanitize($_POST['badge']),
                    'status' => sanitize($_POST['status'])
                ];
                
                if (createAnnouncement($data)) {
                    setFlash('Announcement created successfully!', 'success');
                } else {
                    setFlash('Failed to create announcement.', 'error');
                }
                redirect('announcements.php');
                break;
                
            case 'edit':
                $data = [
                    'title' => sanitize($_POST['title']),
                    'content' => sanitize($_POST['content']),
                    'badge' => sanitize($_POST['badge']),
                    'status' => sanitize($_POST['status'])
                ];
                
                if (updateAnnouncement($_POST['id'], $data)) {
                    setFlash('Announcement updated successfully!', 'success');
                } else {
                    setFlash('Failed to update announcement.', 'error');
                }
                redirect('announcements.php');
                break;
                
            case 'delete':
                if (deleteAnnouncement($_POST['id'])) {
                    setFlash('Announcement deleted successfully!', 'success');
                } else {
                    setFlash('Failed to delete announcement.', 'error');
                }
                redirect('announcements.php');
                break;
        }
    }
}

$announcements = getAllAnnouncements();
$currentAnnouncement = null;
if ($action === 'edit' && $announcementId) {
    $currentAnnouncement = getAnnouncementById($announcementId);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Announcements - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/topbar.php'; ?>
        
        <div class="content-wrapper">
            <?php displayFlash(); ?>
            
            <?php if ($action === 'list'): ?>
            <div class="page-header">
                <h1>Manage Announcements</h1>
                <a href="?action=add" class="btn btn-primary">
                    <span>➕</span> Create Announcement
                </a>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Badge</th>
                                    <th>Posted Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($announcements)): ?>
                                    <?php foreach ($announcements as $announcement): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($announcement['title']); ?></strong></td>
                                        <td>
                                            <?php if ($announcement['badge']): ?>
                                                <span class="badge badge-warning"><?php echo htmlspecialchars($announcement['badge']); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo formatDate($announcement['posted_date'], 'M j, Y g:i A'); ?></td>
                                        <td>
                                            <?php 
                                            $status = $announcement['status'];
                                            $badgeClass = $status === 'active' ? 'badge-success' : 'badge-secondary';
                                            echo '<span class="badge ' . $badgeClass . '">' . strtoupper($status) . '</span>';
                                            ?>
                                        </td>
                                        <td class="actions">
                                            <a href="?action=edit&id=<?php echo $announcement['id']; ?>" class="btn-sm btn-info">Edit</a>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $announcement['id']; ?>">
                                                <button type="submit" class="btn-sm btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center; padding: 40px;">No announcements found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <?php elseif ($action === 'add' || $action === 'edit'): ?>
            <div class="page-header">
                <h1><?php echo $action === 'add' ? 'Create Announcement' : 'Edit Announcement'; ?></h1>
                <a href="announcements.php" class="btn btn-secondary">
                    <span>←</span> Back to List
                </a>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <form method="POST" class="form-horizontal">
                        <input type="hidden" name="action" value="<?php echo $action; ?>">
                        <?php if ($action === 'edit'): ?>
                            <input type="hidden" name="id" value="<?php echo $currentAnnouncement['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label>Title *</label>
                            <input type="text" name="title" required 
                                   value="<?php echo $currentAnnouncement['title'] ?? ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Content *</label>
                            <textarea name="content" rows="8" required><?php echo $currentAnnouncement['content'] ?? ''; ?></textarea>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Badge (Optional)</label>
                                <select name="badge">
                                    <option value="">No Badge</option>
                                    <option value="NEW" <?php echo ($currentAnnouncement['badge'] ?? '') === 'NEW' ? 'selected' : ''; ?>>NEW</option>
                                    <option value="URGENT" <?php echo ($currentAnnouncement['badge'] ?? '') === 'URGENT' ? 'selected' : ''; ?>>URGENT</option>
                                    <option value="IMPORTANT" <?php echo ($currentAnnouncement['badge'] ?? '') === 'IMPORTANT' ? 'selected' : ''; ?>>IMPORTANT</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Status *</label>
                                <select name="status" required>
                                    <option value="active" <?php echo ($currentAnnouncement['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="archived" <?php echo ($currentAnnouncement['status'] ?? '') === 'archived' ? 'selected' : ''; ?>>Archived</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <?php echo $action === 'add' ? 'Create Announcement' : 'Update Announcement'; ?>
                            </button>
                            <a href="announcements.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
