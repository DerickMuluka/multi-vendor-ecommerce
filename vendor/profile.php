<?php
require_once __DIR__ . '/../includes/auth.php';
requireVendor();

$pageTitle = 'Profile';
$vendorId = $_SESSION['vendor_id'];
$success = $error = '';

$stmt = $pdo->prepare("SELECT * FROM vendors WHERE id = ?");
$stmt->execute([$vendorId]);
$vendor = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $storeName = sanitize($_POST['store_name'] ?? '');
    $ownerName = sanitize($_POST['owner_name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    
    $update = $pdo->prepare("UPDATE vendors SET store_name = ?, owner_name = ?, phone = ?, address = ? WHERE id = ?");
    if ($update->execute([$storeName, $ownerName, $phone, $address, $vendorId])) {
        $_SESSION['vendor_name'] = $storeName;
        setFlashMessage('success', 'Profile updated');
        redirect(SITE_URL . '/vendor/profile.php');
    } else {
        $error = 'Update failed';
    }
}

if (!empty($_POST['new_password'])) {
    if (strlen($_POST['new_password']) < 6) {
        $error = 'Password must be at least 6 characters';
    } elseif ($_POST['new_password'] !== $_POST['confirm_password']) {
        $error = 'Passwords do not match';
    } else {
        $pdo->prepare("UPDATE vendors SET password = ? WHERE id = ?")
            ->execute([password_hash($_POST['new_password'], PASSWORD_DEFAULT), $vendorId]);
        setFlashMessage('success', 'Password changed');
        redirect(SITE_URL . '/vendor/profile.php');
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
            <a href="<?= SITE_URL ?>/vendor/add-product.php"><i class="fas fa-plus-circle"></i> Add Product</a>
            <a href="<?= SITE_URL ?>/vendor/orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
            <a href="<?= SITE_URL ?>/vendor/profile.php" class="active"><i class="fas fa-user-cog"></i> Profile</a>
            <a href="<?= SITE_URL ?>/vendor/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>
    
    <div class="dashboard-content">
        <div class="page-header">
            <h1 class="page-title">Store Profile</h1>
        </div>
        
        <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
        
        <div class="table-card" style="padding: 28px; margin-bottom: 24px;">
            <h3 style="margin-bottom: 20px;">Store Information</h3>
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>Store Name</label>
                        <input type="text" name="store_name" class="form-control" value="<?= sanitize($vendor['store_name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Owner Name</label>
                        <input type="text" name="owner_name" class="form-control" value="<?= sanitize($vendor['owner_name']) ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Email (read only)</label>
                        <input type="email" class="form-control" value="<?= sanitize($vendor['email']) ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" name="phone" class="form-control" value="<?= sanitize($vendor['phone']) ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" class="form-control" rows="2"><?= sanitize($vendor['address']) ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
            </form>
        </div>
        
        <div class="table-card" style="padding: 28px;">
            <h3 style="margin-bottom: 20px;">Change Password</h3>
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" class="form-control" minlength="6">
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" minlength="6">
                    </div>
                </div>
                <button type="submit" class="btn btn-outline"><i class="fas fa-key"></i> Change Password</button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>