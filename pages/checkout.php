<?php
require_once '../config.php';
require_once '../includes/db_helper.php';
require_once '../includes/functions.php';

// If the user is logged in, fetch user info for convenience
$user = null;
if (!empty($_SESSION['user_id'])) {
    $user = getUserById($_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - JPCS.Mart</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/pages.css">
    <link rel="stylesheet" href="../css/checkout.css">
</head>
<body class="inner-page">

<?php include '../includes/header.php'; ?>

<section class="checkout-container">
    <h1 class="anton-font">Checkout</h1>
    <p>Review your items and complete payment.</p>

    <div class="checkout-grid">
        <div class="cart-items">
            <h3>Your Items</h3>
            <div id="cartItemsList">Loading...</div>
        </div>

        <div class="order-summary">
            <h3>Order Summary</h3>
            <p id="orderTotal">₱0.00</p>

            <form id="checkoutForm" enctype="multipart/form-data">
                <?php if (!$user): ?>
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" id="phone" name="phone">
                </div>
                <?php else: ?>
                <p>Logged in as <?php echo htmlspecialchars($user['email']); ?>.</p>
                <?php endif; ?>

                <div style="margin-top:10px;">
                    <label>Payment Method</label>
                    <div>
                        <label><input type="radio" name="payment_method" value="onsite" checked> Onsite / Cash</label>
                        <label style="margin-left:10px;"><input type="radio" name="payment_method" value="gcash"> GCash (Upload Receipt)</label>
                    </div>
                </div>

                <div id="gcashReceipt" class="hidden">
                    <label for="gcash_receipt">GCash Receipt (image)</label>
                    <input type="file" id="gcash_receipt" name="gcash_receipt" accept="image/*">
                </div>

                <input type="hidden" id="cartData" name="cart">
                <div class="form-actions">
                    <button type="submit" class="btn-primary" id="placeOrderBtn"><span id="btnText">Place Order</span> <span id="btnSpinner" class="btn-spinner hidden"></span></button>
                </div>
            </form>
            <div id="checkoutMessage" class="alert hidden" role="status"></div>
        </div>
    </div>
</section>

<footer>
    <p>&copy; 2025 JPCS Malvar Chapter. All Rights Reserved.</p>
</footer>

<script>
const CART_KEY='jpcs_cart';
function getCart(){ try{return JSON.parse(localStorage.getItem(CART_KEY))||[];}catch(e){return[];} }
function updateCartDOM(){
    const list = document.getElementById('cartItemsList');
    const cart = getCart();
    if(cart.length===0){ list.innerHTML='<p>Your cart is empty. Go to JPCS.Mart to add items.</p>'; return; }

    list.innerHTML='';
    let total = 0;
    cart.forEach(item=>{
        const div = document.createElement('div'); div.className='cart-item';
        const img = document.createElement('img');
        if(item.image) {
            if(item.image.indexOf('assets/') === 0 || item.image.indexOf('uploads/') === 0) {
                img.src = '../' + item.image;
            } else {
                img.src = '../assets/uploads/products/' + item.image;
            }
        } else img.src='../assets/icons/default.png';
        const info = document.createElement('div'); info.style.flex='1';
        info.innerHTML=`<strong>${item.name}</strong><br>₱${(item.price*1).toFixed(2)} x ${item.quantity}`;
        div.appendChild(img); div.appendChild(info);
        list.appendChild(div);
        total += (item.price*1) * (item.quantity||1);
    });
    document.getElementById('orderTotal').textContent='₱'+total.toFixed(2);
    document.getElementById('cartData').value = JSON.stringify(cart);
}

updateCartDOM();

// Payment method toggle
const rads = document.querySelectorAll('input[name="payment_method"]');
const gcashReceiptEl = document.getElementById('gcashReceipt');
rads.forEach(r=>r.addEventListener('change',()=>{
    const show = document.querySelector('input[name="payment_method"]:checked').value==='gcash';
    gcashReceiptEl.classList.toggle('hidden', !show);
}));

// Submit
const form = document.getElementById('checkoutForm');
const placeOrderBtn = document.getElementById('placeOrderBtn');
const btnSpinner = document.getElementById('btnSpinner');
const btnText = document.getElementById('btnText');
const checkoutMessage = document.getElementById('checkoutMessage');

form.addEventListener('submit', async (e)=>{
    e.preventDefault();
    const formData = new FormData(form);
    formData.set('cart', document.getElementById('cartData').value);

    // Start loading state
    placeOrderBtn.setAttribute('disabled', 'disabled');
    btnSpinner.classList.remove('hidden');
    btnText.textContent = 'Processing...';
    checkoutMessage.classList.add('hidden');

    // Send to handler
    const res = await fetch('../handlers/checkout.php', { method: 'POST', body: formData });
    const json = await res.json();
    if(json.success){
        // Clear cart
        localStorage.removeItem(CART_KEY);
        document.getElementById('checkoutMessage').style.color = 'green';
        checkoutMessage.classList.remove('hidden');
        checkoutMessage.classList.remove('alert-error');
        checkoutMessage.classList.add('alert-success');
        checkoutMessage.textContent = 'Order placed successfully. Redirecting...';
        setTimeout(()=>{ window.location.href = json.redirect; }, 1200);
    } else {
        checkoutMessage.classList.remove('hidden');
        checkoutMessage.classList.remove('alert-success');
        checkoutMessage.classList.add('alert-error');
        checkoutMessage.textContent = json.message || 'Failed to place order.';
        // Re-enable button
        placeOrderBtn.removeAttribute('disabled');
        btnSpinner.classList.add('hidden');
        btnText.textContent = 'Place Order';
    }
});
</script>

<?php include '../includes/tawk_chat.php'; ?>

</body>
</html>
