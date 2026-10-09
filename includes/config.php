<?php
session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'multi_vendor_ecommerce');

// Site configuration
define('SITE_URL', 'http://localhost/multi-vendor-ecommerce');
define('SITE_NAME', 'MarketHub');

// Upload paths
define('UPLOAD_PATH', __DIR__ . '/../uploads/products/');
define('UPLOAD_URL', SITE_URL . '/uploads/products/');

// Timezone
date_default_timezone_set('Africa/Nairobi');

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>