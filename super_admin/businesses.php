<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// Fetch All Businesses
$stmt = $pdo->query("SELECT businesses.*, subscription_plans.name as plan_name FROM businesses LEFT JOIN subscription_plans ON businesses.subscription_plan_id = subscription_plans.id ORDER BY businesses.created_at DESC");
$businesses = $stmt->fetchAll();

// Handle Form Submissions (Register / Edit)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'register_business') {
        $name = clean($_POST['name']);
        $email = clean($_POST['email']);
        $phone = clean($_POST['phone']);
        $plan_id = clean($_POST['subscription_plan_id']);
        $expiry = clean($_POST['subscription_expiry']);
        
        $username = clean($_POST['username']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $full_name = clean($_POST['full_name']);

        try {
            $pdo->beginTransaction();
            // Insert Business
            $stmt = $pdo->prepare("INSERT INTO businesses (name, email, phone, subscription_plan_id, subscription_status, subscription_expiry) VALUES (?, ?, ?, ?, 'Active', ?)");
            $stmt->execute([$name, $email, $phone, $plan_id, $expiry]);
            $business_id = $pdo->lastInsertId();

            // Insert Default Admin
            $stmt = $pdo->prepare("INSERT INTO users (business_id, username, password, full_name, role, email) VALUES (?, ?, ?, ?, 'Admin', ?)");
            $stmt->execute([$business_id, $username, $password, $full_name, $email]);

            $pdo->commit();
            redirect('businesses.php', "Business $name registered successfully.");
        } catch (Exception $e) {
            $pdo->rollBack();
            redirect('businesses.php', "Error registering business: " . $e->getMessage(), 'danger');
        }
    } elseif (isset($_POST['action']) && $_POST['action'] == 'edit_business') {
        $id = $_POST['business_id'];
        $name = clean($_POST['name']);
        $email = clean($_POST['email']);
        $phone = clean($_POST['phone']);
        $plan_id = clean($_POST['subscription_plan_id']);
        $expiry = clean($_POST['subscription_expiry']);
        $status = clean($_POST['subscription_status']);

        $stmt = $pdo->prepare("UPDATE businesses SET name = ?, email = ?, phone = ?, subscription_plan_id = ?, subscription_expiry = ?, subscription_status = ? WHERE id = ?");
        $stmt->execute([$name, $email, $phone, $plan_id, $expiry, $status, $id]);
        redirect('businesses.php', "Business $name updated successfully.");
    }
}

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

// Fetch Plans for forms
$plans = $pdo->query("SELECT * FROM subscription_plans")->fetchAll();
?>

<div class="row align-items-center mb-5">
    <div class="col-lg-6">
        <h2 class="fw-800 text-slate-800">Managed Portals</h2>
        <p class="text-muted">You are viewing all registered businesses on the <span class="fw-bold">Stockara SaaS</span> network.</p>
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
            <button class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm font-bold" data-bs-toggle="modal" data-bs-target="#registerBusinessModal">
                <i class="fas fa-plus me-1"></i> Register New Business
            </button>
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
                                    <a class="dropdown-item py-2 small fw-bold" href="#" data-bs-toggle="modal" data-bs-target="#editBusinessModal" 
                                       data-id="<?php echo $business['id']; ?>"
                                       data-name="<?php echo htmlspecialchars($business['name']); ?>"
                                       data-email="<?php echo htmlspecialchars($business['email']); ?>"
                                       data-phone="<?php echo htmlspecialchars($business['phone']); ?>"
                                       data-plan="<?php echo $business['subscription_plan_id']; ?>"
                                       data-status="<?php echo $business['subscription_status']; ?>"
                                       data-expiry="<?php echo $business['subscription_expiry']; ?>"
                                       onclick="populateEditModal(this)">
                                        <i class="fas fa-edit text-warning me-2"></i> Edit Credentials
                                    </a>
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

