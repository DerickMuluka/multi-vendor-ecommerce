<?php
require_once __DIR__ . '/../includes/auth.php';
requireVendor();

$pageTitle = 'Add Product';
$vendorId = $_SESSION['vendor_id'];
$error = '';

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
        $error = 'Please fill all required fields';
    } else {
        $image = null;
        if (!empty($_FILES['image']['name'])) {
            $image = uploadImage($_FILES['image'], UPLOAD_PATH);
        }
        
        $slug = generateSlug($name) . '-' . time();
        
        $stmt = $pdo->prepare("
            INSERT INTO products (vendor_id, category_id, name, slug, description, price, discount_price, stock, image, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        try {
            $stmt->execute([$vendorId, $categoryId, $name, $slug, $description, $price, $discountPrice, $stock, $image, $status]);
            setFlashMessage('success', 'Product added successfully');
            redirect(SITE_URL . '/vendor/products.php');
        } catch (PDOException $e) {
            $error = 'Failed to add product';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-brand"><i class="fas fa-store"></i> Vendor Panel</div>
        <nav class="sidebar-nav">
            <a href="<?= SITE_URL ?>/vendor/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="<?= SITE_URL ?>/vendor/products.php"><i class="fas fa-box"></i> Products</a>
            <a href="<?= SITE_URL ?>/vendor/add-product.php" class="active"><i class="fas fa-plus-circle"></i> Add Product</a>
            <a href="<?= SITE_URL ?>/vendor/orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
            <a href="<?= SITE_URL ?>/vendor/profile.php"><i class="fas fa-user-cog"></i> Profile</a>
            <a href="<?= SITE_URL ?>/vendor/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>
    
    <div class="dashboard-content">
        <div class="page-header">
            <h1 class="page-title">Add Product</h1>
            <a href="<?= SITE_URL ?>/vendor/products.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
        
        <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
        
        <div class="table-card" style="padding: 28px;">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label>Product Name *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Category *</label>
                        <select name="category_id" class="form-control" required>
                            <option value="">Select category</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= sanitize($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="4"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Price (KSh) *</label>
                        <input type="number" name="price" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Discount Price (KSh)</label>
                        <input type="number" name="discount_price" class="form-control" step="0.01" min="0">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Stock Quantity *</label>
                        <input type="number" name="stock" class="form-control" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Product Image</label>
                    <input type="file" name="image" id="imageInput" class="form-control" accept="image/*">
                    <img id="imagePreview" style="display:none; max-width:200px; margin-top:12px; border-radius:var(--radius-sm);">
                </div>
                
                <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Save Product</button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>