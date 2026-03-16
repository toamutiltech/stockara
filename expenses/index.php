<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

checkRole(['Admin', 'Manager']);

$business_id = $_SESSION['business_id'];

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Filter
$category = $_GET['category'] ?? '';
$where = "WHERE e.business_id = ?";
$params = [$business_id];

if ($category) {
    $where .= " AND e.category = ?";
    $params[] = $category;
}

// Get Expenses
$stmt = $pdo->prepare("SELECT e.*, u.full_name as user_name FROM expenses e LEFT JOIN users u ON e.user_id = u.id $where ORDER BY e.expense_date DESC LIMIT $start, $limit");
$stmt->execute($params);
$expenses = $stmt->fetchAll();

// Get Total
$stmt = $pdo->prepare("SELECT COUNT(*) FROM expenses e $where");
$stmt->execute($params);
$total_records = $stmt->fetchColumn();
$total_pages = ceil($total_records / $limit);

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Expense Management</h1>
    <a href="<?php echo BASE_URL; ?>expenses/add.php" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm"></i> Record Expense</a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row align-items-center">
            <div class="col">
                <h6 class="m-0 font-weight-bold text-primary">Expense List</h6>
            </div>
            <div class="col-auto">
                <form action="" method="GET" class="d-flex">
                    <select name="category" class="form-select form-select-sm me-2" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        <option value="Rent" <?php echo $category == 'Rent' ? 'selected' : ''; ?>>Rent</option>
                        <option value="Salary" <?php echo $category == 'Salary' ? 'selected' : ''; ?>>Salary</option>
                        <option value="Utility" <?php echo $category == 'Utility' ? 'selected' : ''; ?>>Utility</option>
                        <option value="Stock Purchase" <?php echo $category == 'Stock Purchase' ? 'selected' : ''; ?>>Stock Purchase</option>
                        <option value="Marketing" <?php echo $category == 'Marketing' ? 'selected' : ''; ?>>Marketing</option>
                        <option value="Others" <?php echo $category == 'Others' ? 'selected' : ''; ?>>Others</option>
                    </select>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Description</th>
                        <th>Recorded By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($expenses)): ?>
                        <tr><td colspan="6" class="text-center">No expenses recorded.</td></tr>
                    <?php else: ?>
                        <?php foreach($expenses as $e): ?>
                        <tr>
                            <td><?php echo date('d M Y', strtotime($e['expense_date'])); ?></td>
                            <td><span class="badge bg-secondary text-white"><?php echo $e['category']; ?></span></td>
                            <td class="fw-bold text-danger">₦<?php echo number_format($e['amount'], 2); ?></td>
                            <td><?php echo $e['description']; ?></td>
                            <td><small><?php echo $e['user_name']; ?></small></td>
                            <td>
                                <a href="<?php echo BASE_URL; ?>expenses/delete.php?id=<?php echo $e['id']; ?>" class="btn btn-sm text-danger" onclick="return confirm('Delete this record?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if($total_pages > 1): ?>
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination pagination-sm justify-content-end mb-0">
                <?php for($i=1; $i<=$total_pages; $i++): ?>
                <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>&category=<?php echo $category; ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
