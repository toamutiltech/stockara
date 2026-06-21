<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// Fetch All Plans
$stmt = $pdo->query("SELECT * FROM subscription_plans ORDER BY price ASC");
$plans = $stmt->fetchAll();

// Add or Edit Plan logic would go here
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $name = clean($_POST['name']);
    $price = clean($_POST['price']);
    $duration = clean($_POST['duration_days']);
    $max_products = clean($_POST['max_products']);
    $max_users = clean($_POST['max_users']);
    $desc = clean($_POST['description']);
    $features = clean($_POST['features'] ?? '');
    
    if ($id) {
        $stmt = $pdo->prepare("UPDATE subscription_plans SET name = ?, price = ?, duration_days = ?, max_products = ?, max_users = ?, description = ?, features = ? WHERE id = ?");
        $stmt->execute([$name, $price, $duration, $max_products, $max_users, $desc, $features, $id]);
        redirect('plans.php', "Subscription plan '$name' updated successfully");
    } else {
        $stmt = $pdo->prepare("INSERT INTO subscription_plans (name, price, duration_days, max_products, max_users, description, features) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $price, $duration, $max_products, $max_users, $desc, $features]);
        redirect('plans.php', "New subscription plan '$name' created");
    }
}

// Handle Delete Plan
if (isset($_GET['delete_id'])) {
    $id = clean($_GET['delete_id']);
    $stmt = $pdo->prepare("DELETE FROM subscription_plans WHERE id = ?");
    $stmt->execute([$id]);
    redirect('plans.php', "Subscription plan deleted successfully");
}
?>

<div class="row align-items-center mb-5">
    <div class="col-lg-6">
        <h2 class="fw-800 text-slate-800">SaaS Yield & Plans</h2>
        <p class="text-muted">You can configure the subscription architecture for all <span class="fw-bold">Stockara</span> customers.</p>
    </div>
    <div class="col-lg-6 text-lg-end">
        <button class="btn btn-premium shadow-sm" data-bs-toggle="modal" data-bs-target="#planModal">
            <i class="fas fa-plus me-2"></i> Create Dynamic Tier
        </button>
    </div>
</div>

<div class="row g-4 mb-5">
    <?php foreach($plans as $plan): ?>
    <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden p-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 py-2 px-3 rounded-3 small fw-bold"> Tier ID: #<?php echo $plan['id']; ?></span>
                <div class="dropdown">
                    <button class="btn btn-light btn-sm rounded-pill border shadow-sm px-3" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-wrench me-2"></i> Action
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4 mt-2">
                        <li><a class="dropdown-item py-2 small fw-bold" href="javascript:void(0)" onclick='editPlan(<?php echo json_encode($plan); ?>)'><i class="fas fa-edit text-warning me-2"></i> Edit Tier Config</a></li>
                        <li><hr class="dropdown-divider opacity-50"></li>
                        <li><a class="dropdown-item py-2 small fw-bold text-danger" href="plans.php?delete_id=<?php echo $plan['id']; ?>" onclick="return confirm('Are you sure you want to delete this plan?');"><i class="fas fa-trash me-2"></i> Delete Plan</a></li>
                    </ul>
                </div>
            </div>
            
            <h4 class="fw-800 text-slate-800 mb-1"><?php echo $plan['name']; ?></h4>
            <p class="text-muted small truncate-text mb-4"><?php echo $plan['description']; ?></p>
            
            <h2 class="fw-800 text-primary mb-4">₦<?php echo number_format($plan['price']); ?><small class="text-muted h6 font-bold"> / <?php echo $plan['duration_days']; ?> Days</small></h2>
            
            <hr class="opacity-10 mt-0">
            
            <div class="plan-specs py-3">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-sq bg-success bg-opacity-10 text-success rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                        <i class="fas fa-box small"></i>
                    </div>
                    <div>
                        <p class="mb-0 fw-bold small"><?php echo $plan['max_products'] == -1 ? "Unlimited Products" : $plan['max_products'] . " Products Max"; ?></p>
                        <p class="mb-0 text-muted" style="font-size: 0.7rem;">Inventory capacity limit</p>
                    </div>
                </div>
                
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-sq bg-info bg-opacity-10 text-info rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                        <i class="fas fa-users small"></i>
                    </div>
                    <div>
                        <p class="mb-0 fw-bold small"><?php echo $plan['max_users'] == -1 ? "Unlimited Team Members" : $plan['max_users'] . " Users Max"; ?></p>
                        <p class="mb-0 text-muted" style="font-size: 0.7rem;">Account user seats limit</p>
                    </div>
                </div>
                
                <?php if(!empty($plan['features'])): ?>
                <?php $plan_features = explode(',', $plan['features']); ?>
                <div class="mt-3 border-top pt-3">
                    <p class="fw-bold small mb-2 text-slate-800">Additional Features:</p>
                    <ul class="list-unstyled mb-0 small text-muted">
                        <?php foreach($plan_features as $feature): ?>
                        <li class="mb-1"><i class="fas fa-check-circle text-success me-2"></i> <?php echo htmlspecialchars(trim($feature)); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
            
            <button class="btn btn-light border w-100 rounded-3 mt-4 py-2 small fw-bold text-muted hover-shadow" onclick='editPlan(<?php echo json_encode($plan); ?>)'>Update Parameters</button>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Plan Modal -->
