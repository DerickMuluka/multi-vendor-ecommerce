<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$pageTitle = 'All Products';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([(int)$_POST['delete_id']]);
    setFlashMessage('success', 'Product deleted');
    redirect(SITE_URL . '/admin/products.php');
}

$products = $pdo->query("
    SELECT p.*, v.store_name, c.name as category_name 
    FROM products p 
    JOIN vendors v ON p.vendor_id = v.id 
    JOIN categories c ON p.category_id = c.id 
    ORDER BY p.created_at DESC
")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-brand"><i class="fas fa-shield-alt"></i> Admin Panel</div>
        <nav class="sidebar-nav">
            <a href="<?= SITE_URL ?>/admin/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="<?= SITE_URL ?>/admin/vendors.php"><i class="fas fa-store"></i> Vendors</a>
            <a href="<?= SITE_URL ?>/admin/users.php"><i class="fas fa-users"></i> Users</a>
            <a href="<?= SITE_URL ?>/admin/products.php" class="active"><i class="fas fa-box"></i> Products</a>
            <a href="<?= SITE_URL ?>/admin/categories.php"><i class="fas fa-tags"></i> Categories</a>
            <a href="<?= SITE_URL ?>/admin/orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
            <a href="<?= SITE_URL ?>/admin/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>
    
    <div class="dashboard-content">
        <div class="page-header">
            <h1 class="page-title">Products (<?= count($products) ?>)</h1>
        </div>
        
        <div class="table-card">
            <div class="table-responsive">
                <table>
                    <thead><tr><th>Image</th><th>Product</th><th>Vendor</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr><td colspan="8" style="text-align:center; padding:40px;">No products</td></tr>
                        <?php else: foreach ($products as $p): ?>
                            <tr>
                                <td><img src="<?= $p['image'] ? UPLOAD_URL . $p['image'] : 'https://via.placeholder.com/48' ?>" class="product-thumb"></td>
                                <td><strong><?= sanitize($p['name']) ?></strong></td>
                                <td><?= sanitize($p['store_name']) ?></td>
                                <td><?= sanitize($p['category_name']) ?></td>
                                <td><?= formatPrice($p['price']) ?></td>
                                <td><?= $p['stock'] ?></td>
                                <td><span class="badge badge-<?= $p['status'] === 'active' ? 'success' : 'gray' ?>"><?= ucfirst($p['status']) ?></span></td>
                                <td>
                                    <form method="POST" onsubmit="return confirm('Delete?')">
                                        <input type="hidden" name="delete_id" value="<?= $p['id'] ?>">
                                        <button class="action-btn delete"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>