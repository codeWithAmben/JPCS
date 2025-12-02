<?php
require_once '../config.php';
requireAdmin();

$action = $_GET['action'] ?? 'list';
$itemId = $_GET['id'] ?? null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'upload':
                $uploadDir = '../assets/uploads/gallery';
                // Ensure directory exists
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $uploadResult = uploadFile($_FILES['image'], $uploadDir);
                if ($uploadResult['success']) {
                    $data = [
                        'title' => sanitize($_POST['title']),
                        'description' => sanitize($_POST['description']),
                        'category' => sanitize($_POST['category']),
                        'image' => $uploadResult['filename'],
                        'status' => 'active'
                    ];
                    
                    if (createGalleryItem($data)) {
                        setFlash('Photo uploaded successfully!', 'success');
                    } else {
                        setFlash('Failed to save photo details.', 'error');
                    }
                } else {
                    setFlash($uploadResult['error'] ?? $uploadResult['message'], 'error');
                }
                redirect('gallery.php');
                break;
                
            case 'delete':
                if (deleteGalleryItem($_POST['id'])) {
                    setFlash('Photo deleted successfully!', 'success');
                } else {
                    setFlash('Failed to delete photo.', 'error');
                }
                redirect('gallery.php');
                break;
        }
    }
}

$galleryItems = getAllGalleryItems();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Gallery - Admin</title>
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
                <h1>Gallery Management</h1>
                <a href="?action=upload" class="btn btn-primary">
                    <span>📤</span> Upload Photo
                </a>
            </div>
            
            <div class="gallery-grid">
                <?php if (!empty($galleryItems)): ?>
                    <?php foreach ($galleryItems as $item): ?>
                    <div class="gallery-item">
                        <img src="../assets/uploads/gallery/<?php echo htmlspecialchars($item['image']); ?>" 
                             alt="<?php echo htmlspecialchars($item['title']); ?>">
                        <div class="gallery-item-info">
                            <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                            <p><?php echo htmlspecialchars($item['description']); ?></p>
                            <p><small>Category: <?php echo htmlspecialchars($item['category'] ?? $item['event'] ?? 'Other'); ?></small></p>
                            <form method="POST" onsubmit="return confirm('Delete this photo?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm" style="width: 100%;">Delete</button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No photos in gallery.</p>
                <?php endif; ?>
            </div>
            
            <?php elseif ($action === 'upload'): ?>
            <div class="page-header">
                <h1>Upload Photo</h1>
                <a href="gallery.php" class="btn btn-secondary">
                    <span>←</span> Back to Gallery
                </a>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" class="form-horizontal">
                        <input type="hidden" name="action" value="upload">
                        
                        <div class="form-group">
                            <label>Photo Title *</label>
                            <input type="text" name="title" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Description *</label>
                            <textarea name="description" rows="3" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>Category *</label>
                            <select name="category" required>
                                <option value="">Select Category</option>
                                <option value="Events">Events</option>
                                <option value="Workshops">Workshops</option>
                                <option value="Outreach">Outreach</option>
                                <option value="Team Building">Team Building</option>
                                <option value="Competitions">Competitions</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Image File * (Max 5MB)</label>
                            <input type="file" name="image" accept="image/*" required>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Upload Photo</button>
                            <a href="gallery.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
