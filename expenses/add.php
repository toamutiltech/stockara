<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

checkRole(['Admin', 'Manager']);

$business_id = $_SESSION['business_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_expense'])) {
    $amount = (float)$_POST['amount'];
    $category = $_POST['category'];
    $description = clean($_POST['description']);
    $expense_date = $_POST['expense_date'];
    $user_id = $_SESSION['user_id'];

    if ($amount <= 0) {
        $error = "Amount must be greater than zero.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO expenses (business_id, user_id, category, amount, description, expense_date) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$business_id, $user_id, $category, $amount, $description, $expense_date]);
            
            logActivity($pdo, $business_id, $user_id, "Recorded expense: ₦$amount for $category", "Finance");
            redirect(BASE_URL . 'expenses/index.php', "Expense recorded successfully.");
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Record New Expense</h1>
    <a href="<?php echo BASE_URL; ?>expenses/index.php" class="btn btn-sm btn-secondary shadow-sm"><i class="fas fa-arrow-left fa-sm"></i> Back</a>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Expense Details</h6>
            </div>
            <div class="card-body">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Expense Date <span class="text-danger">*</span></label>
                        <input type="date" name="expense_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select name="category" class="form-select" required>
                            <option value="Rent">Rent</option>
                            <option value="Salary">Salary</option>
                            <option value="Utility">Utility</option>
                            <option value="Stock Purchase">Stock Purchase</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Repair/Maintenance">Repair/Maintenance</option>
                            <option value="Others" selected>Others</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₦</span>
                            <input type="number" step="0.01" name="amount" class="form-control" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description / Remarks</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Additional details about the expense..."></textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" name="add_expense" class="btn btn-primary btn-lg py-2 shadow">Save Expense</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