<!-- Register Business Modal -->
<div class="modal fade" id="registerBusinessModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="businesses.php" method="POST">
          <div class="modal-header">
            <h5 class="modal-title fw-bold">Register New Business</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <input type="hidden" name="action" value="register_business">
              <h6 class="mb-3 border-bottom pb-2 text-primary">Business Details</h6>
              <div class="row mb-3">
                  <div class="col-md-6">
                      <label class="form-label small fw-bold">Business Name</label>
                      <input type="text" name="name" class="form-control" required>
                  </div>
                  <div class="col-md-6">
                      <label class="form-label small fw-bold">Business Email</label>
                      <input type="email" name="email" class="form-control" required>
                  </div>
              </div>
              <div class="row mb-3">
                  <div class="col-md-6">
                      <label class="form-label small fw-bold">Phone Number</label>
                      <input type="text" name="phone" class="form-control">
                  </div>
                  <div class="col-md-6">
                      <label class="form-label small fw-bold">Subscription Plan</label>
                      <select name="subscription_plan_id" class="form-select">
                          <?php foreach($plans as $plan): ?>
                              <option value="<?php echo $plan['id']; ?>"><?php echo $plan['name']; ?></option>
                          <?php endforeach; ?>
                      </select>
                  </div>
              </div>
              <div class="row mb-4">
                  <div class="col-md-6">
                      <label class="form-label small fw-bold">Subscription Expiry</label>
                      <input type="date" name="subscription_expiry" class="form-control" required>
                  </div>
              </div>

              <h6 class="mb-3 border-bottom pb-2 text-primary">Default Admin Details</h6>
              <div class="row mb-3">
                  <div class="col-md-6">
                      <label class="form-label small fw-bold">Admin Full Name</label>
                      <input type="text" name="full_name" class="form-control" required>
                  </div>
                  <div class="col-md-6">
                      <label class="form-label small fw-bold">Admin Username</label>
                      <input type="text" name="username" class="form-control" required>
                  </div>
              </div>
              <div class="row mb-3">
                  <div class="col-md-6">
                      <label class="form-label small fw-bold">Admin Password</label>
                      <input type="password" name="password" class="form-control" required>
                  </div>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Register Business</button>
          </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Business Modal -->
<div class="modal fade" id="editBusinessModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="businesses.php" method="POST">
          <div class="modal-header">
            <h5 class="modal-title fw-bold">Edit Business Credentials</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <input type="hidden" name="action" value="edit_business">
              <input type="hidden" name="business_id" id="edit_business_id">
              
              <div class="mb-3">
                  <label class="form-label small fw-bold">Business Name</label>
                  <input type="text" name="name" id="edit_name" class="form-control" required>
              </div>
              <div class="mb-3">
                  <label class="form-label small fw-bold">Business Email</label>
                  <input type="email" name="email" id="edit_email" class="form-control" required>
              </div>
              <div class="mb-3">
                  <label class="form-label small fw-bold">Phone Number</label>
                  <input type="text" name="phone" id="edit_phone" class="form-control">
              </div>
              <div class="mb-3">
                  <label class="form-label small fw-bold">Subscription Plan</label>
                  <select name="subscription_plan_id" id="edit_plan" class="form-select">
                      <?php foreach($plans as $plan): ?>
                          <option value="<?php echo $plan['id']; ?>"><?php echo $plan['name']; ?></option>
                      <?php endforeach; ?>
                  </select>
              </div>
              <div class="mb-3">
                  <label class="form-label small fw-bold">Subscription Status</label>
                  <select name="subscription_status" id="edit_status" class="form-select">
                      <option value="Trial">Trial</option>
                      <option value="Active">Active</option>
                      <option value="Expired">Expired</option>
                      <option value="Cancelled">Cancelled</option>
                  </select>
              </div>
              <div class="mb-3">
                  <label class="form-label small fw-bold">Subscription Expiry</label>
                  <input type="date" name="subscription_expiry" id="edit_expiry" class="form-control" required>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-warning">Save Changes</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script>
function populateEditModal(element) {
    document.getElementById('edit_business_id').value = element.getAttribute('data-id');
    document.getElementById('edit_name').value = element.getAttribute('data-name');
    document.getElementById('edit_email').value = element.getAttribute('data-email');
    document.getElementById('edit_phone').value = element.getAttribute('data-phone');
    document.getElementById('edit_plan').value = element.getAttribute('data-plan');
    document.getElementById('edit_status').value = element.getAttribute('data-status');
    document.getElementById('edit_expiry').value = element.getAttribute('data-expiry');
}
</script>
