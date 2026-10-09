<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Home';

$categories = $pdo->query("SELECT * FROM categories WHERE status = 'active' ORDER BY name LIMIT 8")->fetchAll();

$products = $pdo->query("
    SELECT p.*, v.store_name 
    FROM products p 
    JOIN vendors v ON p.vendor_id = v.id 
    WHERE p.status = 'active' AND v.status = 'active' 
    ORDER BY p.created_at DESC 
    LIMIT 8
")->fetchAll();

$vendorCount  = $pdo->query("SELECT COUNT(*) FROM vendors WHERE status = 'active'")->fetchColumn();
$productCount = $pdo->query("SELECT COUNT(*) FROM products WHERE status = 'active'")->fetchColumn();
$userCount    = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'active'")->fetchColumn();

require_once __DIR__ . '/includes/header.php';
?>

<!-- HERO -->
<section class="hero hero-home">
    <div class="hero-bg" style="background-image: url('https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=1920&q=80');"></div>
    <div class="hero-gradient"></div>
    <div class="container">
        <div class="hero-content">
            <div class="hero-badge">
                <i class="fas fa-bolt"></i> <?= number_format($productCount) ?> products · <?= $vendorCount ?> vendors
            </div>
            <h1>
                Discover. Shop. Sell.<br>
                <span>All in one place.</span>
            </h1>
            <p>
                <?= SITE_NAME ?> brings together independent sellers and conscious buyers 
                in a marketplace built for trust, speed, and great prices.
            </p>
            <div class="hero-actions">
                <a href="<?= SITE_URL ?>/products.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-shopping-bag"></i> Start Shopping
                </a>
                <a href="<?= SITE_URL ?>/register.php" class="btn btn-glass btn-lg">
                    <i class="fas fa-store"></i> Open Your Store
                </a>
            </div>
            
            <div class="hero-stats">
                <div><strong><?= number_format($productCount) ?>+</strong><span>Products</span></div>
                <div><strong><?= $vendorCount ?>+</strong><span>Vendors</span></div>
                <div><strong><?= number_format($userCount) ?>+</strong><span>Shoppers</span></div>
            </div>
        </div>
    </div>
</section>

<!-- TRUST STRIP -->
<section class="trust-strip">
    <div class="container">
        <div class="trust-grid">
            <div class="trust-item"><i class="fas fa-truck-fast"></i><div><strong>Fast Delivery</strong><span>Nationwide shipping</span></div></div>
            <div class="trust-item"><i class="fas fa-shield-halved"></i><div><strong>Secure Payments</strong><span>Protected checkout</span></div></div>
            <div class="trust-item"><i class="fas fa-rotate-left"></i><div><strong>Easy Returns</strong><span>7-day return policy</span></div></div>
            <div class="trust-item"><i class="fas fa-headset"></i><div><strong>24/7 Support</strong><span>We're here to help</span></div></div>
        </div>
    </div>
</section>

<!-- CATEGORIES -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <div>
                <h2 class="section-title">Shop by Category</h2>
                <p class="section-subtitle">Find exactly what you're looking for</p>
            </div>
            <a href="<?= SITE_URL ?>/products.php" class="section-link">View All <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="categories-grid">
            <?php foreach ($categories as $i => $cat): ?>
            <a href="<?= SITE_URL ?>/products.php?category=<?= $cat['id'] ?>" class="category-card">
                <div class="category-icon"><i class="fas fa-<?= ['mobile-screen','shirt','couch','spa','dumbbell','book','utensils','gamepad'][$i % 8] ?>"></i></div>
                <h3><?= sanitize($cat['name']) ?></h3>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- FEATURED PRODUCTS -->
<section class="section section-alt">
    <div class="container">
        <div class="section-header">
            <div>
                <h2 class="section-title">Fresh Picks</h2>
                <p class="section-subtitle">The newest arrivals from our vendors</p>
            </div>
            <a href="<?= SITE_URL ?>/products.php" class="section-link">Browse All <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="products-grid">
            <?php foreach ($products as $p): 
                $final = $p['discount_price'] ?: $p['price'];
                $hasDiscount = $p['discount_price'] && $p['discount_price'] < $p['price'];
            ?>
            <div class="product-card">
                <div class="product-image">
                    <?php if ($hasDiscount): ?><span class="product-badge badge-sale">Sale</span><?php endif; ?>
                    <a href="<?= SITE_URL ?>/product.php?id=<?= $p['id'] ?>">
                        <img src="<?= $p['image'] ? UPLOAD_URL . $p['image'] : 'https://via.placeholder.com/400x400/f1f5f9/94a3b8?text=' . urlencode($p['name']) ?>" alt="<?= sanitize($p['name']) ?>">
                    </a>
                </div>
                <div class="product-info">
                    <span class="product-vendor"><?= sanitize($p['store_name']) ?></span>
                    <h3 class="product-name"><a href="<?= SITE_URL ?>/product.php?id=<?= $p['id'] ?>"><?= sanitize($p['name']) ?></a></h3>
                    <div class="product-price">
                        <span class="price-current"><?= formatPrice($final) ?></span>
                        <?php if ($hasDiscount): ?><span class="price-old"><?= formatPrice($p['price']) ?></span><?php endif; ?>
                    </div>
                    <div class="product-actions">
                        <a href="<?= SITE_URL ?>/product.php?id=<?= $p['id'] ?>" class="btn btn-primary">View</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- SELLER CTA -->
<section class="cta-seller">
    <div class="container">
        <div class="cta-seller-inner">
            <div class="cta-seller-content">
                <span class="cta-tag"><i class="fas fa-rocket"></i> Become a Seller</span>
                <h2>Turn your products into a growing business</h2>
                <p>Join hundreds of vendors already earning on <?= SITE_NAME ?>. Set up your store in minutes — no monthly fees, no hidden costs.</p>
                <ul class="cta-list">
                    <li><i class="fas fa-check"></i> Free store setup</li>
                    <li><i class="fas fa-check"></i> Real-time order tracking</li>
                    <li><i class="fas fa-check"></i> Instant payouts</li>
                </ul>
                <a href="<?= SITE_URL ?>/register.php" class="btn btn-primary btn-lg"><i class="fas fa-store"></i> Start Selling</a>
            </div>
            <div class="cta-seller-image">
                <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800&q=80" alt="Seller">
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>