<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

checkRole(['Admin', 'Manager', 'Cashier', 'Technician']);

$business_id = $_SESSION['business_id'];
$id = (int)$_GET['id'];

// Get Customer
$stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ? AND business_id = ?");
$stmt->execute([$id, $business_id]);
$customer = $stmt->fetch();

if (!$customer) {
    redirect(BASE_URL . 'customers/index.php', "Customer not found.", "danger");
}

// Get Sales History
$stmt = $pdo->prepare("SELECT * FROM sales WHERE customer_id = ? AND business_id = ? ORDER BY created_at DESC");
$stmt->execute([$id, $business_id]);
$sales = $stmt->fetchAll();

// Get Service History
$stmt = $pdo->prepare("SELECT * FROM services WHERE customer_id = ? AND business_id = ? ORDER BY created_at DESC");
$stmt->execute([$id, $business_id]);
$services = $stmt->fetchAll();

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Customer Profile: <?php echo $customer['name']; ?></h1>
    <a href="<?php echo BASE_URL; ?>customers/index.php" class="btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm"></i> Back to Customers
    </a>
</div>

<div class="row">
    <!-- Profile Info -->
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Contact Information</h6>
            </div>
            <div class="card-body">
                <p><strong>Phone:</strong> <?php echo $customer['phone'] ?: 'N/A'; ?></p>
                <p><strong>Email:</strong> <?php echo $customer['email'] ?: 'N/A'; ?></p>
                <p><strong>Address:</strong><br><?php echo nl2br($customer['address']) ?: 'N/A'; ?></p>
                <hr>
                <p><strong>Notes:</strong><br><span class="text-muted"><?php echo nl2br($customer['notes']) ?: 'None'; ?></span></p>
                <a href="<?php echo BASE_URL; ?>customers/edit.php?id=<?php echo $id; ?>" class="btn btn-sm btn-outline-primary w-100 mt-2">Edit Profile</a>
            </div>
        </div>
    </div>

    <!-- Activity Tabs -->
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <ul class="nav nav-tabs card-header-tabs" id="customerTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales" type="button">Sales History</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="services-tab" data-bs-toggle="tab" data-bs-target="#services" type="button">Service Jobs</button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="customerTabContent">
                    <!-- Sales Tab -->
                    <div class="tab-pane fade show active" id="sales" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Receipt</th>
                                        <th>Method</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($sales)): ?>
                                        <tr><td colspan="5" class="text-center py-4 text-muted">No sales recorded for this customer.</td></tr>
                                    <?php else: ?>
                                        <?php foreach($sales as $s): ?>
                                        <tr>
                                            <td><?php echo date('d M Y', strtotime($s['created_at'])); ?></td>
                                            <td>#<?php echo $s['id']; ?></td>
                                            <td><?php echo $s['payment_method']; ?></td>
                                            <td class="fw-bold">₦<?php echo number_format($s['grand_total'], 2); ?></td>
                                            <td><a href="<?php echo BASE_URL; ?>pos/receipt.php?id=<?php echo $s['id']; ?>" class="btn btn-xs btn-outline-primary"><i class="fas fa-eye"></i></a></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Services Tab -->
                    <div class="tab-pane fade" id="services" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Item/Device</th>
                                        <th>Status</th>
                                        <th>Cost</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($services)): ?>
                                        <tr><td colspan="5" class="text-center py-4 text-muted">No services recorded for this customer.</td></tr>
                                    <?php else: ?>
                                        <?php foreach($services as $sv): ?>
                                        <tr>
                                            <td><?php echo date('d M Y', strtotime($sv['created_at'])); ?></td>
                                            <td><?php echo $sv['item_or_device']; ?></td>
                                            <td><span class="badge bg-secondary"><?php echo $sv['status']; ?></span></td>
                                            <td>₦<?php echo number_format($sv['cost'], 2); ?></td>
                                            <td><a href="<?php echo BASE_URL; ?>services/edit.php?id=<?php echo $sv['id']; ?>" class="btn btn-xs btn-outline-primary"><i class="fas fa-edit"></i></a></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
