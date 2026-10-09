<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$pageTitle = 'Categories';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $name = sanitize($_POST['name']);
        $slug = generateSlug($name);
        
        $check = $pdo->prepare("SELECT id FROM categories WHERE slug = ?");
        $check->execute([$slug]);
        if ($check->fetch()) {
            $error = 'Category already exists';
        } else {
            $pdo->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)")->execute([$name, $slug]);
            setFlashMessage('success', 'Category added');
            redirect(SITE_URL . '/admin/categories.php');
        }
    } elseif (isset($_POST['delete_id'])) {
        $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([(int)$_POST['delete_id']]);
        setFlashMessage('success', 'Category deleted');
        redirect(SITE_URL . '/admin/categories.php');
    }
}

$categories = $pdo->query("
    SELECT c.*, (SELECT COUNT(*) FROM products WHERE category_id = c.id) as product_count 
    FROM categories c ORDER BY c.name
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
            <a href="<?= SITE_URL ?>/admin/categories.php" class="active"><i class="fas fa-tags"></i> Categories</a>
            <a href="<?= SITE_URL ?>/admin/orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
            <a href="<?= SITE_URL ?>/admin/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>
    
    <div class="dashboard-content">
        <div class="page-header">
            <h1 class="page-title">Categories</h1>
        </div>
        
        <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
        
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px;" class="category-layout">
            <div class="table-card" style="padding: 24px;">
                <h3 style="margin-bottom: 16px;">Add Category</h3>
                <form method="POST">
                    <div class="form-group">
                        <label>Category Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <button type="submit" name="add" class="btn btn-primary btn-block"><i class="fas fa-plus"></i> Add</button>
                </form>
            </div>
            
            <div class="table-card">
                <div class="table-responsive">
                    <table>
                        <thead><tr><th>Name</th><th>Slug</th><th>Products</th><th>Action</th></tr></thead>
                        <tbody>
                            <?php foreach ($categories as $c): ?>
                                <tr>
                                    <td><strong><?= sanitize($c['name']) ?></strong></td>
                                    <td><?= sanitize($c['slug']) ?></td>
                                    <td><?= $c['product_count'] ?></td>
                                    <td>
                                        <form method="POST" onsubmit="return confirm('Delete?')">
                                            <input type="hidden" name="delete_id" value="<?= $c['id'] ?>">
                                            <button class="action-btn delete"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media (max-width: 768px) { .category-layout { grid-template-columns: 1fr !important; } }
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>