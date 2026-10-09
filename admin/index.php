<?php
require_once __DIR__ . '/../includes/config.php';
if (isset($_SESSION['admin_id'])) {
    header("Location: " . SITE_URL . '/admin/dashboard.php');
} else {
    header("Location: " . SITE_URL . '/admin/login.php');
}
exit();
?>