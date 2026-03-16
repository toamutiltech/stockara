<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

checkRole(['Admin', 'Manager', 'Cashier']);

$business_id = $_SESSION['business_id'];

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$start = ($page - 1) * $limit;

// Search
$search = isset($_GET['search']) ? clean($_GET['search']) : '';

$where = "WHERE p.business_id = ?";
$params = [$business_id];

if ($search) {
    $where .= " AND (p.name LIKE ? OR p.sku LIKE ? OR p.barcode LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

/*
|--------------------------------------------------
| GET PRODUCTS
|--------------------------------------------------
*/
$stmt = $pdo->prepare("
    SELECT p.*, c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    $where
    ORDER BY p.id DESC
    LIMIT $start, $limit
");
$stmt->execute($params);
$products = $stmt->fetchAll();

/*
|--------------------------------------------------
| GET TOTAL RECORDS (FOR PAGINATION)
|--------------------------------------------------
*/
$stmt = $pdo->prepare("
    SELECT COUNT(*) as total
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    $where
");
$stmt->execute($params);
$total_records = $stmt->fetch()['total'];
$total_pages = ceil($total_records / $limit);

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Inventory Management</h1>
    <div>
        <a href="<?php echo BASE_URL; ?>products/categories.php" class="btn btn-sm btn-outline-primary shadow-sm">
            <i class="fas fa-tags"></i> Categories
        </a>
        <a href="<?php echo BASE_URL; ?>products/add.php" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus"></i> Add Product
        </a>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row align-items-center">
            <div class="col">
                <h6 class="m-0 font-weight-bold text-primary">Product List</h6>
            </div>
            <div class="col-auto">
                <form method="GET" class="d-flex">
                    <input type="text"
                           name="search"
                           class="form-control form-control-sm me-2"
                           placeholder="Search SKU, Name, Barcode..."
                           value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>SKU</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Stock</th>
                        <th>Cost</th>
                        <th>Selling</th>
                        <th>Barcode</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                <?php if(empty($products)): ?>
                    <tr>
                        <td colspan="8" class="text-center">No products found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach($products as $p): ?>
                        <tr>
                            <td><code><?php echo htmlspecialchars($p['sku']); ?></code></td>

                            <td><?php echo htmlspecialchars($p['name']); ?></td>

                            <td>
                                <?php echo $p['category_name'] 
                                    ? htmlspecialchars($p['category_name']) 
                                    : '<span class="text-muted">N/A</span>'; ?>
                            </td>

                            <td>
                                <?php if($p['quantity'] <= $p['low_stock_threshold']): ?>
                                    <span class="badge bg-danger">
                                        <?php echo $p['quantity']; ?> (Low)
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-success">
                                        <?php echo $p['quantity']; ?>
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td>₦<?php echo number_format($p['cost_price'], 2); ?></td>

                            <td class="fw-bold">
                                ₦<?php echo number_format($p['selling_price'], 2); ?>
                            </td>

                            <td>
                                <a href="<?php echo BASE_URL; ?>products/print_barcode.php?id=<?php echo $p['id']; ?>"
                                   class="btn btn-sm btn-outline-dark">
                                    <i class="fas fa-barcode"></i>
                                    <?php echo htmlspecialchars($p['barcode']); ?>
                                </a>
                            </td>

                            <td>
                                <div class="btn-group">
                                    <a href="<?php echo BASE_URL; ?>products/adjust.php?id=<?php echo $p['id']; ?>"
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-sync"></i>
                                    </a>

                                    <a href="<?php echo BASE_URL; ?>products/edit.php?id=<?php echo $p['id']; ?>"
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <a href="<?php echo BASE_URL; ?>products/delete.php?id=<?php echo $p['id']; ?>"
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Delete this product?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if($total_pages > 1): ?>
            <nav>
                <ul class="pagination pagination-sm justify-content-end mb-0">
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                            <a class="page-link"
                               href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>">
                               <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>

    </div>
</div>

<?php include '../includes/footer.php'; ?>