<?php
require_once __DIR__ . '/includes/functions.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) redirect(SITE_URL . '/products.php');

$stmt = $pdo->prepare("
    SELECT p.*, v.store_name, v.id as vendor_id, c.name as category_name 
    FROM products p 
    JOIN vendors v ON p.vendor_id = v.id 
    JOIN categories c ON p.category_id = c.id 
    WHERE p.id = ? AND p.status = 'active'
");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) redirect(SITE_URL . '/products.php');

$pageTitle = $product['name'];

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (!isUserLoggedIn()) {
        setFlashMessage('error', 'Please login to add items to cart');
        redirect(SITE_URL . '/user/login.php');
    }
    
    $qty = max(1, (int)($_POST['quantity'] ?? 1));
    
    $check = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $check->execute([$_SESSION['user_id'], $id]);
    $existing = $check->fetch();
    
    if ($existing) {
        $pdo->prepare("UPDATE cart SET quantity = quantity + ? WHERE id = ?")->execute([$qty, $existing['id']]);
    } else {
        $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)")->execute([$_SESSION['user_id'], $id, $qty]);
    }
    
    setFlashMessage('success', 'Added to cart');
    redirect(SITE_URL . '/product.php?id=' . $id);
}

// Related products
$relatedStmt = $pdo->prepare("
    SELECT p.*, v.store_name FROM products p 
    JOIN vendors v ON p.vendor_id = v.id 
    WHERE p.category_id = ? AND p.id != ? AND p.status = 'active' 
    LIMIT 4
");
$relatedStmt->execute([$product['category_id'], $id]);
$relatedProducts = $relatedStmt->fetchAll();

$finalPrice = $product['discount_price'] ?: $product['price'];
$hasDiscount = $product['discount_price'] && $product['discount_price'] < $product['price'];

require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding-top: 32px;">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: start;" class="product-detail-grid">
        <div style="background: white; border-radius: var(--radius-lg); overflow: hidden; border: 1px solid var(--gray-200);">
            <img src="<?= $product['image'] ? UPLOAD_URL . $product['image'] : 'https://via.placeholder.com/600x600/f1f5f9/94a3b8?text=' . urlencode($product['name']) ?>" alt="<?= sanitize($product['name']) ?>" style="width: 100%; aspect-ratio: 1; object-fit: cover;">
        </div>
        
        <div>
            <span class="product-vendor" style="font-size: 0.85rem;"><?= sanitize($product['store_name']) ?></span>
            <h1 style="font-size: 1.8rem; font-weight: 800; margin: 8px 0 16px; color: var(--gray-900);"><?= sanitize($product['name']) ?></h1>
            
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                <span style="font-size: 2rem; font-weight: 800; color: var(--gray-900);"><?= formatPrice($finalPrice) ?></span>
                <?php if ($hasDiscount): ?>
                    <span style="font-size: 1.1rem; color: var(--gray-400); text-decoration: line-through;"><?= formatPrice($product['price']) ?></span>
                    <span class="badge badge-danger">-<?= round((($product['price'] - $product['discount_price']) / $product['price']) * 100) ?>%</span>
                <?php endif; ?>
            </div>
            
            <div style="padding: 16px 0; border-top: 1px solid var(--gray-200); border-bottom: 1px solid var(--gray-200); margin-bottom: 24px;">
                <p style="color: var(--gray-600); line-height: 1.8;"><?= nl2br(sanitize($product['description'])) ?></p>
            </div>
            
            <p style="margin-bottom: 20px; font-size: 0.9rem; color: <?= $product['stock'] > 0 ? 'var(--success)' : 'var(--danger)' ?>; font-weight: 600;">
                <i class="fas fa-<?= $product['stock'] > 0 ? 'check-circle' : 'times-circle' ?>"></i>
                <?= $product['stock'] > 0 ? 'In Stock (' . $product['stock'] . ' available)' : 'Out of Stock' ?>
            </p>
            
            <?php if ($product['stock'] > 0): ?>
            <form method="POST">
                <div style="display: flex; gap: 12px; align-items: center; margin-bottom: 20px;">
                    <label style="font-weight: 600; font-size: 0.9rem;">Quantity:</label>
                    <div class="qty-control">
                        <button type="button" class="qty-minus"><i class="fas fa-minus"></i></button>
                        <input type="number" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>">
                        <button type="button" class="qty-plus"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
                <button type="submit" name="add_to_cart" class="btn btn-primary btn-lg btn-block">
                    <i class="fas fa-shopping-bag"></i> Add to Cart
                </button>
            </form>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if (!empty($relatedProducts)): ?>
    <section class="section">
        <div class="section-header">
            <h2 class="section-title">You may also like</h2>
        </div>
        <div class="products-grid">
            <?php foreach ($relatedProducts as $rp): 
                $rpFinal = $rp['discount_price'] ?: $rp['price'];
            ?>
            <div class="product-card">
                <div class="product-image">
                    <a href="<?= SITE_URL ?>/product.php?id=<?= $rp['id'] ?>">
                        <img src="<?= $rp['image'] ? UPLOAD_URL . $rp['image'] : 'https://via.placeholder.com/400x400/f1f5f9/94a3b8?text=' . urlencode($rp['name']) ?>" alt="<?= sanitize($rp['name']) ?>">
                    </a>
                </div>
                <div class="product-info">
                    <span class="product-vendor"><?= sanitize($rp['store_name']) ?></span>
                    <h3 class="product-name"><a href="<?= SITE_URL ?>/product.php?id=<?= $rp['id'] ?>"><?= sanitize($rp['name']) ?></a></h3>
                    <div class="product-price">
                        <span class="price-current"><?= formatPrice($rpFinal) ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</div>

<style>
@media (max-width: 768px) {
    .product-detail-grid { grid-template-columns: 1fr !important; gap: 24px !important; }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>