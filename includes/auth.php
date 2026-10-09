<?php
require_once __DIR__ . '/functions.php';

// Admin authentication
function adminLogin($pdo, $username, $password) {
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $admin = $stmt->fetch();
    
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        return true;
    }
    return false;
}

// Vendor authentication
function vendorLogin($pdo, $email, $password) {
    $stmt = $pdo->prepare("SELECT * FROM vendors WHERE email = ?");
    $stmt->execute([$email]);
    $vendor = $stmt->fetch();
    
    if ($vendor && password_verify($password, $vendor['password'])) {
        if ($vendor['status'] !== 'active') {
            return 'inactive';
        }
        $_SESSION['vendor_id'] = $vendor['id'];
        $_SESSION['vendor_name'] = $vendor['store_name'];
        return true;
    }
    return false;
}

// User authentication
function userLogin($pdo, $email, $password) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        if ($user['status'] !== 'active') {
            return 'blocked';
        }
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        return true;
    }
    return false;
}

// Register vendor
function registerVendor($pdo, $data) {
    $stmt = $pdo->prepare("SELECT id FROM vendors WHERE email = ?");
    $stmt->execute([$data['email']]);
    if ($stmt->fetch()) {
        return 'email_exists';
    }
    
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO vendors (store_name, owner_name, email, phone, password, address) VALUES (?, ?, ?, ?, ?, ?)");
    
    try {
        $stmt->execute([
            $data['store_name'],
            $data['owner_name'],
            $data['email'],
            $data['phone'],
            $hashedPassword,
            $data['address'] ?? ''
        ]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// Register user
function registerUser($pdo, $data) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$data['email']]);
    if ($stmt->fetch()) {
        return 'email_exists';
    }
    
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, password, address) VALUES (?, ?, ?, ?, ?)");
    
    try {
        $stmt->execute([
            $data['full_name'],
            $data['email'],
            $data['phone'] ?? '',
            $hashedPassword,
            $data['address'] ?? ''
        ]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}
?>