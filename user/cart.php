<?php
require_once __DIR__ . '/../includes/auth.php';
requireUser();

$pageTitle = 'Shopping Cart';
$userId = $_SESSION['user_id'];

// Handle updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_qty'], $_POST['cart_id'])) {
        $qty = max(1, (int)$_POST['update_qty']);
        $cartId = (int)$_POST['cart_id'];
        $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?")->execute([$qty, $cartId, $userId]);
    } elseif (isset($_POST['remove_id'])) {
        $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?")->execute([(int)$_POST['remove_id'], $userId]);
        setFlashMessage('success', 'Item removed');
    }
    redirect(SITE_URL . '/user/cart.php');
}

$stmt = $pdo->prepare("
    SELECT c.id as cart_id, c.quantity, p.id as product_id, p.name, p.price, p.discount_price, p.image, p.stock, v.store_name 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    JOIN vendors v ON p.vendor_id = v.id 
    WHERE c.user_id = ? 
    ORDER BY c.created_at DESC
");
$stmt->execute([$userId]);
$cartItems = $stmt->fetchAll();

$subtotal = 0;
foreach ($cartItems as $item) {
    $price = $item['discount_price'] ?: $item['price'];
    $subtotal += $price * $item['quantity'];
}

$shipping = $subtotal > 0 ? 200 : 0;
$total = $subtotal + $shipping;

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container" style="padding-top: 40px;">
    <div class="page-header">
        <h1 class="page-title">Shopping Cart</h1>
    </div>
    
    <?php if (empty($cartItems)): ?>
        <div class="empty-state">
            <i class="fas fa-shopping-bag"></i>
            <h3>Your cart is empty</h3>
            <p>Add items to start shopping</p>
            <a href="<?= SITE_URL ?>/products.php" class="btn btn-primary">Browse Products</a>
        </div>
    <?php else: ?>
        <div class="cart-layout">
            <div>
                <?php foreach ($cartItems as $item): 
                    $price = $item['discount_price'] ?: $item['price'];
                ?>
                <div class="cart-item">
                    <img src="<?= $item['image'] ? UPLOAD_URL . $item['image'] : 'https://via.placeholder.com/90' ?>" class="cart-item-image">
                    <div class="cart-item-info">
                        <span class="vendor"><?= sanitize($item['store_name']) ?></span>
                        <h3><?= sanitize($item['name']) ?></h3>
                        <div class="price"><?= formatPrice($price) ?></div>
                    </div>
                    <div class="cart-item-actions">
                        <form method="POST" style="display:flex; gap:8px; align-items:center;">
                            <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                            <div class="qty-control">
                                <button type="button" class="qty-minus"><i class="fas fa-minus"></i></button>
                                <input type="number" name="update_qty" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock'] ?>" onchange="this.form.submit()">
                                <button type="button" class="qty-plus"><i class="fas fa-plus"></i></button>
                            </div>
                        </form>
                        <form method="POST">
                            <input type="hidden" name="remove_id" value="<?= $item['cart_id'] ?>">
                            <button class="btn btn-sm" style="background:rgba(239,68,68,0.1); color:var(--danger);" title="Remove"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="summary-card">
                <h3>Order Summary</h3>
                <div class="summary-row"><span>Subtotal</span><span><?= formatPrice($subtotal) ?></span></div>
                <div class="summary-row"><span>Shipping</span><span><?= formatPrice($shipping) ?></span></div>
                <div class="summary-row total"><span>Total</span><span><?= formatPrice($total) ?></span></div>
                <a href="<?= SITE_URL ?>/user/checkout.php" class="btn btn-primary btn-block btn-lg" style="margin-top:20px;">
                    <i class="fas fa-lock"></i> Proceed to Checkout
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>