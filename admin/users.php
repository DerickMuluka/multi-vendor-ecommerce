<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$pageTitle = 'Manage Users';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $action = $_POST['action'] ?? '';
    
    if ($action === 'block') {
        $pdo->prepare("UPDATE users SET status = 'blocked' WHERE id = ?")->execute([$id]);
        setFlashMessage('success', 'User blocked');
    } elseif ($action === 'unblock') {
        $pdo->prepare("UPDATE users SET status = 'active' WHERE id = ?")->execute([$id]);
        setFlashMessage('success', 'User unblocked');
    } elseif ($action === 'delete') {
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
        setFlashMessage('success', 'User deleted');
    }
    redirect(SITE_URL . '/admin/users.php');
}

$users = $pdo->query("
    SELECT u.*, (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as order_count 
    FROM users u ORDER BY u.created_at DESC
")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-brand"><i class="fas fa-shield-alt"></i> Admin Panel</div>
        <nav class="sidebar-nav">
            <a href="<?= SITE_URL ?>/admin/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="<?= SITE_URL ?>/admin/vendors.php"><i class="fas fa-store"></i> Vendors</a>
            <a href="<?= SITE_URL ?>/admin/users.php" class="active"><i class="fas fa-users"></i> Users</a>
            <a href="<?= SITE_URL ?>/admin/products.php"><i class="fas fa-box"></i> Products</a>
            <a href="<?= SITE_URL ?>/admin/categories.php"><i class="fas fa-tags"></i> Categories</a>
            <a href="<?= SITE_URL ?>/admin/orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
            <a href="<?= SITE_URL ?>/admin/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>
    
    <div class="dashboard-content">
        <div class="page-header">
            <h1 class="page-title">Users (<?= count($users) ?>)</h1>
        </div>
        
        <div class="table-card">
            <div class="table-responsive">
                <table>
                    <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Orders</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr><td colspan="6" style="text-align:center; padding:40px;">No users</td></tr>
                        <?php else: foreach ($users as $u): ?>
                            <tr>
                                <td><strong><?= sanitize($u['full_name']) ?></strong></td>
                                <td><?= sanitize($u['email']) ?></td>
                                <td><?= sanitize($u['phone']) ?></td>
                                <td><?= $u['order_count'] ?></td>
                                <td><span class="badge badge-<?= $u['status'] === 'active' ? 'success' : 'danger' ?>"><?= ucfirst($u['status']) ?></span></td>
                                <td>
                                    <div class="action-btns">
                                        <?php if ($u['status'] === 'active'): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                            <input type="hidden" name="action" value="block">
                                            <button class="action-btn edit" title="Block"><i class="fas fa-ban"></i></button>
                                        </form>
                                        <?php else: ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                            <input type="hidden" name="action" value="unblock">
                                            <button class="action-btn view" title="Unblock"><i class="fas fa-check"></i></button>
                                        </form>
                                        <?php endif; ?>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Delete user?')">
                                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                            <input type="hidden" name="action" value="delete">
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