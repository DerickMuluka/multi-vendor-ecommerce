<?php
require_once __DIR__ . '/../includes/auth.php';
requireVendor();

$vendorId = $_SESSION['vendor_id'];
$id = (int)($_GET['id'] ?? 0);
$error = '';

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND vendor_id = ?");
$stmt->execute([$id, $vendorId]);
$product = $stmt->fetch();

if (!$product) redirect(SITE_URL . '/vendor/products.php');

$pageTitle = 'Edit Product';
$categories = $pdo->query("SELECT * FROM categories WHERE status = 'active' ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $description = sanitize($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $discountPrice = !empty($_POST['discount_price']) ? (float)$_POST['discount_price'] : null;
    $stock = (int)($_POST['stock'] ?? 0);
    $status = $_POST['status'] ?? 'active';
    
    if (empty($name) || $categoryId <= 0 || $price <= 0) {
        $error = 'Please fill required fields';
    } else {
        $image = $product['image'];
        if (!empty($_FILES['image']['name'])) {
            $newImage = uploadImage($_FILES['image'], UPLOAD_PATH);
            if ($newImage) {
                if ($image && file_exists(UPLOAD_PATH . $image)) unlink(UPLOAD_PATH . $image);
                $image = $newImage;
            }
        }
        
        $update = $pdo->prepare("
            UPDATE products SET category_id = ?, name = ?, description = ?, price = ?, discount_price = ?, stock = ?, image = ?, status = ? 
            WHERE id = ? AND vendor_id = ?
        ");
        $update->execute([$categoryId, $name, $description, $price, $discountPrice, $stock, $image, $status, $id, $vendorId]);
        
        setFlashMessage('success', 'Product updated');
        redirect(SITE_URL . '/vendor/products.php');
    }
}

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
            <h1 class="page-title">Edit Product</h1>
            <a href="<?= SITE_URL ?>/vendor/products.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
        
        <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
        
        <div class="table-card" style="padding: 28px;">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label>Product Name *</label>
                        <input type="text" name="name" class="form-control" value="<?= sanitize($product['name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Category *</label>
                        <select name="category_id" class="form-control" required>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= $product['category_id'] == $c['id'] ? 'selected' : '' ?>><?= sanitize($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="4"><?= sanitize($product['description']) ?></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Price (KSh) *</label>
                        <input type="number" name="price" class="form-control" step="0.01" value="<?= $product['price'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Discount Price</label>
                        <input type="number" name="discount_price" class="form-control" step="0.01" value="<?= $product['discount_price'] ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Stock *</label>
                        <input type="number" name="stock" class="form-control" value="<?= $product['stock'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="active" <?= $product['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $product['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Product Image</label>
                    <?php if ($product['image']): ?>
                        <img src="<?= UPLOAD_URL . $product['image'] ?>" style="max-width:120px; display:block; margin-bottom:10px; border-radius:var(--radius-sm);">
                    <?php endif; ?>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
                
                <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Update Product</button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>