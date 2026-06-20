<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// Handle Create / Delete Actions
if (isset($_POST['create_admin'])) {
    $uname = clean($_POST['username']);
    $pass = $_POST['password'];
    $full_name = clean($_POST['full_name']);
    $email = clean($_POST['email']);
    
    // Check if username exists
    $stmt = $pdo->prepare("SELECT id FROM super_admins WHERE username = ?");
    $stmt->execute([$uname]);
    if ($stmt->fetch()) {
        redirect('super_admins.php', "Username '$uname' is already taken", 'danger');
    }
    
    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO super_admins (username, password, full_name, email) VALUES (?, ?, ?, ?)");
    $stmt->execute([$uname, $hashed_pass, $full_name, $email]);
    redirect('super_admins.php', "New master admin '$full_name' created successfully");
}

if (isset($_GET['delete_admin'])) {
    $id = $_GET['delete_admin'];
    
    // Prevent self-deletion for the primary admin (optional safety)
    if ($id == $_SESSION['super_admin_id']) {
        redirect('super_admins.php', "You cannot remove your own master account.", 'danger');
    }
    
    $stmt = $pdo->prepare("DELETE FROM super_admins WHERE id = ?");
    $stmt->execute([$id]);
    redirect('super_admins.php', "Master account removed.");
}

// Fetch All Admins
$stmt = $pdo->query("SELECT * FROM super_admins ORDER BY created_at DESC");
$admins = $stmt->fetchAll();
?>

<div class="row align-items-center mb-5">
    <div class="col-lg-6">
        <h2 class="fw-800 text-slate-800">Platform Controllers</h2>
        <p class="text-muted">You can authorize additional <span class="fw-bold">Global Administrators</span> to help manage the SaaS network.</p>
    </div>
    <div class="col-lg-6 text-lg-end">
        <button class="btn btn-premium shadow-sm" data-bs-toggle="modal" data-bs-target="#adminModal">
            <i class="fas fa-plus me-2"></i> Authorize New Master
        </button>
    </div>
</div>

<div class="row g-4">
    <?php foreach($admins as $admin): ?>
    <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden p-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="h-12 w-12 rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center font-bold" style="width: 50px; height: 50px; font-size: 1.2rem;">
                    <?php echo strtoupper(substr($admin['full_name'], 0, 1)); ?>
                </div>
                <div class="dropdown">
                    <button class="btn btn-light btn-sm rounded-pill border shadow-sm px-3" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-h me-1"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4 mt-2">
                        <li><a class="dropdown-item py-2 small fw-bold" href="#"><i class="fas fa-edit text-warning me-2"></i> Update</a></li>
                        <li><hr class="dropdown-divider opacity-50"></li>
                        <li><a class="dropdown-item py-2 small fw-bold text-danger" href="super_admins.php?delete_admin=<?php echo $admin['id']; ?>" onclick="return confirm('Revoke global access for this master admin?')"><i class="fas fa-user-minus me-2"></i> Revoke Access</a></li>
                    </ul>
                </div>
            </div>
            
            <h5 class="fw-800 text-slate-800 mb-1"><?php echo $admin['full_name']; ?></h5>
            <p class="text-muted small mb-3">@<?php echo $admin['username']; ?></p>
            
            <p class="mb-2 text-muted truncate-text" style="font-size: 0.85rem;"><i class="fas fa-envelope me-2 text-primary"></i> <?php echo $admin['email']; ?></p>
            <p class="mb-0 text-muted" style="font-size: 0.85rem;"><i class="fas fa-calendar-alt me-2 text-primary"></i> Since: <?php echo date('M d, Y', strtotime($admin['created_at'])); ?></p>
            
            <hr class="opacity-10">
            <div class="mt-2">
                <span class="badge bg-success bg-opacity-10 text-success rounded-3 border-0 py-2 px-3 fw-bold small">Master Level Access</span>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Add Admin Modal -->
<div class="modal fade" id="adminModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <form method="POST" class="p-4">
                <div class="modal-header border-0 px-0 pt-0">
                    <h5 class="modal-title fw-800 text-slate-800">
                        <i class="fas fa-user-plus text-primary me-2"></i> Authorize New Master
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="row g-3 modal-body px-0">
                    <div class="col-12">
                        <label class="form-label small fw-bold text-muted">Full Legal Name</label>
                        <input type="text" name="full_name" class="form-control bg-light border-0 py-2 rounded-3" placeholder="Antigravity AI" required>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label small fw-bold text-muted">Email Address</label>
                        <input type="email" name="email" class="form-control bg-light border-0 py-2 rounded-3" placeholder="admin@example.com" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Console Username</label>
                        <input type="text" name="username" class="form-control bg-light border-0 py-2 rounded-3" placeholder="master_alpha" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Console Password</label>
                        <input type="password" name="password" class="form-control bg-light border-0 py-2 rounded-3" placeholder="Keep it secure!" required>
                    </div>
                </div>
                
                <div class="mt-4 text-end">
                    <button type="button" class="btn btn-light rounded-pill px-4 small fw-bold me-2" data-bs-dismiss="modal">Discard</button>
                    <button type="submit" name="create_admin" class="btn btn-premium px-5 shadow-sm">Authorize Entry</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
