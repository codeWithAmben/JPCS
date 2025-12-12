<?php
require_once '../config.php';
requireAdmin();

$action = $_GET['action'] ?? 'list';
$officerId = $_GET['id'] ?? null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $uploadResult = null;
                if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
                    $uploadResult = uploadFile($_FILES['photo'], 'profiles');
                }
                
                $data = [
                    'name' => sanitize($_POST['name']),
                    'position' => sanitize($_POST['position']),
                    'category' => sanitize($_POST['category']),
                    'order' => sanitize($_POST['order']),
                    'email' => sanitize($_POST['email']),
                    'bio' => sanitize($_POST['bio']),
                    'photo' => $uploadResult['success'] ? $uploadResult['filename'] : 'default.jpg',
                    'status' => sanitize($_POST['status']),
                    'term_start' => sanitize($_POST['term_start']),
                    'term_end' => sanitize($_POST['term_end'])
                ];
                
                if (createOfficer($data)) {
                    setFlash('Officer added successfully!', 'success');
                } else {
                    setFlash('Failed to add officer.', 'error');
                }
                redirect('officers.php');
                break;
                
            case 'edit':
                $data = [
                    'name' => sanitize($_POST['name']),
                    'position' => sanitize($_POST['position']),
                    'category' => sanitize($_POST['category']),
                    'order' => sanitize($_POST['order']),
                    'email' => sanitize($_POST['email']),
                    'bio' => sanitize($_POST['bio']),
                    'status' => sanitize($_POST['status']),
                    'term_start' => sanitize($_POST['term_start']),
                    'term_end' => sanitize($_POST['term_end'])
                ];
                
                if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
                    $uploadResult = uploadFile($_FILES['photo'], 'profiles');
                    if ($uploadResult['success']) {
                        $data['photo'] = $uploadResult['filename'];
                    }
                }
                
                if (updateOfficer($_POST['id'], $data)) {
                    setFlash('Officer updated successfully!', 'success');
                } else {
                    setFlash('Failed to update officer.', 'error');
                }
                redirect('officers.php');
                break;
                
            case 'delete':
                if (deleteOfficer($_POST['id'])) {
                    setFlash('Officer deleted successfully!', 'success');
                } else {
                    setFlash('Failed to delete officer.', 'error');
                }
                redirect('officers.php');
                break;
        }
    }
}

