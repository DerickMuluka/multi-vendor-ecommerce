<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$pageTitle = 'Orders';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $pdo->prepare("UPDATE orders SET order_status = ? WHERE id = ?")->execute([$_POST['status'], (int)$_POST['order_id']]);
    setFlashMessage('success', 'Order status updated');
    redirect(SITE_URL . '/admin/orders.php');
}

$orders = $pdo->query("
    SELECT o.*, u.full_name, u.email 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC
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
            <a href="<?= SITE_URL ?>/admin/products.php"><i class="fas fa-box"></i> Products</a>
            <a href="<?= SITE_URL ?>/admin/categories.php"><i class="fas fa-tags"></i> Categories</a>
            <a href="<?= SITE_URL ?>/admin/orders.php" class="active"><i class="fas fa-shopping-cart"></i> Orders</a>
            <a href="<?= SITE_URL ?>/admin/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>
    
    <div class="dashboard-content">
        <div class="page-header">
            <h1 class="page-title">Orders (<?= count($orders) ?>)</h1>
        </div>
        
        <div class="table-card">
            <div class="table-responsive">
                <table>
                    <thead><tr><th>Order #</th><th>Customer</th><th>Amount</th><th>Payment</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr><td colspan="7" style="text-align:center; padding:40px;">No orders</td></tr>
                        <?php else: foreach ($orders as $o): ?>
                            <tr>
                                <td><strong><?= $o['order_number'] ?></strong></td>
                                <td><?= sanitize($o['full_name']) ?><br><small style="color:var(--gray-500)"><?= sanitize($o['email']) ?></small></td>
                                <td><?= formatPrice($o['total_amount']) ?></td>
                                <td><span class="badge badge-<?= $o['payment_status'] === 'paid' ? 'success' : 'warning' ?>"><?= ucfirst($o['payment_status']) ?></span></td>
                                <td><span class="badge badge-<?= $o['order_status'] === 'delivered' ? 'success' : ($o['order_status'] === 'cancelled' ? 'danger' : 'info') ?>"><?= ucfirst($o['order_status']) ?></span></td>
                                <td><?= date('M d, Y', strtotime($o['created_at'])) ?></td>
                                <td>
                                    <form method="POST" style="display:flex; gap:6px;">
                                        <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                        <select name="status" class="form-control" style="width:auto; padding:6px 10px; font-size:0.8rem;">
                                            <?php foreach (['pending','processing','shipped','delivered','cancelled'] as $s): ?>
                                                <option value="<?= $s ?>" <?= $o['order_status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button class="action-btn view"><i class="fas fa-check"></i></button>
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