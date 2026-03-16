<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

checkRole(['Admin', 'Manager', 'Technician', 'Cashier']);

$business_id = $_SESSION['business_id'];

// Get Services with customer names and technicians
$stmt = $pdo->prepare("SELECT s.*, c.name as customer_name, u.full_name as tech_name FROM services s LEFT JOIN customers c ON s.customer_id = c.id LEFT JOIN users u ON s.user_id = u.id WHERE s.business_id = ? ORDER BY s.id DESC");
$stmt->execute([$business_id]);
$services = $stmt->fetchAll();

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Service Records</h1>
    <a href="<?php echo BASE_URL; ?>services/add.php" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm"></i> New Service Job</a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Active & Past Service Jobs</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Device/Item</th>
                        <th>Service Title</th>
                        <th>Customer</th>
                        <th>Status</th>
                        <th>Technician</th>
                        <th>Cost</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($services)): ?>
                        <tr><td colspan="9" class="text-center">No service records found.</td></tr>
                    <?php else: ?>
                        <?php foreach($services as $s): ?>
                        <tr>
                            <td>#<?php echo $s['id']; ?></td>
                            <td><strong><?php echo $s['item_or_device']; ?></strong></td>
                            <td><?php echo $s['service_title']; ?></td>
                            <td><?php echo $s['customer_name'] ?: 'N/A'; ?></td>
                            <td>
                                <?php
                                $badge = 'bg-secondary';
                                if($s['status'] == 'In Progress') $badge = 'bg-primary';
                                if($s['status'] == 'Completed') $badge = 'bg-success';
                                if($s['status'] == 'Collected') $badge = 'bg-info';
                                if($s['status'] == 'Diagnosed') $badge = 'bg-warning';
                                ?>
                                <span class="badge <?php echo $badge; ?>"><?php echo $s['status']; ?></span>
                            </td>
                            <td><?php echo $s['tech_name'] ?: '<span class="text-muted">Unassigned</span>'; ?></td>
                            <td>₦<?php echo number_format($s['cost'], 2); ?></td>
                            <td><?php echo date('d M Y', strtotime($s['created_at'])); ?></td>
                            <td>
                                <a href="<?php echo BASE_URL; ?>services/edit.php?id=<?php echo $s['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
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
