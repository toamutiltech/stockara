<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// Handle Actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = clean($_GET['id']);
    $action = $_GET['action'];
    
    if ($action == 'toggle') {
        $stmt = $pdo->prepare("UPDATE users SET status = 1 - status WHERE id = ?");
        $stmt->execute([$id]);
        redirect('users.php' . (isset($_GET['business_id']) ? '?business_id=' . $_GET['business_id'] : ''), "User status updated");
    } elseif ($action == 'delete') {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        redirect('users.php' . (isset($_GET['business_id']) ? '?business_id=' . $_GET['business_id'] : ''), "User deleted permanently");
    }
}

// Handle Edit User
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_user_id'])) {
    $id = clean($_POST['edit_user_id']);
    $full_name = clean($_POST['full_name']);
    $email = clean($_POST['email']);
    $role = clean($_POST['role']);
    
    $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, role = ? WHERE id = ?");
    $stmt->execute([$full_name, $email, $role, $id]);
    redirect('users.php' . (isset($_GET['business_id']) ? '?business_id=' . $_GET['business_id'] : ''), "User details updated successfully");
}

// Fetch Businesses for Filter
$businesses_stmt = $pdo->query("SELECT id, name FROM businesses ORDER BY name ASC");
$businesses = $businesses_stmt->fetchAll();

// Build Query
$filter_business_id = $_GET['business_id'] ?? '';

$query = "SELECT users.*, businesses.name as business_name FROM users LEFT JOIN businesses ON users.business_id = businesses.id";
$params = [];

if (!empty($filter_business_id)) {
    $query .= " WHERE users.business_id = ?";
    $params[] = $filter_business_id;
}

$query .= " ORDER BY users.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$users = $stmt->fetchAll();
?>

<div class="row align-items-center mb-5">
    <div class="col-lg-6">
        <h2 class="fw-800 text-slate-800">Platform Users</h2>
        <p class="text-muted">Manage all registered staff and admin accounts across the <span class="fw-bold">Stockara</span> platform.</p>
    </div>
</div>

<!-- Filter Section -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-6 col-lg-4">
                <label class="form-label small fw-bold text-muted">Filter by Business</label>
                <select name="business_id" class="form-select bg-light border-0 py-2 rounded-3">
                    <option value="">All Businesses</option>
                    <?php foreach ($businesses as $b): ?>
                        <option value="<?php echo $b['id']; ?>" <?php echo $filter_business_id == $b['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($b['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 col-lg-2">
                <button type="submit" class="btn btn-primary w-100 shadow-sm"><i class="fas fa-filter me-2"></i> Apply</button>
            </div>
            <?php if (!empty($filter_business_id)): ?>
            <div class="col-md-12 col-lg-2">
                <a href="users.php" class="btn btn-light w-100 border text-muted">Clear Filter</a>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Users List -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
    <div class="table-responsive p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="px-4 border-0 text-muted small fw-bold text-uppercase">User</th>
                    <th class="border-0 text-muted small fw-bold text-uppercase">Role</th>
                    <th class="border-0 text-muted small fw-bold text-uppercase">Business</th>
                    <th class="border-0 text-muted small fw-bold text-uppercase">Status</th>
                    <th class="px-4 border-0 text-muted small fw-bold text-uppercase text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $user): ?>
                <tr>
                    <td class="px-4 border-0 py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <span class="fw-bold"><?php echo strtoupper(substr($user['username'], 0, 1)); ?></span>
                            </div>
                            <div>
                                <p class="mb-0 fw-bold small"><?php echo htmlspecialchars($user['full_name'] ?: $user['username']); ?></p>
                                <p class="mb-0 text-muted text-xs small" style="font-size: 0.75rem;">
                                    <?php echo htmlspecialchars($user['email']); ?> 
                                    <span class="text-slate-400 ms-1">(@<?php echo htmlspecialchars($user['username']); ?>)</span>
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class="border-0">
                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 small"><?php echo $user['role']; ?></span>
                    </td>
                    <td class="border-0">
                        <p class="mb-0 fw-bold small text-slate-700"><?php echo htmlspecialchars($user['business_name'] ?: 'N/A'); ?></p>
                    </td>
                    <td class="border-0">
                        <?php if ($user['status'] == 1): ?>
                            <span class="badge bg-success bg-opacity-10 text-success small"><i class="fas fa-check-circle me-1"></i> Active</span>
                        <?php else: ?>
                            <span class="badge bg-danger bg-opacity-10 text-danger small"><i class="fas fa-ban me-1"></i> Suspended</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 border-0 text-end">
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm rounded-circle border shadow-sm" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4 mt-2">
                                <li><a class="dropdown-item py-2 small fw-bold" href="javascript:void(0)" onclick='editUser(<?php echo htmlspecialchars(json_encode($user), ENT_QUOTES, "UTF-8"); ?>)'><i class="fas fa-edit text-primary me-2"></i> Edit Details</a></li>
                                <li>
                                    <a class="dropdown-item py-2 small fw-bold <?php echo $user['status'] == 1 ? 'text-warning' : 'text-success'; ?>" href="users.php?action=toggle&id=<?php echo $user['id']; ?>&business_id=<?php echo urlencode($filter_business_id); ?>">
                                        <i class="fas <?php echo $user['status'] == 1 ? 'fa-ban' : 'fa-check-circle'; ?> me-2"></i> 
                                        <?php echo $user['status'] == 1 ? 'Suspend User' : 'Activate User'; ?>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider opacity-50"></li>
                                <li><a class="dropdown-item py-2 small fw-bold text-danger" href="users.php?action=delete&id=<?php echo $user['id']; ?>&business_id=<?php echo urlencode($filter_business_id); ?>" onclick="return confirm('Are you sure you want to permanently delete this user?')"><i class="fas fa-trash me-2"></i> Delete User</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($users)): ?>
                <tr><td colspan="5" class="text-center py-5 text-muted small">No users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 px-4 pt-4">
                <h5 class="modal-title fw-800 text-slate-800"><i class="fas fa-user-edit text-primary me-2"></i> Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" class="modal-body p-4">
                <input type="hidden" name="edit_user_id" id="edit_user_id">
                
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small fw-bold text-muted">Username</label>
                        <input type="text" id="edit_username" class="form-control bg-light border-0 py-2 rounded-3" disabled>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label small fw-bold text-muted">Full Name</label>
                        <input type="text" name="full_name" id="edit_full_name" class="form-control bg-light border-0 py-2 rounded-3">
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label small fw-bold text-muted">Email</label>
                        <input type="email" name="email" id="edit_email" class="form-control bg-light border-0 py-2 rounded-3">
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label small fw-bold text-muted">Role</label>
                        <select name="role" id="edit_role" class="form-select bg-light border-0 py-2 rounded-3" required>
                            <option value="Admin">Admin</option>
                            <option value="Manager">Manager</option>
                            <option value="Cashier">Cashier</option>
                            <option value="Technician">Technician</option>
                        </select>
                    </div>
                </div>
                
                <div class="mt-4 text-end">
                    <button type="button" class="btn btn-light rounded-pill px-4 small fw-bold me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-premium px-5 shadow-sm">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editUser(user) {
    $('#edit_user_id').val(user.id);
    $('#edit_username').val(user.username);
    $('#edit_full_name').val(user.full_name);
    $('#edit_email').val(user.email);
    $('#edit_role').val(user.role);
    
    $('#editUserModal').modal('show');
}
</script>

<?php require_once 'includes/footer.php'; ?>
