<?php
require_once __DIR__ . '/includes/auth.php';

// Already logged in? Redirect by role
if (isAdminLoggedIn())  redirect(SITE_URL . '/admin/dashboard.php');
if (isVendorLoggedIn()) redirect(SITE_URL . '/vendor/dashboard.php');
if (isUserLoggedIn())   redirect(SITE_URL . '/user/dashboard.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        // 1. Try admin (username or email)
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ? OR email = ?");
        $stmt->execute([$email, $email]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            setFlashMessage('success', 'Signed in as Administrator');
            redirect(SITE_URL . '/admin/dashboard.php');
        }
        
        // 2. Try vendor
        $stmt = $pdo->prepare("SELECT * FROM vendors WHERE email = ?");
        $stmt->execute([$email]);
        $vendor = $stmt->fetch();
        
        if ($vendor && password_verify($password, $vendor['password'])) {
            if ($vendor['status'] !== 'active') {
                $error = 'Your vendor account is ' . $vendor['status'] . '. Contact support.';
            } else {
                $_SESSION['vendor_id'] = $vendor['id'];
                $_SESSION['vendor_name'] = $vendor['store_name'];
                setFlashMessage('success', 'Signed in as ' . $vendor['store_name']);
                redirect(SITE_URL . '/vendor/dashboard.php');
            }
        }
        
        // 3. Try user
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] !== 'active') {
                $error = 'Your account has been blocked. Contact support.';
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                setFlashMessage('success', 'Signed in successfully');
                redirect(SITE_URL . '/user/dashboard.php');
            }
        }
        
        if (empty($error)) $error = 'Invalid email or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | <?= SITE_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>
<body>
    <div class="auth-wrapper" style="background-image: url('https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1600&q=80');">
        <div class="auth-overlay"></div>
        
        <div class="auth-container">
            <a href="<?= SITE_URL ?>/index.php" class="auth-logo">
                <i class="fas fa-store"></i> <?= SITE_NAME ?>
            </a>
            
            <div class="auth-card">
                <div class="auth-header">
                    <h1>Sign In</h1>
                    <p>Access your account</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label>Email or Username</label>
                        <input type="text" name="email" class="form-control" placeholder="you@example.com" required autofocus>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        <i class="fas fa-sign-in-alt"></i> Sign In
                    </button>
                </form>
                
                <div class="auth-footer">
                    New here? <a href="<?= SITE_URL ?>/register.php">Create an account</a>
                </div>
            </div>
            
            <div class="auth-demo">
                <p><strong>Demo credentials</strong></p>
                <div class="demo-grid">
                    <div><i class="fas fa-shield-alt"></i> admin / admin123</div>
                    <div><i class="fas fa-store"></i> vendor@example.com / vendor123</div>
                    <div><i class="fas fa-user"></i> user@example.com / user123</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>