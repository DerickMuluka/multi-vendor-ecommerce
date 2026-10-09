<?php
require_once __DIR__ . '/../includes/auth.php';
requireVendor();

$pageTitle = 'My Products';
$vendorId = $_SESSION['vendor_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ? AND vendor_id = ?");
    $stmt->execute([(int)$_POST['delete_id'], $vendorId]);
    setFlashMessage('success', 'Product deleted');
    redirect(SITE_URL . '/vendor/products.php');
}

$stmt = $pdo->prepare("
    SELECT p.*, c.name as category_name 
    FROM products p 
    JOIN categories c ON p.category_id = c.id 
    WHERE p.vendor_id = ? 
    ORDER BY p.created_at DESC
");
$stmt->execute([$vendorId]);
$products = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-brand"><i class="fas fa-store"></i> Vendor Panel</div>
        <nav class="sidebar-nav">
            <a href="<?= SITE_URL ?>/vendor/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="<?= SITE_URL ?>/vendor/products.php" class="active"><i class="fas fa-box"></i> Products</a>
            <a href="<?= SITE_URL ?>/vendor/add-product.php"><i class="fas fa-plus-circle"></i> Add Product</a>
            <a href="<?= SITE_URL ?>/vendor/orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
            <a href="<?= SITE_URL ?>/vendor/profile.php"><i class="fas fa-user-cog"></i> Profile</a>
            <a href="<?= SITE_URL ?>/vendor/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>
    
    <div class="dashboard-content">
        <div class="page-header">
            <h1 class="page-title">Products (<?= count($products) ?>)</h1>
            <a href="<?= SITE_URL ?>/vendor/add-product.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add Product</a>
        </div>
        
        <div class="table-card">
            <div class="table-responsive">
                <table>
                    <thead><tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr><td colspan="7" style="text-align:center; padding:40px;">
                                <i class="fas fa-box-open" style="font-size:2rem; color:var(--gray-300); display:block; margin-bottom:10px;"></i>
                                No products yet. <a href="<?= SITE_URL ?>/vendor/add-product.php" style="color:var(--primary); font-weight:600;">Add your first product</a>
                            </td></tr>
                        <?php else: foreach ($products as $p): ?>
                            <tr>
                                <td><img src="<?= $p['image'] ? UPLOAD_URL . $p['image'] : 'https://via.placeholder.com/48' ?>" class="product-thumb"></td>
                                <td><strong><?= sanitize($p['name']) ?></strong></td>
                                <td><?= sanitize($p['category_name']) ?></td>
                                <td><?= formatPrice($p['price']) ?></td>
                                <td><?= $p['stock'] ?></td>
                                <td><span class="badge badge-<?= $p['status'] === 'active' ? 'success' : 'gray' ?>"><?= ucfirst($p['status']) ?></span></td>
                                <td>
                                    <div class="action-btns">
                                        <a href="<?= SITE_URL ?>/vendor/edit-product.php?id=<?= $p['id'] ?>" class="action-btn edit"><i class="fas fa-edit"></i></a>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this product?')">
                                            <input type="hidden" name="delete_id" value="<?= $p['id'] ?>">
                                            <button class="action-btn delete"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
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