$officers = getAllOfficers();
$currentOfficer = null;
if ($action === 'edit' && $officerId) {
    $currentOfficer = getOfficerById($officerId);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Officers - Admin</title>
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
            <!-- List View -->
            <div class="page-header">
                <h1>Manage Officers</h1>
                <a href="?action=add" class="btn btn-primary">
                    <span>➕</span> Add New Officer
                </a>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Position</th>
                                    <th>Category</th>
                                    <th>Email</th>
                                    <th>Term</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($officers)): ?>
                                    <?php foreach ($officers as $officer): ?>
                                    <tr>
                                        <td data-label="Order"><strong><?php echo htmlspecialchars($officer['order']); ?></strong></td>
                                        <td data-label="Photo">
                                            <img src="../assets/profiles/<?php echo htmlspecialchars($officer['photo']); ?>" 
                                                 alt="<?php echo htmlspecialchars($officer['name']); ?>"
                                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                                        </td>
                                        <td data-label="Name"><strong><?php echo htmlspecialchars($officer['name']); ?></strong></td>
                                        <td data-label="Position"><?php echo htmlspecialchars($officer['position']); ?></td>
                                        <td data-label="Category">
                                            <?php 
                                            $category = $officer['category'];
                                            $badgeClass = $category === 'Executive' ? 'badge-info' : 
                                                        ($category === 'Director' ? 'badge-success' : 'badge-warning');
                                            echo '<span class="badge ' . $badgeClass . '">' . $category . '</span>';
                                            ?>
                                        </td>
                                        <td data-label="Email"><?php echo htmlspecialchars($officer['email']); ?></td>
                                        <td data-label="Term"><?php echo formatDate($officer['term_start'], 'Y') . ' - ' . formatDate($officer['term_end'], 'Y'); ?></td>
                                        <td data-label="Status">
                                            <?php 
                                            $status = $officer['status'];
                                            $badgeClass = $status === 'active' ? 'badge-success' : 'badge-secondary';
                                            echo '<span class="badge ' . $badgeClass . '">' . strtoupper($status) . '</span>';
                                            ?>
                                        </td>
                                        <td class="actions">
                                            <a href="?action=edit&id=<?php echo $officer['id']; ?>" class="btn-sm btn-info">Edit</a>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this officer?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $officer['id']; ?>">
                                                <button type="submit" class="btn-sm btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" style="text-align: center; padding: 40px;">No officers found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <?php elseif ($action === 'add' || $action === 'edit'): ?>
            <!-- Add/Edit Form -->
            <div class="page-header">
                <h1><?php echo $action === 'add' ? 'Add New Officer' : 'Edit Officer'; ?></h1>
                <a href="officers.php" class="btn btn-secondary">
                    <span>←</span> Back to List
                </a>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" class="form-horizontal">
                        <input type="hidden" name="action" value="<?php echo $action; ?>">
                        <?php if ($action === 'edit'): ?>
                            <input type="hidden" name="id" value="<?php echo $currentOfficer['id']; ?>">
                        <?php endif; ?>
                        
                        <h3>Officer Information</h3>
                        <div class="form-group">
                            <label>Full Name *</label>
                            <input type="text" name="name" required 
                                   value="<?php echo $currentOfficer['name'] ?? ''; ?>">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Position *</label>
                                <input type="text" name="position" required 
                                       value="<?php echo $currentOfficer['position'] ?? ''; ?>"
                                       placeholder="e.g., President, VP Internal">
                            </div>
                            
                            <div class="form-group">
                                <label>Category *</label>
                                <select name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="Executive" <?php echo ($currentOfficer['category'] ?? '') === 'Executive' ? 'selected' : ''; ?>>Executive</option>
                                    <option value="Governor" <?php echo ($currentOfficer['category'] ?? '') === 'Governor' ? 'selected' : ''; ?>>Governor</option>
                                    <option value="Director" <?php echo ($currentOfficer['category'] ?? '') === 'Director' ? 'selected' : ''; ?>>Director</option>
                                    <option value="Committee" <?php echo ($currentOfficer['category'] ?? '') === 'Committee' ? 'selected' : ''; ?>>Committee</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Display Order * (Lower numbers appear first)</label>
                                <input type="number" name="order" required min="1" 
                                       value="<?php echo $currentOfficer['order'] ?? ''; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label>Email *</label>
                                <input type="email" name="email" required 
                                       value="<?php echo $currentOfficer['email'] ?? ''; ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Biography *</label>
                            <textarea name="bio" rows="4" required><?php echo $currentOfficer['bio'] ?? ''; ?></textarea>
                        </div>
                        
                        <h3>Photo</h3>
                        <div class="form-group">
                            <label>Profile Photo <?php echo $action === 'edit' ? '(Leave blank to keep current)' : '*'; ?></label>
                            <input type="file" name="photo" accept="image/*" <?php echo $action === 'add' ? 'required' : ''; ?>>
                            <?php if ($action === 'edit' && !empty($currentOfficer['photo'])): ?>
                                <div style="margin-top: 10px;">
                                    <img src="../assets/profiles/<?php echo htmlspecialchars($currentOfficer['photo']); ?>" 
                                         alt="Current Photo" 
                                         style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">
                                    <p style="margin-top: 5px; color: #7f8c8d; font-size: 0.85rem;">Current Photo</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <h3>Term Period</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Term Start *</label>
                                <input type="date" name="term_start" required 
                                       value="<?php echo $currentOfficer['term_start'] ?? ''; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label>Term End *</label>
                                <input type="date" name="term_end" required 
                                       value="<?php echo $currentOfficer['term_end'] ?? ''; ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Status *</label>
                            <select name="status" required>
                                <option value="active" <?php echo ($currentOfficer['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo ($currentOfficer['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <?php echo $action === 'add' ? 'Add Officer' : 'Update Officer'; ?>
                            </button>
                            <a href="officers.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
