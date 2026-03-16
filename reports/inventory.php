<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

checkRole(['Admin', 'Manager']);

$business_id = $_SESSION['business_id'];

// 1. Overall Statistics
$stmt = $pdo->prepare("SELECT COUNT(*) as total_items, SUM(quantity) as total_qty, SUM(quantity * cost_price) as total_cost, SUM(quantity * selling_price) as total_value FROM products WHERE business_id = ?");
$stmt->execute([$business_id]);
$stats = $stmt->fetch();

// 2. Category Breakdown
$stmt = $pdo->prepare("SELECT c.name as category, COUNT(p.id) as item_count, SUM(p.quantity) as total_qty FROM categories c LEFT JOIN products p ON c.id = p.category_id WHERE c.business_id = ? GROUP BY c.id");
$stmt->execute([$business_id]);
$categories = $stmt->fetchAll();

// 3. Low Stock Items (Comprehensive List)
$stmt = $pdo->prepare("SELECT name, quantity, low_stock_threshold, sku FROM products WHERE business_id = ? AND quantity <= low_stock_threshold ORDER BY quantity ASC");
$stmt->execute([$business_id]);
$low_stock = $stmt->fetchAll();

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Inventory Analysis Report</h1>
    <a href="javascript:window.print()" class="btn btn-sm btn-outline-info shadow-sm"><i class="fas fa-print"></i> Print Report</a>
</div>

<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total SKU Items</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['total_items']); ?></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Unit Quantity</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['total_qty']); ?></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Stock Cost Value</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">₦<?php echo number_format($stats['total_cost'], 2); ?></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Estimated Selling Value</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">₦<?php echo number_format($stats['total_value'], 2); ?></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Category Distribution</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Category</th>
                                <th class="text-center">Unique Items</th>
                                <th class="text-center">Total Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($categories as $cat): ?>
                            <tr>
                                <td><?php echo $cat['category'] ?: 'Uncategorized'; ?></td>
                                <td class="text-center"><?php echo $cat['item_count']; ?></td>
                                <td class="text-center fw-bold"><?php echo number_format($cat['total_qty']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-danger">Low Stock Critical List</h6>
            </div>
            <div class="card-body">
                <?php if(empty($low_stock)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                        <p class="text-muted">No items are below threshold.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th class="text-end">Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($low_stock as $ls): ?>
                                <tr>
                                    <td><?php echo $ls['name']; ?></td>
                                    <td><small class="text-muted"><?php echo $ls['sku']; ?></small></td>
                                    <td class="text-end fw-bold text-danger"><?php echo $ls['quantity']; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
