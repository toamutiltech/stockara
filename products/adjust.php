<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

checkRole(['Admin', 'Manager']);

$business_id = $_SESSION['business_id'];
$id = (int)$_GET['id'];

// Get Product
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND business_id = ?");
$stmt->execute([$id, $business_id]);
$product = $stmt->fetch();

if (!$product) {
    redirect(BASE_URL . 'products/index.php', "Product not found.", "danger");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['adjust_stock'])) {
    $type = $_POST['adjustment_type'];
    $qty = (int)$_POST['quantity'];
    $reason = clean($_POST['reason']);
    $user_id = $_SESSION['user_id'];

    if ($qty <= 0) {
        $error = "Quantity must be greater than zero.";
    } else {
        try {
            $pdo->beginTransaction();

            // 1. Log adjustment
            $stmt = $pdo->prepare("INSERT INTO stock_adjustments (business_id, product_id, user_id, adjustment_type, quantity, reason) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$business_id, $id, $user_id, $type, $qty, $reason]);

            // 2. Update product quantity
            if ($type == 'Addition') {
                $stmt = $pdo->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?");
            } else {
                $stmt = $pdo->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
            }
            $stmt->execute([$qty, $id]);

            $pdo->commit();
            logActivity($pdo, $business_id, $user_id, "Stock adjustment ($type $qty) for product: " . $product['name'], "Inventory");
            redirect(BASE_URL . 'products/index.php', "Stock adjusted successfully.");
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error: " . $e->getMessage();
        }
    }
}

// Get recent adjustments for this product
$stmt = $pdo->prepare("SELECT sa.*, u.full_name FROM stock_adjustments sa LEFT JOIN users u ON sa.user_id = u.id WHERE sa.product_id = ? ORDER BY sa.created_at DESC LIMIT 5");
$stmt->execute([$id]);
$adjustments = $stmt->fetchAll();

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Stock Adjustment: <?php echo $product['name']; ?></h1>
    <a href="<?php echo BASE_URL; ?>products/index.php" class="btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm"></i> Back to List
    </a>
</div>

<div class="row">
    <div class="col-md-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Manual Adjustment</h6>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <small class="text-muted text-uppercase fw-bold">Current Stock Level</small>
                    <h2 class="fw-bold mb-0"><?php echo $product['quantity']; ?> <span class="fs-6 text-muted fw-normal">units</span></h2>
                </div>

                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Adjustment Type <span class="text-danger">*</span></label>
                        <select name="adjustment_type" class="form-select" required>
                            <option value="Addition">Addition (+)</option>
                            <option value="Subtraction">Subtraction (-)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" class="form-control" min="1" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reason / Remark</label>
                        <input type="text" name="reason" class="form-control" placeholder="e.g. Damage, Restock, Return, Gift">
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" name="adjust_stock" class="btn btn-primary py-2 shadow" onclick="return confirm('Ensure the quantities are correct. Continue?')"> Apply Adjustment </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recent Adjustments</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Qty</th>
                                <th>User</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($adjustments)): ?>
                                <tr><td colspan="5" class="text-center py-4 text-muted">No manual adjustments recorded.</td></tr>
                            <?php else: ?>
                                <?php foreach($adjustments as $adj): ?>
                                <tr>
                                    <td><?php echo date('d M, H:i', strtotime($adj['created_at'])); ?></td>
                                    <td>
                                        <span class="badge <?php echo $adj['adjustment_type'] == 'Addition' ? 'bg-success' : 'bg-danger'; ?>">
                                            <?php echo $adj['adjustment_type']; ?>
                                        </span>
                                    </td>
                                    <td class="fw-bold"><?php echo $adj['quantity']; ?></td>
                                    <td><?php echo $adj['full_name']; ?></td>
                                    <td><small class="text-muted"><?php echo $adj['reason'] ?: '-'; ?></small></td>
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

<?php include '../includes/footer.php'; ?>
