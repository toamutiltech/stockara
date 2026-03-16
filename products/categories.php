<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

checkRole(['Admin', 'Manager']);

$business_id = $_SESSION['business_id'];

// Add Category
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $name = clean($_POST['name']);
    if (!empty($name)) {
        $stmt = $pdo->prepare("INSERT INTO categories (business_id, name) VALUES (?, ?)");
        $stmt->execute([$business_id, $name]);
        redirect(BASE_URL . 'products/categories.php', "Category added.");
    }
}

// Delete Category
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ? AND business_id = ?");
    $stmt->execute([$id, $business_id]);
    redirect(BASE_URL . 'products/categories.php', "Category deleted.");
}

// Get Categories
$stmt = $pdo->prepare("SELECT * FROM categories WHERE business_id = ? ORDER BY name ASC");
$stmt->execute([$business_id]);
$categories = $stmt->fetchAll();

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Categories</h1>
    <a href="<?php echo BASE_URL; ?>products/index.php" class="btn btn-sm btn-secondary shadow-sm"><i class="fas fa-arrow-left"></i> Back to Products</a>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Add Category</h6>
            </div>
            <div class="card-body">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Electronics" required>
                    </div>
                    <button type="submit" name="add_category" class="btn btn-primary w-100">Add Category</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Category List</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($categories as $c): ?>
                            <tr>
                                <td><?php echo $c['name']; ?></td>
                                <td><?php echo date('Y-m-d', strtotime($c['created_at'])); ?></td>
                                <td>
                                    <a href="?delete=<?php echo $c['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete category?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
