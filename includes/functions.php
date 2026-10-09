<?php
require_once __DIR__ . '/db.php';

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function generateOrderNumber() {
    return 'ORD-' . strtoupper(uniqid());
}

function generateSlug($string) {
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
    return trim($slug, '-');
}

function formatPrice($price) {
    return 'KSh ' . number_format($price, 2);
}

function getCartCount($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetch();
    return $result['total'] ?? 0;
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function isVendorLoggedIn() {
    return isset($_SESSION['vendor_id']);
}

function isUserLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireAdmin() {
    if (!isAdminLoggedIn()) {
        redirect(SITE_URL . '/admin/login.php');
    }
}

function requireVendor() {
    if (!isVendorLoggedIn()) {
        redirect(SITE_URL . '/vendor/login.php');
    }
}

function requireUser() {
    if (!isUserLoggedIn()) {
        redirect(SITE_URL . '/user/login.php');
    }
}

function uploadImage($file, $targetDir) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        return null;
    }
    
    if ($file['size'] > 5 * 1024 * 1024) {
        return null;
    }
    
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('img_') . '.' . $extension;
    $targetPath = $targetDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $filename;
    }
    
    return null;
}

function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function setFlashMessage($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}
?>