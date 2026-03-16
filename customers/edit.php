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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_customer'])) {
    $name = clean($_POST['name']);
    $phone = clean($_POST['phone']);
    $email = clean($_POST['email']);
    $address = clean($_POST['address']);
    $notes = clean($_POST['notes']);

    try {
        $stmt = $pdo->prepare("UPDATE customers SET name = ?, phone = ?, email = ?, address = ?, notes = ? WHERE id = ? AND business_id = ?");
        $stmt->execute([$name, $phone, $email, $address, $notes, $id, $business_id]);
        
        logActivity($pdo, $business_id, $_SESSION['user_id'], "Updated customer: $name", "Customers");
        redirect(BASE_URL . 'customers/view.php?id=' . $id, "Customer updated successfully.");
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit Customer: <?php echo $customer['name']; ?></h1>
    <a href="<?php echo BASE_URL; ?>customers/view.php?id=<?php echo $id; ?>" class="btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm"></i> Back to Profile
    </a>
</div>

<?php if(isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Customer Profile</h6>
            </div>
            <div class="card-body">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="<?php echo $customer['name']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo $customer['phone']; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" value="<?php echo $customer['email']; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Home/Office Address</label>
                        <textarea name="address" class="form-control" rows="2"><?php echo $customer['address']; ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="2"><?php echo $customer['notes']; ?></textarea>
                    </div>
                    
                    <div class="text-end mt-4">
                        <button type="submit" name="update_customer" class="btn btn-primary px-5">Update Customer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
