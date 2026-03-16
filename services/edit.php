<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

checkRole(['Admin', 'Manager', 'Technician', 'Cashier']);

$business_id = $_SESSION['business_id'];
$id = (int)$_GET['id'];

// Get Job
$stmt = $pdo->prepare("SELECT * FROM services WHERE id = ? AND business_id = ?");
$stmt->execute([$id, $business_id]);
$service = $stmt->fetch();

if (!$service) {
    redirect(BASE_URL . 'services/index.php', "Service record not found.", "danger");
}

// Get Customers
$stmt = $pdo->prepare("SELECT id, name, phone FROM customers WHERE business_id = ? ORDER BY name ASC");
$stmt->execute([$business_id]);
$customers = $stmt->fetchAll();

// Get Technicians
$stmt = $pdo->prepare("SELECT id, full_name FROM users WHERE business_id = ? AND (role = 'Technician' OR role = 'Admin') AND status = 1");
$stmt->execute([$business_id]);
$technicians = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_service'])) {
    $title = clean($_POST['service_title']);
    $description = clean($_POST['description']);
    $item = clean($_POST['item_or_device']);
    $customer_id = $_POST['customer_id'] ?: null;
    $tech_id = $_POST['user_id'] ?: null;
    $cost = (float)$_POST['cost'];
    $status = $_POST['status'];

    try {
        $stmt = $pdo->prepare("UPDATE services SET customer_id = ?, user_id = ?, service_title = ?, description = ?, item_or_device = ?, cost = ?, status = ? WHERE id = ? AND business_id = ?");
        $stmt->execute([$customer_id, $tech_id, $title, $description, $item, $cost, $status, $id, $business_id]);
        
        logActivity($pdo, $business_id, $_SESSION['user_id'], "Updated service job #$id: $title", "Services");
        redirect(BASE_URL . 'services/index.php', "Service record updated successfully.");
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit Service Job #<?php echo $id; ?></h1>
    <a href="<?php echo BASE_URL; ?>services/index.php" class="btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm"></i> Back to List
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Job Update</h6>
            </div>
            <div class="card-body">
                <form action="" method="POST">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Service Title <span class="text-danger">*</span></label>
                            <input type="text" name="service_title" class="form-control" value="<?php echo $service['service_title']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Item / Device <span class="text-danger">*</span></label>
                            <input type="text" name="item_or_device" class="form-control" value="<?php echo $service['item_or_device']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Customer</label>
                            <select name="customer_id" class="form-select">
                                <option value="">Select Customer</option>
                                <?php foreach($customers as $c): ?>
                                    <option value="<?php echo $c['id']; ?>" <?php echo $service['customer_id'] == $c['id'] ? 'selected' : ''; ?>><?php echo $c['name']; ?> (<?php echo $c['phone']; ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Assigned Technician</label>
                            <select name="user_id" class="form-select">
                                <option value="">Unassigned</option>
                                <?php foreach($technicians as $t): ?>
                                    <option value="<?php echo $t['id']; ?>" <?php echo $service['user_id'] == $t['id'] ? 'selected' : ''; ?>><?php echo $t['full_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cost</label>
                            <div class="input-group">
                                <span class="input-group-text">₦</span>
                                <input type="number" step="0.01" name="cost" class="form-control" value="<?php echo $service['cost']; ?>">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="Pending" <?php echo $service['status'] == 'Pending' ? 'selected' : ''; ?>>Pending / Received</option>
                                <option value="Diagnosed" <?php echo $service['status'] == 'Diagnosed' ? 'selected' : ''; ?>>Diagnosed</option>
                                <option value="In Progress" <?php echo $service['status'] == 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                                <option value="Completed" <?php echo $service['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                <option value="Collected" <?php echo $service['status'] == 'Collected' ? 'selected' : ''; ?>>Collected</option>
                            </select>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Issue Description / Notes</label>
                            <textarea name="description" class="form-control" rows="4"><?php echo $service['description']; ?></textarea>
                        </div>
                    </div>
                    
                    <div class="text-end mt-3">
                        <button type="submit" name="update_service" class="btn btn-primary px-5 py-2 fw-bold">Update Job Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
