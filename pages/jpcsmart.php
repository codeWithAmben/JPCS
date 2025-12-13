<?php
require_once '../config.php';
require_once '../includes/db_helper.php';

// Get all active products from database
$allProducts = getAllProducts();
$products = array_filter($allProducts, function($product) {
    return ($product['status'] ?? 'active') === 'active' || ($product['status'] ?? '') === 'available';
});

// Get unique categories
$categories = ['All Products'];
foreach ($products as $product) {
    $cat = $product['category'] ?? 'Other';
    if (!in_array($cat, $categories)) {
        $categories[] = $cat;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JPCS.Mart - JPCS Malvar</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/pages.css">
    <link rel="stylesheet" href="../css/jpcsmart.css">
</head>

<body class="inner-page">

<?php include '../includes/header.php'; ?>

<section class="mart-section">
    <h1 class="anton-font">JPCS.Mart</h1>
    <p class="mart-intro">
        Your one-stop shop for official JPCS merchandise and materials. Show your pride as a member of the JPCS Malvar Chapter!
    </p>

    <div class="product-categories">
        <?php foreach ($categories as $cat): ?>
            <button class="product-category-btn <?php echo $cat === 'All Products' ? 'active' : ''; ?>" data-category="<?php echo htmlspecialchars($cat); ?>">
                <?php echo htmlspecialchars($cat); ?>
            </button>
        <?php endforeach; ?>
    </div>

    <div class="products-grid">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="product-card" data-id="<?php echo htmlspecialchars($product['id']); ?>" data-category="<?php echo htmlspecialchars($product['category'] ?? 'Other'); ?>" data-image="<?php echo htmlspecialchars($product['image'] ?? ''); ?>" data-stock="<?php echo (int)($product['stock'] ?? 0); ?>">
                    <div class="product-image">
                        <?php if (!empty($product['image']) && $product['image'] !== 'default.jpg'): ?>
                            <img src="../assets/uploads/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <?php
                            // Default icons based on category or name
                            $icon = '🛒';
                            $name = strtolower($product['name'] ?? '');
                            $category = strtolower($product['category'] ?? '');
                            if (strpos($name, 'shirt') !== false || strpos($category, 'apparel') !== false) $icon = '👕';
                            elseif (strpos($name, 'bag') !== false || strpos($name, 'backpack') !== false) $icon = '🎒';
                            elseif (strpos($name, 'notebook') !== false || strpos($category, 'book') !== false) $icon = '📝';
                            elseif (strpos($name, 'cap') !== false || strpos($name, 'hat') !== false) $icon = '🧢';
                            elseif (strpos($name, 'sticker') !== false) $icon = '📱';
                            elseif (strpos($name, 'lanyard') !== false) $icon = '🏷️';
                            ?>
                            <?php echo $icon; ?>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="product-price">₱<?php echo number_format((float)($product['price'] ?? 0), 2); ?></p>
                        <p class="product-description"><?php echo htmlspecialchars($product['description'] ?? ''); ?></p>
                        <p class="stock-status <?php 
                            $stock = (int)($product['stock'] ?? 0);
                            if ($stock > 10) echo 'in-stock';
                            elseif ($stock > 0) echo 'low-stock';
                            else echo 'out-of-stock';
                        ?>">
                            <?php 
                            if ($stock > 10) echo '✓ In Stock (' . $stock . ')';
                            elseif ($stock > 0) echo '⚠ Only ' . $stock . ' left';
                            else echo '✗ Out of Stock';
                            ?>
                        </p>
                        <?php if ($stock > 0): ?>
                            <button class="product-btn">Add to Cart</button>
                        <?php else: ?>
                            <button class="product-btn" disabled style="opacity: 0.5; cursor: not-allowed;">Out of Stock</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 60px 20px; color: #666;">
                <p style="font-size: 3rem; margin-bottom: 20px;">🛒</p>
                <h3>No Products Available</h3>
                <p>Check back soon for new merchandise!</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Floating Cart Summary -->
    <div class="cart-summary" id="cartSummary" style="display:none;">
        <div class="cart-count" id="cartCount">0</div>
        <div class="cart-label">View Cart</div>
    </div>

    <div class="coming-soon">
        <h2>More Products Coming Soon!</h2>
        <p>We're constantly adding new items to our collection. Stay tuned for exclusive merchandise drops!</p>
    </div>
</section>

<footer>
    <p>&copy; 2025 JPCS Malvar Chapter. All Rights Reserved.</p>
</footer>

<script src="../js/script.js"></script>
<script>
    // Product category filter
    document.querySelectorAll('.product-category-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Update active button
            document.querySelectorAll('.product-category-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Filter products
            const category = this.getAttribute('data-category');
            document.querySelectorAll('.product-card').forEach(card => {
                if (category === 'All Products' || card.getAttribute('data-category') === category) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // Simple localStorage cart
    const CART_KEY = 'jpcs_cart';
    function getCart() {
        try { return JSON.parse(localStorage.getItem(CART_KEY)) || []; } catch(e) { return []; }
    }
    function saveCart(cart) { localStorage.setItem(CART_KEY, JSON.stringify(cart)); updateCartSummary(); }

    function addToCart(product) {
        const cart = getCart();
        const existing = cart.find(item => item.product_id === product.product_id);
        if (existing) existing.quantity += product.quantity;
        else cart.push(product);
        saveCart(cart);
    }

    function updateCartSummary() {
        const cart = getCart();
        const count = cart.reduce((s,i)=> s + (i.quantity || 1), 0);
        document.getElementById('cartCount').textContent = count;
        document.getElementById('cartSummary').style.display = count > 0 ? 'flex' : 'none';
    }

    // Attach add to cart buttons
    document.querySelectorAll('.product-card').forEach(card => {
        const btn = card.querySelector('.product-btn');
        if (!btn) return;
        btn.addEventListener('click', () => {
            const id = card.getAttribute('data-id') || card.querySelector('.product-info h3').textContent.trim();
            const name = card.querySelector('.product-info h3').textContent.trim();
            const priceText = card.querySelector('.product-price').textContent.replace(/[^0-9\.\,]/g,'').replace(',','');
            const price = parseFloat(priceText) || 0;
            const image = card.getAttribute('data-image') || '';
            const stock = parseInt(card.getAttribute('data-stock') || '0', 10) || 0;
            if (stock <= 0) { alert('Item is out of stock'); return; }
            const product = { product_id: id, name, price, quantity: 1, image };
            addToCart(product);
            alert('Added to cart: ' + name);
        });
    });

    // Cart summary click: go to checkout page
    document.getElementById('cartSummary').addEventListener('click', function(){ window.location.href = '../pages/checkout.php'; });

    updateCartSummary();
</script>

<?php include '../includes/tawk_chat.php'; ?>

</body>
</html>
