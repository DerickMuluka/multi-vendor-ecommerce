<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Products';

$categoryId = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$sort = $_GET['sort'] ?? 'newest';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 12;
$offset = ($page - 1) * $perPage;

$where = ["p.status = 'active'", "v.status = 'active'"];
$params = [];

if ($categoryId > 0) {
    $where[] = "p.category_id = ?";
    $params[] = $categoryId;
}

if ($search) {
    $where[] = "(p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$whereClause = implode(' AND ', $where);

$orderBy = match($sort) {
    'price_asc' => 'p.price ASC',
    'price_desc' => 'p.price DESC',
    'name' => 'p.name ASC',
    default => 'p.created_at DESC'
};

// Count
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM products p JOIN vendors v ON p.vendor_id = v.id WHERE $whereClause");
$countStmt->execute($params);
$total = $countStmt->fetchColumn();
$totalPages = ceil($total / $perPage);

// Fetch
$stmt = $pdo->prepare("
    SELECT p.*, v.store_name, c.name as category_name 
    FROM products p 
    JOIN vendors v ON p.vendor_id = v.id 
    JOIN categories c ON p.category_id = c.id 
    WHERE $whereClause 
    ORDER BY $orderBy 
    LIMIT $perPage OFFSET $offset
");
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories WHERE status = 'active' ORDER BY name")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding-top: 40px;">
    <div class="page-header">
        <div>
            <h1 class="page-title">All Products</h1>
            <p style="color: var(--gray-500); font-size: 0.9rem; margin-top: 4px;"><?= $total ?> products available</p>
        </div>
    </div>
    
    <form method="GET" class="filters-bar">
        <input type="text" name="search" class="form-control" placeholder="Search products..." value="<?= $search ?>">
        <select name="category" class="form-control">
            <option value="0">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $categoryId == $cat['id'] ? 'selected' : '' ?>><?= sanitize($cat['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="sort" class="form-control">
            <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest</option>
            <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
            <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
            <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Name A-Z</option>
        </select>
        <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
    </form>
    
    <?php if (empty($products)): ?>
        <div class="empty-state">
            <i class="fas fa-box-open"></i>
            <h3>No products found</h3>
            <p>Try adjusting your search or filters</p>
            <a href="<?= SITE_URL ?>/products.php" class="btn btn-primary">Clear Filters</a>
        </div>
    <?php else: ?>
        <div class="products-grid">
            <?php foreach ($products as $product): 
                $finalPrice = $product['discount_price'] ?: $product['price'];
                $hasDiscount = $product['discount_price'] && $product['discount_price'] < $product['price'];
            ?>
            <div class="product-card">
                <div class="product-image">
                    <?php if ($hasDiscount): ?>
                        <span class="product-badge badge-sale">Sale</span>
                    <?php elseif ($product['stock'] <= 0): ?>
                        <span class="product-badge badge-out">Out of Stock</span>
                    <?php endif; ?>
                    <a href="<?= SITE_URL ?>/product.php?id=<?= $product['id'] ?>">
                        <img src="<?= $product['image'] ? UPLOAD_URL . $product['image'] : 'https://via.placeholder.com/400x400/f1f5f9/94a3b8?text=' . urlencode($product['name']) ?>" alt="<?= sanitize($product['name']) ?>">
                    </a>
                </div>
                <div class="product-info">
                    <span class="product-vendor"><?= sanitize($product['store_name']) ?></span>
                    <h3 class="product-name">
                        <a href="<?= SITE_URL ?>/product.php?id=<?= $product['id'] ?>"><?= sanitize($product['name']) ?></a>
                    </h3>
                    <div class="product-price">
                        <span class="price-current"><?= formatPrice($finalPrice) ?></span>
                        <?php if ($hasDiscount): ?>
                            <span class="price-old"><?= formatPrice($product['price']) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="product-actions">
                        <a href="<?= SITE_URL ?>/product.php?id=<?= $product['id'] ?>" class="btn btn-primary">View</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>"><i class="fas fa-chevron-left"></i></a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == $page): ?>
                    <span class="active"><?= $i ?></span>
                <?php elseif ($i <= 3 || $i > $totalPages - 3 || abs($i - $page) <= 1): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                <?php elseif ($i == 4 || $i == $totalPages - 3): ?>
                    <span>...</span>
                <?php endif; ?>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>"><i class="fas fa-chevron-right"></i></a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>