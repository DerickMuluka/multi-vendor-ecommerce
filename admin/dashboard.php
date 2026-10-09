<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$pageTitle = 'Admin Dashboard';

$stats = [
    'vendors' => $pdo->query("SELECT COUNT(*) FROM vendors")->fetchColumn(),
    'pending_vendors' => $pdo->query("SELECT COUNT(*) FROM vendors WHERE status = 'pending'")->fetchColumn(),
    'users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'products' => $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn(),
    'orders' => $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
    'revenue' => $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE order_status != 'cancelled'")->fetchColumn(),
];

$recentOrders = $pdo->query("
    SELECT o.*, u.full_name FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC LIMIT 5
")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-brand"><i class="fas fa-shield-alt"></i> Admin Panel</div>
        <nav class="sidebar-nav">
            <a href="<?= SITE_URL ?>/admin/dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="<?= SITE_URL ?>/admin/vendors.php"><i class="fas fa-store"></i> Vendors</a>
            <a href="<?= SITE_URL ?>/admin/users.php"><i class="fas fa-users"></i> Users</a>
            <a href="<?= SITE_URL ?>/admin/products.php"><i class="fas fa-box"></i> Products</a>
            <a href="<?= SITE_URL ?>/admin/categories.php"><i class="fas fa-tags"></i> Categories</a>
            <a href="<?= SITE_URL ?>/admin/orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
            <a href="<?= SITE_URL ?>/admin/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>
    
    <div class="dashboard-content">
        <div class="page-header">
            <div>
                <h1 class="page-title">Dashboard</h1>
                <p style="color: var(--gray-500); font-size: 0.9rem; margin-top: 4px;">Marketplace overview</p>
            </div>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon purple"><i class="fas fa-store"></i></div>
                <div class="stat-info"><h3><?= $stats['vendors'] ?></h3><p>Vendors</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange"><i class="fas fa-clock"></i></div>
                <div class="stat-info"><h3><?= $stats['pending_vendors'] ?></h3><p>Pending</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fas fa-users"></i></div>
                <div class="stat-info"><h3><?= $stats['users'] ?></h3><p>Users</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fas fa-box"></i></div>
                <div class="stat-info"><h3><?= $stats['products'] ?></h3><p>Products</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple"><i class="fas fa-shopping-cart"></i></div>
                <div class="stat-info"><h3><?= $stats['orders'] ?></h3><p>Orders</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fas fa-money-bill-wave"></i></div>
                <div class="stat-info"><h3><?= formatPrice($stats['revenue']) ?></h3><p>Revenue</p></div>
            </div>
        </div>
        
        <div class="table-card">
            <div class="table-header">
                <h3>Recent Orders</h3>
                <a href="<?= SITE_URL ?>/admin/orders.php" class="btn btn-outline btn-sm">View All</a>
            </div>
            <div class="table-responsive">
                <table>
                    <thead><tr><th>Order #</th><th>Customer</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody>
                        <?php if (empty($recentOrders)): ?>
                            <tr><td colspan="5" style="text-align:center; padding: 40px; color: var(--gray-500);">No orders yet</td></tr>
                        <?php else: foreach ($recentOrders as $order): ?>
                            <tr>
                                <td><strong><?= $order['order_number'] ?></strong></td>
                                <td><?= sanitize($order['full_name']) ?></td>
                                <td><?= formatPrice($order['total_amount']) ?></td>
                                <td><span class="badge badge-<?= $order['order_status'] === 'delivered' ? 'success' : ($order['order_status'] === 'pending' ? 'warning' : 'info') ?>"><?= ucfirst($order['order_status']) ?></span></td>
                                <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>