<div class="modal fade" id="planModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 px-4 pt-4">
                <h5 class="modal-title fw-800 text-slate-800"><i class="fas fa-credit-card text-primary me-2"></i> Configure Tier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="planForm" method="POST" class="modal-body p-4">
                <input type="hidden" name="id" id="plan_id">
                
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small fw-bold text-muted">Plan Name</label>
                        <input type="text" name="name" id="plan_name" class="form-control bg-light border-0 py-2 rounded-3" placeholder="e.g. Pro Monthly" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Price (₦)</label>
                        <input type="number" name="price" id="plan_price" class="form-control bg-light border-0 py-2 rounded-3" placeholder="5000" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Duration (Days)</label>
                        <input type="number" name="duration_days" id="plan_duration" class="form-control bg-light border-0 py-2 rounded-3" placeholder="30" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Max Products (-1 = Unltd)</label>
                        <input type="number" name="max_products" id="plan_products" class="form-control bg-light border-0 py-2 rounded-3" placeholder="100" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Max Users (-1 = Unltd)</label>
                        <input type="number" name="max_users" id="plan_users" class="form-control bg-light border-0 py-2 rounded-3" placeholder="5" required>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label small fw-bold text-muted">Plan Description</label>
                        <textarea name="description" id="plan_desc" class="form-control bg-light border-0 py-2 rounded-3" rows="3" placeholder="Dynamic plan details..."></textarea>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label small fw-bold text-muted">Additional Features (Comma separated)</label>
                        <textarea name="features" id="plan_features" class="form-control bg-light border-0 py-2 rounded-3" rows="2" placeholder="e.g. Priority Support, Custom Domain"></textarea>
                    </div>
                </div>
                
                <div class="mt-4 text-end">
                    <button type="button" class="btn btn-light rounded-pill px-4 small fw-bold me-2" data-bs-dismiss="modal">Discard</button>
                    <button type="submit" class="btn btn-premium px-5 shadow-sm">Commit Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editPlan(plan) {
    $('#plan_id').val(plan.id);
    $('#plan_name').val(plan.name);
    $('#plan_price').val(plan.price);
    $('#plan_duration').val(plan.duration_days);
    $('#plan_products').val(plan.max_products);
    $('#plan_users').val(plan.max_users);
    $('#plan_desc').val(plan.description);
    $('#plan_features').val(plan.features);
    
    $('#planModal').modal('show');
}

// Clear modal on close
$('#planModal').on('hidden.bs.modal', function () {
    $('#planForm')[0].reset();
    $('#plan_id').val('');
});
</script>

<?php require_once 'includes/footer.php'; ?>
