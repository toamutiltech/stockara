<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// Fetch All Businesses
$stmt = $pdo->query("SELECT businesses.*, subscription_plans.name as plan_name FROM businesses LEFT JOIN subscription_plans ON businesses.subscription_plan_id = subscription_plans.id ORDER BY businesses.created_at DESC");
$businesses = $stmt->fetchAll();

// Handle Business Actions (Toggle Status, Delete)
if (isset($_GET['action'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];
    
    if ($action == 'toggle_status') {
        $stmt = $pdo->prepare("SELECT subscription_status FROM businesses WHERE id = ?");
        $stmt->execute([$id]);
        $current = $stmt->fetchColumn();
        $new_status = ($current == 'Active') ? 'Cancelled' : 'Active';
        
        $stmt = $pdo->prepare("UPDATE businesses SET subscription_status = ? WHERE id = ?");
        $stmt->execute([$new_status, $id]);
        redirect('businesses.php', "Business status updated to $new_status");
    }
}
?>

<div class="row align-items-center mb-5">
    <div class="col-lg-6">
        <h2 class="fw-800 text-slate-800">Managed Portals</h2>
        <p class="text-muted">You are viewing all registered businesses on the <span class="fw-bold">KeepRecord SaaS</span> network.</p>
    </div>
    <div class="col-lg-6 text-lg-end">
        <a href="dashboard.php" class="btn btn-outline-secondary rounded-pill px-4 me-2 small fw-bold shadow-sm">
            <i class="fas fa-chevron-left me-2"></i> Back to Stats
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
    <div class="card-header bg-white py-4 px-4 d-flex align-items-center justify-content-between border-0">
        <div class="d-flex align-items-center gap-3 w-50">
            <i class="fas fa-filter text-muted"></i>
            <input type="text" class="form-control form-control-sm bg-light border-0 py-2 rounded-3 w-50" placeholder="Filter by name or email...">
        </div>
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-light border small font-bold">Export <i class="fas fa-download ms-1"></i></button>
        </div>
    </div>
    <div class="table-responsive p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="px-4 border-0 text-muted small fw-bold text-uppercase">Portal Detail</th>
                    <th class="border-0 text-muted small fw-bold text-uppercase">Revenue Support</th>
                    <th class="border-0 text-muted small fw-bold text-uppercase">Tier / Plan</th>
                    <th class="border-0 text-muted small fw-bold text-uppercase">Subscription Status</th>
                    <th class="border-0 text-muted small fw-bold text-uppercase">Registration Date</th>
                    <th class="px-4 border-0 text-muted small fw-bold text-uppercase text-end">Action Control</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($businesses as $business): ?>
                <tr>
                    <td class="px-4 border-0 py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                <i class="fas fa-building fa-lg"></i>
                            </div>
                            <div>
                                <p class="mb-0 fw-bold small"><?php echo $business['name']; ?></p>
                                <p class="mb-0 text-muted text-xs small" style="font-size: 0.75rem;"><i class="fas fa-envelope me-1"></i> <?php echo $business['email']; ?></p>
                                <p class="mb-0 text-muted text-xs small" style="font-size: 0.75rem;"><i class="fas fa-phone me-1"></i> <?php echo $business['phone']; ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="border-0 small text-muted">₦0.00 <span class="xs opacity-50 d-block">lifetime spend</span></td>
                    <td class="border-0">
                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1 small rounded-3" style="font-size: 0.7rem;">
                            <?php echo $business['plan_name'] ?? 'Custom Plan'; ?>
                        </span>
                    </td>
                    <td class="border-0">
                        <span class="badge badge-premium <?php 
                            echo $business['subscription_status'] == 'Active' ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger'; 
                        ?>">
                            <?php echo $business['subscription_status']; ?>
                        </span>
                        <p class="mb-0 text-xs small mt-1 opacity-50" style="font-size: 0.7rem;">Expires: <?php echo $business['subscription_expiry'] ?: 'N/A'; ?></p>
                    </td>
                    <td class="border-0 small text-muted"><?php echo date('M d, Y', strtotime($business['created_at'])); ?></td>
                    <td class="px-4 border-0 text-end">
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm rounded-circle border shadow-sm" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4 mt-2">
                                <li><a class="dropdown-item py-2 small fw-bold" href="#"><i class="fas fa-eye text-primary me-2"></i> View Full Profile</a></li>
                                <li><a class="dropdown-item py-2 small fw-bold" href="#"><i class="fas fa-edit text-warning me-2"></i> Edit Credentials</a></li>
                                <li><hr class="dropdown-divider opacity-50"></li>
                                <li>
                                    <a class="dropdown-item py-2 small fw-bold text-danger" href="businesses.php?id=<?php echo $business['id']; ?>&action=toggle_status">
                                        <i class="fas fa-toggle-on me-2"></i> Toggle Activation
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($businesses)): ?>
                <tr><td colspan="6" class="text-center py-5 text-muted small">No business portals recorded in the system.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
