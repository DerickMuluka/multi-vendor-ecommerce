<?php
require_once __DIR__ . '/includes/functions.php';

unset(
    $_SESSION['admin_id'], $_SESSION['admin_username'],
    $_SESSION['vendor_id'], $_SESSION['vendor_name'],
    $_SESSION['user_id'], $_SESSION['user_name']
);

setFlashMessage('success', 'You have been signed out');
header("Location: " . SITE_URL . '/login.php');
exit();
?>