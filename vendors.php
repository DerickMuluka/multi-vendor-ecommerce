<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Vendors';

$vendors = $pdo->query("
    SELECT v.*, (SELECT COUNT(*) FROM products WHERE vendor_id = v.id AND status = 'active') as product_count 
    FROM vendors v WHERE v.status = 'active' 
    ORDER BY v.store_name
")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding-top: 40px;">
    <div class="page-header">
        <div>
            <h1 class="page-title">Our Vendors</h1>
            <p style="color: var(--gray-500); font-size: 0.9rem; margin-top: 4px;"><?= count($vendors) ?> trusted sellers</p>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 20px;">
        <?php foreach ($vendors as $v): ?>
            <div class="category-card" style="text-align: left;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                    <div style="width: 50px; height: 50px; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); border-radius: var(--radius); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.2rem;">
                        <?= strtoupper(substr($v['store_name'], 0, 1)) ?>
                    </div>
                    <div>
                        <h3 style="font-size: 1rem; font-weight: 700;"><?= sanitize($v['store_name']) ?></h3>
                        <p style="font-size: 0.8rem; color: var(--gray-500);"><?= $v['product_count'] ?> products</p>
                    </div>
                </div>
                <p style="font-size: 0.85rem; color: var(--gray-600); margin-bottom: 16px;">Owned by <?= sanitize($v['owner_name']) ?></p>
                <a href="<?= SITE_URL ?>/products.php?vendor=<?= $v['id'] ?>" class="btn btn-outline btn-sm btn-block">View Products</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>