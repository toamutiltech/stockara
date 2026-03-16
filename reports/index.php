<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

checkRole(['Admin', 'Manager']);

$business_id = $_SESSION['business_id'];

// Get Sales Data for reports
$start_date = $_GET['start'] ?? date('Y-m-01');
$end_date = $_GET['end'] ?? date('Y-m-d');

// 1. Sales Summary
$stmt = $pdo->prepare("SELECT SUM(total_amount) as subtotal, SUM(discount_amount) as discount, SUM(grand_total) as grand_total, COUNT(*) as count FROM sales WHERE business_id = ? AND DATE(created_at) BETWEEN ? AND ? AND status = 'Completed'");
$stmt->execute([$business_id, $start_date, $end_date]);
$sales_summary = $stmt->fetch();

// 2. Service Summary
$stmt = $pdo->prepare("SELECT SUM(cost) as total, COUNT(*) as count FROM services WHERE business_id = ? AND DATE(created_at) BETWEEN ? AND ?");
$stmt->execute([$business_id, $start_date, $end_date]);
$service_summary = $stmt->fetch();

// 3. Top Selling Products
$stmt = $pdo->prepare("SELECT p.name, SUM(si.quantity) as total_qty, SUM(si.subtotal) as total_rev FROM sale_items si JOIN products p ON si.product_id = p.id JOIN sales s ON si.sale_id = s.id WHERE s.business_id = ? AND DATE(s.created_at) BETWEEN ? AND ? GROUP BY p.id ORDER BY total_qty DESC LIMIT 10");
$stmt->execute([$business_id, $start_date, $end_date]);
$top_products = $stmt->fetchAll();

// 4. Expense Summary
$stmt = $pdo->prepare("SELECT category, SUM(amount) as total FROM expenses WHERE business_id = ? AND expense_date BETWEEN ? AND ? GROUP BY category");
$stmt->execute([$business_id, $start_date, $end_date]);
$expense_breakdown = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT SUM(amount) as total FROM expenses WHERE business_id = ? AND expense_date BETWEEN ? AND ?");
$stmt->execute([$business_id, $start_date, $end_date]);
$total_expense = $stmt->fetch()['total'] ?? 0;

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Business Reports</h1>
    <div>
        <a href="<?php echo BASE_URL; ?>reports/inventory.php" class="btn btn-sm btn-outline-primary shadow-sm"><i class="fas fa-boxes"></i> Inventory Report</a>
    </div>
    <div class="d-flex">
        <form class="row g-2 align-items-center">
            <div class="col-auto">
                <input type="date" name="start" class="form-control form-control-sm" value="<?php echo $start_date; ?>">
            </div>
            <div class="col-auto">
                <input type="date" name="end" class="form-control form-control-sm" value="<?php echo $end_date; ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                <a href="<?php echo BASE_URL; ?>reports/export_sales.php?start=<?php echo $start_date; ?>&end=<?php echo $end_date; ?>" class="btn btn-sm btn-outline-success"><i class="fas fa-file-csv"></i> Export Sales</a>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <!-- Sales Stats -->
    <div class="col-md-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 bg-white">
                <h6 class="m-0 font-weight-bold text-primary">Sales Overview</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 border-end py-3">
                        <div class="text-xs font-weight-bold text-uppercase text-muted mb-1">Orders</div>
                        <div class="h4 mb-0 font-weight-bold"><?php echo $sales_summary['count']; ?></div>
                    </div>
                    <div class="col-6 py-3">
                        <div class="text-xs font-weight-bold text-uppercase text-muted mb-1">Net Revenue</div>
                        <div class="h4 mb-0 font-weight-bold text-success">₦<?php echo number_format($sales_summary['grand_total'], 2); ?></div>
                    </div>
                    <div class="col-12 mt-3 p-3 bg-light rounded text-start">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Gross Sales:</span>
                            <span>₦<?php echo number_format($sales_summary['subtotal'], 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Discounts:</span>
                            <span class="text-danger">-₦<?php echo number_format($sales_summary['discount'], 2); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Stats -->
    <div class="col-md-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 bg-white">
                <h6 class="m-0 font-weight-bold text-info">Service Record Overview</h6>
            </div>
            <div class="card-body">
                <div class="row text-center border-bottom mb-3">
                    <div class="col-6 border-end py-3">
                        <div class="text-xs font-weight-bold text-uppercase text-muted mb-1">Jobs Done</div>
                        <div class="h4 mb-0 font-weight-bold"><?php echo $service_summary['count']; ?></div>
                    </div>
                    <div class="col-6 py-3">
                        <div class="text-xs font-weight-bold text-uppercase text-muted mb-1">Service Income</div>
                        <div class="h4 mb-0 font-weight-bold text-info">₦<?php echo number_format($service_summary['total'], 2); ?></div>
                    </div>
                </div>
                
                <div class="px-2">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Revenue:</span>
                        <span class="fw-bold">₦<?php echo number_format($sales_summary['grand_total'] + $service_summary['total'], 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Expenses:</span>
                        <span class="fw-bold text-danger">-₦<?php echo number_format($total_expense, 2); ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <h5 class="mb-0">Net Profit:</h5>
                        <h5 class="mb-0 <?php echo ($sales_summary['grand_total'] + $service_summary['total'] - $total_expense) >= 0 ? 'text-success' : 'text-danger'; ?>">
                            ₦<?php echo number_format($sales_summary['grand_total'] + $service_summary['total'] - $total_expense, 2); ?>
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Top Selling Products</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($top_products as $tp): ?>
                            <tr>
                                <td><?php echo $tp['name']; ?></td>
                                <td><?php echo $tp['total_qty']; ?></td>
                                <td>₦<?php echo number_format($tp['total_rev'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-danger">Expense Breakdown</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th class="text-end">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($expense_breakdown as $eb): ?>
                            <tr>
                                <td><?php echo $eb['category']; ?></td>
                                <td class="text-end fw-bold">₦<?php echo number_format($eb['total'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($expense_breakdown)): ?>
                                <tr><td colspan="2" class="text-center text-muted py-3">No expenses for this period.</td></tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th>Grand Total</th>
                                <th class="text-end text-danger">₦<?php echo number_format($total_expense, 2); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
