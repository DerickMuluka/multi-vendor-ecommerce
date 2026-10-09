<?php
require_once __DIR__ . '/../includes/auth.php';
requireUser();

$pageTitle = 'Settings';
$userId = $_SESSION['user_id'];
$error = '';

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $fullName = sanitize($_POST['full_name'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $address = sanitize($_POST['address'] ?? '');
        
        $pdo->prepare("UPDATE users SET full_name = ?, phone = ?, address = ? WHERE id = ?")
            ->execute([$fullName, $phone, $address, $userId]);
        $_SESSION['user_name'] = $fullName;
        setFlashMessage('success', 'Profile updated');
        redirect(SITE_URL . '/user/profile.php');
    }
    
    if (isset($_POST['change_password'])) {
        $newPass = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        
        if (strlen($newPass) < 6) {
            $error = 'Password must be at least 6 characters';
        } elseif ($newPass !== $confirm) {
            $error = 'Passwords do not match';
        } else {
            $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")
                ->execute([password_hash($newPass, PASSWORD_DEFAULT), $userId]);
            setFlashMessage('success', 'Password changed');
            redirect(SITE_URL . '/user/profile.php');
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container" style="padding-top: 40px;">
    <div class="page-header">
        <h1 class="page-title">Account Settings</h1>
    </div>
    
    <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;" class="profile-grid">
        <div class="table-card" style="padding: 28px;">
            <h3 style="margin-bottom: 20px;">Personal Information</h3>
            <form method="POST">
                <input type="hidden" name="update_profile" value="1">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" class="form-control" value="<?= sanitize($user['full_name']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" value="<?= sanitize($user['email']) ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="tel" name="phone" class="form-control" value="<?= sanitize($user['phone']) ?>">
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" class="form-control" rows="3"><?= sanitize($user['address']) ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
            </form>
        </div>
        
        <div class="table-card" style="padding: 28px;">
            <h3 style="margin-bottom: 20px;">Change Password</h3>
            <form method="POST">
                <input type="hidden" name="change_password" value="1">
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" class="form-control" minlength="6">
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control" minlength="6">
                </div>
                <button type="submit" class="btn btn-outline"><i class="fas fa-key"></i> Update Password</button>
            </form>
        </div>
    </div>
</div>

<style>
@media (max-width: 768px) { .profile-grid { grid-template-columns: 1fr !important; } }
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>