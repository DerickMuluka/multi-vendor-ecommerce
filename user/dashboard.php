<?php
require_once __DIR__ . '/../includes/auth.php';
requireUser();

$pageTitle = 'My Account';
$userId = $_SESSION['user_id'];

$orderStmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
$orderStmt->execute([$userId]);
$orderCount = $orderStmt->fetchColumn();

$pendingStmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ? AND order_status IN ('pending','processing','shipped')");
$pendingStmt->execute([$userId]);
$activeOrders = $pendingStmt->fetchColumn();

$spentStmt = $pdo->prepare("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE user_id = ? AND order_status != 'cancelled'");
$spentStmt->execute([$userId]);
$totalSpent = $spentStmt->fetchColumn();

$recentStmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$recentStmt->execute([$userId]);
$recentOrders = $recentStmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container" style="padding-top: 40px;">
    <div class="page-header">
        <div>
            <h1 class="page-title"><?= sanitize($_SESSION['user_name']) ?></h1>
            <p style="color: var(--gray-500); font-size: 0.9rem; margin-top: 4px;">Account overview</p>
        </div>
        <a href="<?= SITE_URL ?>/products.php" class="btn btn-primary"><i class="fas fa-shopping-bag"></i> Continue Shopping</a>
    </div>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon purple"><i class="fas fa-shopping-cart"></i></div>
            <div class="stat-info"><h3><?= $orderCount ?></h3><p>Total Orders</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon orange"><i class="fas fa-truck"></i></div>
            <div class="stat-info"><h3><?= $activeOrders ?></h3><p>Active Orders</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-money-bill-wave"></i></div>
            <div class="stat-info"><h3><?= formatPrice($totalSpent) ?></h3><p>Total Spent</p></div>
        </div>
    </div>
    
    <div class="table-card">
        <div class="table-header">
            <h3>Recent Orders</h3>
            <a href="<?= SITE_URL ?>/user/orders.php" class="btn btn-outline btn-sm">View All</a>
        </div>
        <div class="table-responsive">
            <table>
                <thead><tr><th>Order #</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>
                    <?php if (empty($recentOrders)): ?>
                        <tr><td colspan="4" style="text-align:center; padding:40px; color:var(--gray-500);">No orders yet. <a href="<?= SITE_URL ?>/products.php" style="color:var(--primary); font-weight:600;">Start shopping</a></td></tr>
                    <?php else: foreach ($recentOrders as $o): ?>
                        <tr>
                            <td><strong><?= $o['order_number'] ?></strong></td>
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

<?php require_once __DIR__ . '/../includes/footer.php'; ?>