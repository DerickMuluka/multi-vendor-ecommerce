<?php
require_once __DIR__ . '/includes/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['account_type'] ?? 'user';
    
    if (!in_array($type, ['user', 'vendor'])) {
        $error = 'Invalid account type';
    } else {
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email';
        } else {
            if ($type === 'user') {
                $result = registerUser($pdo, [
                    'full_name' => sanitize($_POST['full_name'] ?? ''),
                    'email' => $email,
                    'phone' => sanitize($_POST['phone'] ?? ''),
                    'password' => $password,
                    'address' => sanitize($_POST['address'] ?? '')
                ]);
            } else {
                $result = registerVendor($pdo, [
                    'store_name' => sanitize($_POST['store_name'] ?? ''),
                    'owner_name' => sanitize($_POST['owner_name'] ?? ''),
                    'email' => $email,
                    'phone' => sanitize($_POST['phone'] ?? ''),
                    'password' => $password,
                    'address' => sanitize($_POST['address'] ?? '')
                ]);
            }
            
            if ($result === true) {
                $msg = $type === 'vendor' 
                    ? 'Vendor account created! Await admin approval before signing in.'
                    : 'Account created! You can now sign in.';
                setFlashMessage('success', $msg);
                redirect(SITE_URL . '/login.php');
            } elseif ($result === 'email_exists') {
                $error = 'That email is already registered';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | <?= SITE_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>
<body>
    <div class="auth-wrapper" style="background-image: url('https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=1600&q=80');">
        <div class="auth-overlay"></div>
        
        <div class="auth-container">
            <a href="<?= SITE_URL ?>/index.php" class="auth-logo">
                <i class="fas fa-store"></i> <?= SITE_NAME ?>
            </a>
            
            <div class="auth-card" style="max-width: 540px;">
                <div class="auth-header">
                    <h1>Create Account</h1>
                    <p>Join <?= SITE_NAME ?> today</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
                <?php endif; ?>
                
                <div class="tabs">
                    <button type="button" class="tab active" data-tab="user">
                        <i class="fas fa-user"></i> Customer
                    </button>
                    <button type="button" class="tab" data-tab="vendor">
                        <i class="fas fa-store"></i> Vendor
                    </button>
                </div>
                
                <form method="POST" id="registerForm">
                    <input type="hidden" name="account_type" id="accountType" value="user">
                    
                    <!-- Customer fields -->
                    <div class="tab-pane active" data-pane="user">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="full_name" class="form-control" placeholder="Your full name">
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="tel" name="phone" class="form-control" placeholder="07XX XXX XXX">
                        </div>
                    </div>
                    
                    <!-- Vendor fields -->
                    <div class="tab-pane" data-pane="vendor">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Store Name</label>
                                <input type="text" name="store_name" class="form-control" placeholder="Your store name">
                            </div>
                            <div class="form-group">
                                <label>Owner Name</label>
                                <input type="text" name="owner_name" class="form-control" placeholder="Owner full name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Business Phone</label>
                            <input type="tel" name="phone_vendor" class="form-control" placeholder="07XX XXX XXX">
                        </div>
                    </div>
                    
                    <!-- Common fields -->
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="you@example.com" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" placeholder="At least 6 characters" required minlength="6">
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="Delivery / business address"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        <i class="fas fa-user-plus"></i> Create Account
                    </button>
                </form>
                
                <div class="auth-footer">
                    Have an account? <a href="<?= SITE_URL ?>/login.php">Sign in</a>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Tab switching
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', () => {
                const target = tab.dataset.tab;
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
                tab.classList.add('active');
                document.querySelector(`.tab-pane[data-pane="${target}"]`).classList.add('active');
                document.getElementById('accountType').value = target;
                
                // Move phone field appropriately
                const phoneUser = document.querySelector('input[name="phone"]');
                const phoneVendor = document.querySelector('input[name="phone_vendor"]');
                if (target === 'vendor') {
                    phoneUser.removeAttribute('name');
                    phoneVendor.setAttribute('name', 'phone');
                } else {
                    phoneVendor.removeAttribute('name');
                    phoneUser.setAttribute('name', 'phone');
                }
            });
        });
    </script>
</body>
</html>