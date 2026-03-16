<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    header('Location: ' . BASE_URL . 'auth/login.php');
    exit();
}

checkSubscription();

$business_id = $_SESSION['business_id'];

// Get Stats
// 1. Total Products
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM products WHERE business_id = ?");
$stmt->execute([$business_id]);
$total_products = $stmt->fetch()['total'];

// 2. Inventory Value
$stmt = $pdo->prepare("SELECT SUM(quantity * selling_price) as total_val FROM products WHERE business_id = ?");
$stmt->execute([$business_id]);
$inventory_value = $stmt->fetch()['total_val'] ?? 0;

// 3. Sales Today
$stmt = $pdo->prepare("SELECT SUM(grand_total) as total_sales FROM sales WHERE business_id = ? AND DATE(created_at) = CURDATE() AND status = 'Completed'");
$stmt->execute([$business_id]);
$sales_today = $stmt->fetch()['total_sales'] ?? 0;

// 4. Services Today
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM services WHERE business_id = ? AND DATE(created_at) = CURDATE()");
$stmt->execute([$business_id]);
$services_today = $stmt->fetch()['total'];

// 5. Expenses Today
$stmt = $pdo->prepare("SELECT SUM(amount) as total FROM expenses WHERE business_id = ? AND expense_date = CURDATE()");
$stmt->execute([$business_id]);
$expenses_today = $stmt->fetch()['total'] ?? 0;

// 5. Low Stock
$stmt = $pdo->prepare("SELECT * FROM products WHERE business_id = ? AND quantity <= low_stock_threshold LIMIT 5");
$stmt->execute([$business_id]);
$low_stock_products = $stmt->fetchAll();

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    <a href="<?php echo BASE_URL; ?>pos/index.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> New Sale
    </a>
</div>

<!-- Content Row -->
<div class="row">

    <!-- Total Products Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary h-100 py-2" style="border-left: 4px solid var(--primary-color);">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Products</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($total_products); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-boxes fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Value Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success h-100 py-2" style="border-left: 4px solid var(--success-color);">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Inventory Value</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">₦<?php echo number_format($inventory_value, 2); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Today Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info h-100 py-2" style="border-left: 4px solid var(--info-color);">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Sales Today</div>
                        <div class="row no-gutters align-items-center">
                            <div class="col-auto">
                                <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">₦<?php echo number_format($sales_today, 2); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Services Today Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning h-100 py-2" style="border-left: 4px solid var(--warning-color);">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Services Today</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($services_today); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tools fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Expenses Today Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger h-100 py-2" style="border-left: 4px solid var(--danger-color);">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Expenses Today</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">₦<?php echo number_format($expenses_today, 2); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estimated Profit Today -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-dark h-100 py-2" style="border-left: 4px solid #5a5c69;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Estimated Net profit (Today)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">₦<?php echo number_format($sales_today - $expenses_today, 2); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-pie fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Sales Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Revenue Overview (Last 7 Days)</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="salesChart" style="height: 320px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Alert -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-danger">Low Stock Alerts</h6>
            </div>
            <div class="card-body">
                <?php if(empty($low_stock_products)): ?>
                    <p class="text-muted text-center py-4">All stock levels are healthy.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($low_stock_products as $p): ?>
                                <tr>
                                    <td><?php echo $p['name']; ?></td>
                                    <td><span class="badge bg-danger"><?php echo $p['quantity']; ?> left</span></td>
                                    <td><a href="<?php echo BASE_URL; ?>products/edit.php?id=<?php echo $p['id']; ?>" class="btn btn-xs btn-primary"><i class="fas fa-edit"></i></a></td>
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

<?php
// Prepare chart data
$days = [];
$revenues = [];
for($i=6; $i>=0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $days[] = date('D', strtotime($date));
    
    $stmt = $pdo->prepare("SELECT SUM(grand_total) as total FROM sales WHERE business_id = ? AND DATE(created_at) = ? AND status = 'Completed'");
    $stmt->execute([$business_id, $date]);
    $revenues[] = (float)($stmt->fetch()['total'] ?? 0);
}
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('salesChart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($days); ?>,
            datasets: [{
                label: 'Revenue (₦)',
                data: <?php echo json_encode($revenues); ?>,
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                borderColor: 'rgba(78, 115, 223, 1)',
                pointRadius: 3,
                pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointBorderColor: 'rgba(78, 115, 223, 1)',
                pointHoverRadius: 3,
                pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                pointHitRadius: 10,
                pointBorderWidth: 2,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false,
                        color: 'rgba(0,0,0,0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
