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

// Get Categories
$stmt = $pdo->prepare("SELECT * FROM categories WHERE business_id = ?");
$stmt->execute([$business_id]);
$categories = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_product'])) {
    $name = clean($_POST['name']);
    $category_id = $_POST['category_id'] ?: null;
    $sku = clean($_POST['sku']);
    $barcode = clean($_POST['barcode']);
    $cost_price = (float)$_POST['cost_price'];
    $selling_price = (float)$_POST['selling_price'];
    $quantity = (int)$_POST['quantity'];
    $low_stock = (int)$_POST['low_stock'];
    $supplier = clean($_POST['supplier']);
    $description = clean($_POST['description']);

    try {
        $stmt = $pdo->prepare("UPDATE products SET category_id = ?, name = ?, sku = ?, barcode = ?, cost_price = ?, selling_price = ?, quantity = ?, low_stock_threshold = ?, supplier = ?, description = ? WHERE id = ? AND business_id = ?");
        $stmt->execute([$category_id, $name, $sku, $barcode, $cost_price, $selling_price, $quantity, $low_stock, $supplier, $description, $id, $business_id]);
        
        logActivity($pdo, $business_id, $_SESSION['user_id'], "Updated product: $name", "Inventory");
        redirect(BASE_URL . 'products/index.php', "Product updated successfully.");
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit Product: <?php echo $product['name']; ?></h1>
    <a href="<?php echo BASE_URL; ?>products/index.php" class="btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm"></i> Back to List
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Product Details</h6>
            </div>
            <div class="card-body">
                <form action="" method="POST">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="<?php echo $product['name']; ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select">
                                <option value="">Select Category</option>
                                <?php foreach($categories as $c): ?>
                                    <option value="<?php echo $c['id']; ?>" <?php echo $product['category_id'] == $c['id'] ? 'selected' : ''; ?>><?php echo $c['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">SKU</label>
                            <input type="text" name="sku" class="form-control" value="<?php echo $product['sku']; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Barcode</label>
                            <input type="text" name="barcode" class="form-control" value="<?php echo $product['barcode']; ?>">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Cost Price <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₦</span>
                                <input type="number" step="0.01" name="cost_price" class="form-control" value="<?php echo $product['cost_price']; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Selling Price <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₦</span>
                                <input type="number" step="0.01" name="selling_price" class="form-control" value="<?php echo $product['selling_price']; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Current Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" class="form-control" value="<?php echo $product['quantity']; ?>" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Low Stock Alert at</label>
                            <input type="number" name="low_stock" class="form-control" value="<?php echo $product['low_stock_threshold']; ?>">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Supplier</label>
                            <input type="text" name="supplier" class="form-control" value="<?php echo $product['supplier']; ?>">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"><?php echo $product['description']; ?></textarea>
                        </div>
                    </div>
                    
                    <div class="text-end mt-3">
                        <button type="submit" name="update_product" class="btn btn-primary px-5">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
