<?php
require_once __DIR__ . '/../includes/auth.php';
requireVendor();

$pageTitle = 'Vendor Dashboard';
$vendorId = $_SESSION['vendor_id'];

$stats = [
    'products' => $pdo->prepare("SELECT COUNT(*) FROM products WHERE vendor_id = ?"),
    'orders' => $pdo->prepare("SELECT COUNT(DISTINCT order_id) FROM order_items WHERE vendor_id = ?"),
    'revenue' => $pdo->prepare("SELECT COALESCE(SUM(subtotal), 0) FROM order_items WHERE vendor_id = ?")
];

$stats['products']->execute([$vendorId]); $productCount = $stats['products']->fetchColumn();
$stats['orders']->execute([$vendorId]); $orderCount = $stats['orders']->fetchColumn();
$stats['revenue']->execute([$vendorId]); $revenue = $stats['revenue']->fetchColumn();

$lowStock = $pdo->prepare("SELECT * FROM products WHERE vendor_id = ? AND stock <= 5 ORDER BY stock ASC LIMIT 5");
$lowStock->execute([$vendorId]);
$lowStockProducts = $lowStock->fetchAll();

$recentOrders = $pdo->prepare("
    SELECT DISTINCT o.id, o.order_number, o.total_amount, o.order_status, o.created_at, u.full_name
    FROM orders o 
    JOIN order_items oi ON o.id = oi.order_id 
    JOIN users u ON o.user_id = u.id 
    WHERE oi.vendor_id = ? 
    ORDER BY o.created_at DESC LIMIT 5
");
$recentOrders->execute([$vendorId]);
$recentOrders = $recentOrders->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-brand"><i class="fas fa-store"></i> Vendor Panel</div>
        <nav class="sidebar-nav">
            <a href="<?= SITE_URL ?>/vendor/dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="<?= SITE_URL ?>/vendor/products.php"><i class="fas fa-box"></i> Products</a>
            <a href="<?= SITE_URL ?>/vendor/add-product.php"><i class="fas fa-plus-circle"></i> Add Product</a>
            <a href="<?= SITE_URL ?>/vendor/orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
            <a href="<?= SITE_URL ?>/vendor/profile.php"><i class="fas fa-user-cog"></i> Profile</a>
            <a href="<?= SITE_URL ?>/vendor/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>
    
    <div class="dashboard-content">
        <div class="page-header">
            <div>
                <h1 class="page-title"><?= sanitize($_SESSION['vendor_name']) ?></h1>
                <p style="color: var(--gray-500); font-size: 0.9rem; margin-top: 4px;">Store overview</p>
            </div>
            <a href="<?= SITE_URL ?>/vendor/add-product.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add Product</a>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon purple"><i class="fas fa-box"></i></div>
                <div class="stat-info"><h3><?= $productCount ?></h3><p>Products</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fas fa-shopping-cart"></i></div>
                <div class="stat-info"><h3><?= $orderCount ?></h3><p>Orders</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fas fa-money-bill-wave"></i></div>
                <div class="stat-info"><h3><?= formatPrice($revenue) ?></h3><p>Revenue</p></div>
            </div>
        </div>
        
        <?php if (!empty($lowStockProducts)): ?>
        <div class="alert alert-warning" style="background:#fffbeb; color:#92400e; border-left:4px solid var(--warning);">
            <i class="fas fa-exclamation-triangle"></i> <?= count($lowStockProducts) ?> product(s) with low stock
        </div>
        <?php endif; ?>
        
        <div class="table-card">
            <div class="table-header">
                <h3>Recent Orders</h3>
                <a href="<?= SITE_URL ?>/vendor/orders.php" class="btn btn-outline btn-sm">View All</a>
            </div>
            <div class="table-responsive">
                <table>
                    <thead><tr><th>Order #</th><th>Customer</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody>
                        <?php if (empty($recentOrders)): ?>
                            <tr><td colspan="5" style="text-align:center; padding:40px; color:var(--gray-500);">No orders yet</td></tr>
                        <?php else: foreach ($recentOrders as $o): ?>
                            <tr>
                                <td><strong><?= $o['order_number'] ?></strong></td>
                                <td><?= sanitize($o['full_name']) ?></td>
                                <td><?= formatPrice($o['total_amount']) ?></td>
                                <td><span class="badge badge-<?= $o['order_status'] === 'delivered' ? 'success' : ($o['order_status'] === 'cancelled' ? 'danger' : 'info') ?>"><?= ucfirst($o['order_status']) ?></span></td>
                                <td><?= date('M d, Y', strtotime($o['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>