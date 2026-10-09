<?php
require_once __DIR__ . '/../includes/auth.php';
requireUser();

$pageTitle = 'Checkout';
$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT c.id as cart_id, c.quantity, p.id as product_id, p.name, p.price, p.discount_price, p.stock, p.vendor_id 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ?
");
$stmt->execute([$userId]);
$cartItems = $stmt->fetchAll();

if (empty($cartItems)) redirect(SITE_URL . '/user/cart.php');

$userStmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$userStmt->execute([$userId]);
$user = $userStmt->fetch();

$subtotal = 0;
foreach ($cartItems as $item) {
    $price = $item['discount_price'] ?: $item['price'];
    $subtotal += $price * $item['quantity'];
}
$shipping = 200;
$total = $subtotal + $shipping;

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = sanitize($_POST['address'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $notes = sanitize($_POST['notes'] ?? '');
    
    if (empty($address) || empty($phone)) {
        $error = 'Please fill all required fields';
    } else {
        try {
            $pdo->beginTransaction();
            
            $orderNumber = generateOrderNumber();
            $orderStmt = $pdo->prepare("
                INSERT INTO orders (user_id, order_number, total_amount, shipping_address, phone, notes) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $orderStmt->execute([$userId, $orderNumber, $total, $address, $phone, $notes]);
            $orderId = $pdo->lastInsertId();
            
            $itemStmt = $pdo->prepare("
                INSERT INTO order_items (order_id, product_id, vendor_id, quantity, price, subtotal) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            foreach ($cartItems as $item) {
                $price = $item['discount_price'] ?: $item['price'];
                $itemSubtotal = $price * $item['quantity'];
                $itemStmt->execute([$orderId, $item['product_id'], $item['vendor_id'], $item['quantity'], $price, $itemSubtotal]);
                
                $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?")->execute([$item['quantity'], $item['product_id']]);
            }
            
            $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$userId]);
            
            $pdo->commit();
            
            setFlashMessage('success', 'Order placed successfully! Order #' . $orderNumber);
            redirect(SITE_URL . '/user/orders.php');
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Order failed: ' . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container" style="padding-top: 40px;">
    <div class="page-header">
        <h1 class="page-title">Checkout</h1>
    </div>
    
    <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
    
    <div class="cart-layout">
        <div class="table-card" style="padding: 28px;">
            <h3 style="margin-bottom: 20px;">Delivery Information</h3>
            <form method="POST" id="checkoutForm">
                <div class="form-group">
                    <label>Delivery Address *</label>
                    <textarea name="address" class="form-control" rows="3" required><?= sanitize($user['address']) ?></textarea>
                </div>
                <div class="form-group">
                    <label>Phone Number *</label>
                    <input type="tel" name="phone" class="form-control" value="<?= sanitize($user['phone']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Order Notes (optional)</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Any special instructions..."></textarea>
                </div>
                
                <h3 style="margin: 28px 0 16px;">Payment Method</h3>
                <div style="padding: 16px; border: 2px solid var(--primary); border-radius: var(--radius); background: rgba(99,102,241,0.04); display: flex; align-items: center; gap: 12px;">
                    <input type="radio" name="payment" value="cod" checked id="cod">
                    <label for="cod" style="font-weight: 600; cursor: pointer; flex: 1;">Cash on Delivery</label>
                    <i class="fas fa-money-bill-wave" style="color: var(--primary);"></i>
                </div>
            </form>
        </div>
        
        <div class="summary-card">
            <h3>Order Summary</h3>
            <?php foreach ($cartItems as $item): 
                $price = $item['discount_price'] ?: $item['price'];
            ?>
                <div class="summary-row">
                    <span><?= sanitize($item['name']) ?> × <?= $item['quantity'] ?></span>
                    <span><?= formatPrice($price * $item['quantity']) ?></span>
                </div>
            <?php endforeach; ?>
            <div class="summary-row" style="border-top: 1px solid var(--gray-200); margin-top: 8px; padding-top: 12px;">
                <span>Subtotal</span><span><?= formatPrice($subtotal) ?></span>
            </div>
            <div class="summary-row"><span>Shipping</span><span><?= formatPrice($shipping) ?></span></div>
            <div class="summary-row total"><span>Total</span><span><?= formatPrice($total) ?></span></div>
            <button type="submit" form="checkoutForm" class="btn btn-primary btn-block btn-lg" style="margin-top:20px;">
                <i class="fas fa-check-circle"></i> Place Order
            </button>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>