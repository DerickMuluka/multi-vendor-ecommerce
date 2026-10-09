<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$pageTitle = 'Manage Vendors';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $action = $_POST['action'] ?? '';
    
    if ($action === 'approve') {
        $pdo->prepare("UPDATE vendors SET status = 'active' WHERE id = ?")->execute([$id]);
        setFlashMessage('success', 'Vendor approved');
    } elseif ($action === 'suspend') {
        $pdo->prepare("UPDATE vendors SET status = 'suspended' WHERE id = ?")->execute([$id]);
        setFlashMessage('success', 'Vendor suspended');
    } elseif ($action === 'delete') {
        $pdo->prepare("DELETE FROM vendors WHERE id = ?")->execute([$id]);
        setFlashMessage('success', 'Vendor deleted');
    }
    redirect(SITE_URL . '/admin/vendors.php');
}

$vendors = $pdo->query("
    SELECT v.*, (SELECT COUNT(*) FROM products WHERE vendor_id = v.id) as product_count 
    FROM vendors v ORDER BY v.created_at DESC
")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-brand"><i class="fas fa-shield-alt"></i> Admin Panel</div>
        <nav class="sidebar-nav">
            <a href="<?= SITE_URL ?>/admin/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="<?= SITE_URL ?>/admin/vendors.php" class="active"><i class="fas fa-store"></i> Vendors</a>
            <a href="<?= SITE_URL ?>/admin/users.php"><i class="fas fa-users"></i> Users</a>
            <a href="<?= SITE_URL ?>/admin/products.php"><i class="fas fa-box"></i> Products</a>
            <a href="<?= SITE_URL ?>/admin/categories.php"><i class="fas fa-tags"></i> Categories</a>
            <a href="<?= SITE_URL ?>/admin/orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
            <a href="<?= SITE_URL ?>/admin/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>
    
    <div class="dashboard-content">
        <div class="page-header">
            <h1 class="page-title">Vendors (<?= count($vendors) ?>)</h1>
        </div>
        
        <div class="table-card">
            <div class="table-responsive">
                <table>
                    <thead><tr><th>Store</th><th>Owner</th><th>Email</th><th>Products</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php if (empty($vendors)): ?>
                            <tr><td colspan="6" style="text-align:center; padding:40px;">No vendors</td></tr>
                        <?php else: foreach ($vendors as $v): ?>
                            <tr>
                                <td><strong><?= sanitize($v['store_name']) ?></strong></td>
                                <td><?= sanitize($v['owner_name']) ?></td>
                                <td><?= sanitize($v['email']) ?></td>
                                <td><?= $v['product_count'] ?></td>
                                <td><span class="badge badge-<?= $v['status'] === 'active' ? 'success' : ($v['status'] === 'pending' ? 'warning' : 'danger') ?>"><?= ucfirst($v['status']) ?></span></td>
                                <td>
                                    <div class="action-btns">
                                        <?php if ($v['status'] !== 'active'): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="id" value="<?= $v['id'] ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <button class="action-btn view" title="Approve"><i class="fas fa-check"></i></button>
                                        </form>
                                        <?php endif; ?>
                                        <?php if ($v['status'] === 'active'): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="id" value="<?= $v['id'] ?>">
                                            <input type="hidden" name="action" value="suspend">
                                            <button class="action-btn edit" title="Suspend"><i class="fas fa-ban"></i></button>
                                        </form>
                                        <?php endif; ?>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this vendor?')">
                                            <input type="hidden" name="id" value="<?= $v['id'] ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <button class="action-btn delete" title="Delete"><i class="fas fa-trash"></i></button>
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