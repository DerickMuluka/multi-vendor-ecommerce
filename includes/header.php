<?php
require_once __DIR__ . '/functions.php';
$cartCount = isUserLoggedIn() ? getCartCount($pdo, $_SESSION['user_id']) : 0;
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' | ' . SITE_NAME : SITE_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>
<body>
<header class="main-header">
    <div class="header-container">
        <a href="<?= SITE_URL ?>/index.php" class="logo">
            <span class="logo-icon"><i class="fas fa-store"></i></span>
            <span class="logo-text"><?= SITE_NAME ?></span>
        </a>
        
        <nav class="main-nav" id="mainNav">
            <a href="<?= SITE_URL ?>/index.php">Home</a>
            <a href="<?= SITE_URL ?>/products.php">Products</a>
            <a href="<?= SITE_URL ?>/vendors.php">Vendors</a>
        </nav>
        
        <div class="header-actions">
            <?php if (isUserLoggedIn()): ?>
                <a href="<?= SITE_URL ?>/user/cart.php" class="cart-btn">
                    <i class="fas fa-shopping-bag"></i>
                    <?php if ($cartCount > 0): ?><span class="cart-badge"><?= $cartCount ?></span><?php endif; ?>
                </a>
                <div class="user-dropdown">
                    <button class="user-btn">
                        <i class="fas fa-user"></i>
                        <span><?= explode(' ', $_SESSION['user_name'])[0] ?></span>
                    </button>
                    <div class="dropdown-menu">
                        <a href="<?= SITE_URL ?>/user/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                        <a href="<?= SITE_URL ?>/user/orders.php"><i class="fas fa-box"></i> Orders</a>
                        <a href="<?= SITE_URL ?>/user/profile.php"><i class="fas fa-cog"></i> Settings</a>
                        <a href="<?= SITE_URL ?>/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            <?php elseif (isVendorLoggedIn()): ?>
                <a href="<?= SITE_URL ?>/vendor/dashboard.php" class="btn btn-outline"><i class="fas fa-store"></i> Vendor Panel</a>
                <a href="<?= SITE_URL ?>/logout.php" class="btn btn-primary">Logout</a>
            <?php elseif (isAdminLoggedIn()): ?>
                <a href="<?= SITE_URL ?>/admin/dashboard.php" class="btn btn-outline"><i class="fas fa-shield-alt"></i> Admin Panel</a>
                <a href="<?= SITE_URL ?>/logout.php" class="btn btn-primary">Logout</a>
            <?php else: ?>
                <a href="<?= SITE_URL ?>/login.php" class="btn btn-outline"><i class="fas fa-sign-in-alt"></i> Sign In</a>
                <a href="<?= SITE_URL ?>/register.php" class="btn btn-primary"><i class="fas fa-store"></i> Sell</a>
            <?php endif; ?>
            
            <button class="mobile-toggle" id="mobileToggle"><i class="fas fa-bars"></i></button>
        </div>
    </div>
</header>

<?php $flash = getFlashMessage(); if ($flash): ?>
<div class="flash-message flash-<?= $flash['type'] ?>" id="flashMessage">
    <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : ($flash['type'] === 'error' ? 'exclamation-circle' : 'info-circle') ?>"></i>
    <span><?= $flash['message'] ?></span>
    <button onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
</div>
<?php endif; ?>

<main class="main-content">