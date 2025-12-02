<?php
require_once '../config.php';
requireAdmin();

$action = $_GET['action'] ?? 'list';
$productId = $_GET['id'] ?? null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $uploadResult = null;
                if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                    $uploadResult = uploadFile($_FILES['image'], 'products');
                }
                
                $data = [
                    'name' => sanitize($_POST['name']),
                    'description' => sanitize($_POST['description']),
                    'price' => sanitize($_POST['price']),
                    'stock' => sanitize($_POST['stock']),
                    'category' => sanitize($_POST['category']),
                    'image' => $uploadResult['success'] ? $uploadResult['filename'] : 'default.jpg',
                    'status' => sanitize($_POST['status'])
                ];
                
                if (createProduct($data)) {
                    setFlash('Product added successfully!', 'success');
                } else {
                    setFlash('Failed to add product.', 'error');
                }
                redirect('products.php');
                break;
                
            case 'edit':
                $data = [
                    'name' => sanitize($_POST['name']),
                    'description' => sanitize($_POST['description']),
                    'price' => sanitize($_POST['price']),
                    'stock' => sanitize($_POST['stock']),
                    'category' => sanitize($_POST['category']),
                    'status' => sanitize($_POST['status'])
                ];
                
                if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                    $uploadResult = uploadFile($_FILES['image'], 'products');
                    if ($uploadResult['success']) {
                        $data['image'] = $uploadResult['filename'];
                    }
                }
                
                if (updateProduct($_POST['id'], $data)) {
                    setFlash('Product updated successfully!', 'success');
                } else {
                    setFlash('Failed to update product.', 'error');
                }
                redirect('products.php');
                break;
                
            case 'delete':
                if (deleteProduct($_POST['id'])) {
                    setFlash('Product deleted successfully!', 'success');
                } else {
                    setFlash('Failed to delete product.', 'error');
                }
                redirect('products.php');
                break;
        }
    }
}

$products = getAllProducts();
$currentProduct = null;
if ($action === 'edit' && $productId) {
    $currentProduct = getProductById($productId);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin</title>
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
                <h1>Manage Products</h1>
                <a href="?action=add" class="btn btn-primary">
                    <span>➕</span> Add Product
                </a>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($products)): ?>
                                    <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($product['name']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($product['category']); ?></td>
                                        <td>₱<?php echo number_format($product['price'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($product['stock']); ?></td>
                                        <td>
                                            <?php 
                                            $status = $product['status'];
                                            $badgeClass = $status === 'available' ? 'badge-success' : 'badge-secondary';
                                            echo '<span class="badge ' . $badgeClass . '">' . strtoupper($status) . '</span>';
                                            ?>
                                        </td>
                                        <td class="actions">
                                            <a href="?action=edit&id=<?php echo $product['id']; ?>" class="btn-sm btn-info">Edit</a>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this product?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                                <button type="submit" class="btn-sm btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 40px;">No products found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <?php elseif ($action === 'add' || $action === 'edit'): ?>
            <div class="page-header">
                <h1><?php echo $action === 'add' ? 'Add Product' : 'Edit Product'; ?></h1>
                <a href="products.php" class="btn btn-secondary">
                    <span>←</span> Back to List
                </a>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" class="form-horizontal">
                        <input type="hidden" name="action" value="<?php echo $action; ?>">
                        <?php if ($action === 'edit'): ?>
                            <input type="hidden" name="id" value="<?php echo $currentProduct['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label>Product Name *</label>
                            <input type="text" name="name" required 
                                   value="<?php echo $currentProduct['name'] ?? ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Description *</label>
                            <textarea name="description" rows="4" required><?php echo $currentProduct['description'] ?? ''; ?></textarea>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Category *</label>
                                <select name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="Merchandise" <?php echo ($currentProduct['category'] ?? '') === 'Merchandise' ? 'selected' : ''; ?>>Merchandise</option>
                                    <option value="Books" <?php echo ($currentProduct['category'] ?? '') === 'Books' ? 'selected' : ''; ?>>Books</option>
                                    <option value="Accessories" <?php echo ($currentProduct['category'] ?? '') === 'Accessories' ? 'selected' : ''; ?>>Accessories</option>
                                    <option value="Other" <?php echo ($currentProduct['category'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Price *</label>
                                <input type="number" name="price" required min="0" step="0.01"
                                       value="<?php echo $currentProduct['price'] ?? ''; ?>">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Stock Quantity *</label>
                                <input type="number" name="stock" required min="0"
                                       value="<?php echo $currentProduct['stock'] ?? ''; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label>Status *</label>
                                <select name="status" required>
                                    <option value="available" <?php echo ($currentProduct['status'] ?? '') === 'available' ? 'selected' : ''; ?>>Available</option>
                                    <option value="out_of_stock" <?php echo ($currentProduct['status'] ?? '') === 'out_of_stock' ? 'selected' : ''; ?>>Out of Stock</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Product Image <?php echo $action === 'edit' ? '(Leave blank to keep current)' : '*'; ?></label>
                            <input type="file" name="image" accept="image/*" <?php echo $action === 'add' ? 'required' : ''; ?>>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <?php echo $action === 'add' ? 'Add Product' : 'Update Product'; ?>
                            </button>
                            <a href="products.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
