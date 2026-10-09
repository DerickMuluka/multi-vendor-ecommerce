<?php
require_once __DIR__ . '/../includes/auth.php';
requireVendor();

$pageTitle = 'Orders';
$vendorId = $_SESSION['vendor_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $pdo->prepare("UPDATE orders SET order_status = ? WHERE id = ?")->execute([$_POST['status'], (int)$_POST['order_id']]);
    setFlashMessage('success', 'Order status updated');
    redirect(SITE_URL . '/vendor/orders.php');
}

$stmt = $pdo->prepare("
    SELECT DISTINCT o.*, u.full_name, u.email, u.phone as user_phone
    FROM orders o 
    JOIN order_items oi ON o.id = oi.order_id 
    JOIN users u ON o.user_id = u.id 
    WHERE oi.vendor_id = ? 
    ORDER BY o.created_at DESC
");
$stmt->execute([$vendorId]);
$orders = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-brand"><i class="fas fa-store"></i> Vendor Panel</div>
        <nav class="sidebar-nav">
            <a href="<?= SITE_URL ?>/vendor/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="<?= SITE_URL ?>/vendor/products.php"><i class="fas fa-box"></i> Products</a>
            <a href="<?= SITE_URL ?>/vendor/add-product.php"><i class="fas fa-plus-circle"></i> Add Product</a>
            <a href="<?= SITE_URL ?>/vendor/orders.php" class="active"><i class="fas fa-shopping-cart"></i> Orders</a>
            <a href="<?= SITE_URL ?>/vendor/profile.php"><i class="fas fa-user-cog"></i> Profile</a>
            <a href="<?= SITE_URL ?>/vendor/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>
    
    <div class="dashboard-content">
        <div class="page-header">
            <h1 class="page-title">Orders (<?= count($orders) ?>)</h1>
        </div>
        
        <div class="table-card">
            <div class="table-responsive">
                <table>
                    <thead><tr><th>Order #</th><th>Customer</th><th>Amount</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr><td colspan="6" style="text-align:center; padding:40px; color:var(--gray-500);">No orders yet</td></tr>
                        <?php else: foreach ($orders as $o): ?>
                            <tr>
                                <td><strong><?= $o['order_number'] ?></strong></td>
                                <td><?= sanitize($o['full_name']) ?><br><small style="color:var(--gray-500)"><?= sanitize($o['user_phone']) ?></small></td>
                                <td><?= formatPrice($o['total_amount']) ?></td>
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