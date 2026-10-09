<?php
require_once __DIR__ . '/../includes/config.php';
unset($_SESSION['user_id'], $_SESSION['user_name']);
header("Location: " . SITE_URL . '/user/login.php');
exit();
?>