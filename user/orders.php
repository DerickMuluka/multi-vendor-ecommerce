<?php
require_once __DIR__ . '/../includes/auth.php';
requireUser();

$pageTitle = 'My Orders';
$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container" style="padding-top: 40px;">
    <div class="page-header">
        <h1 class="page-title">My Orders</h1>
    </div>
    
    <?php if (empty($orders)): ?>
        <div class="empty-state">
            <i class="fas fa-box-open"></i>
            <h3>No orders yet</h3>
            <p>Your order history will appear here</p>
            <a href="<?= SITE_URL ?>/products.php" class="btn btn-primary">Start Shopping</a>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): 
            $itemsStmt = $pdo->prepare("
                SELECT oi.*, p.name, p.image, v.store_name 
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.id 
                JOIN vendors v ON oi.vendor_id = v.id 
                WHERE oi.order_id = ?
            ");
            $itemsStmt->execute([$order['id']]);
            $items = $itemsStmt->fetchAll();
        ?>
        <div class="table-card" style="margin-bottom: 20px;">
            <div class="table-header">
                <div>
                    <h3><?= $order['order_number'] ?></h3>
                    <p style="font-size:0.85rem; color:var(--gray-500); margin-top:4px;"><?= date('F d, Y g:i A', strtotime($order['created_at'])) ?></p>
                </div>
                <span class="badge badge-<?= $order['order_status'] === 'delivered' ? 'success' : ($order['order_status'] === 'cancelled' ? 'danger' : 'info') ?>"><?= ucfirst($order['order_status']) ?></span>
            </div>
            <div style="padding: 20px;">
                <?php foreach ($items as $item): ?>
                    <div style="display:flex; gap:14px; padding:12px 0; border-bottom:1px solid var(--gray-100); align-items:center;">
                        <img src="<?= $item['image'] ? UPLOAD_URL . $item['image'] : 'https://via.placeholder.com/60' ?>" style="width:56px; height:56px; border-radius:var(--radius-sm); object-fit:cover;">
                        <div style="flex:1;">
                            <strong><?= sanitize($item['name']) ?></strong>
                            <p style="font-size:0.8rem; color:var(--gray-500);"><?= sanitize($item['store_name']) ?> · Qty: <?= $item['quantity'] ?></p>
                        </div>
                        <span style="font-weight:700;"><?= formatPrice($item['subtotal']) ?></span>
                    </div>
                <?php endforeach; ?>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:16px; padding-top:16px; border-top:2px solid var(--gray-200);">
                    <div>
                        <p style="font-size:0.8rem; color:var(--gray-500);">Delivery to:</p>
                        <p style="font-size:0.9rem;"><?= sanitize($order['shipping_address']) ?></p>
                    </div>
                    <div style="text-align:right;">
                        <p style="font-size:0.8rem; color:var(--gray-500);">Total</p>
                        <p style="font-size:1.25rem; font-weight:800;"><?= formatPrice($order['total_amount']) ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>