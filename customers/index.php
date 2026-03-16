<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

checkRole(['Admin', 'Manager', 'Cashier', 'Technician']);

$business_id = $_SESSION['business_id'];

// Get Customers
$stmt = $pdo->prepare("SELECT * FROM customers WHERE business_id = ? ORDER BY id DESC");
$stmt->execute([$business_id]);
$customers = $stmt->fetchAll();

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Customer Management</h1>
    <a href="<?php echo BASE_URL; ?>customers/add.php" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm"></i> Add New Customer</a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Customer Database</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Total Visits</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($customers)): ?>
                        <tr><td colspan="6" class="text-center text-muted">No customers recorded yet.</td></tr>
                    <?php else: ?>
                        <?php foreach($customers as $c): ?>
                        <tr>
                            <td><strong><?php echo $c['name']; ?></strong></td>
                            <td><?php echo $c['phone']; ?></td>
                            <td><?php echo $c['email']; ?></td>
                            <td><?php echo $c['address']; ?></td>
                            <td>
                                <?php
                                // Basic count from sales + services
                                $stmt = $pdo->prepare("SELECT (SELECT COUNT(*) FROM sales WHERE customer_id = ?) + (SELECT COUNT(*) FROM services WHERE customer_id = ?) as total");
                                $stmt->execute([$c['id'], $c['id']]);
                                echo $stmt->fetch()['total'];
                                ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?php echo BASE_URL; ?>customers/view.php?id=<?php echo $c['id']; ?>" class="btn btn-sm btn-outline-info" title="View History"><i class="fas fa-history"></i></a>
                                    <a href="<?php echo BASE_URL; ?>customers/edit.php?id=<?php echo $c['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
