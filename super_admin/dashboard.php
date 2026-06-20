<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// Fetch Global Stats
// 1. Total Businesses
$stmt = $pdo->query("SELECT COUNT(*) FROM businesses");
$total_businesses = $stmt->fetchColumn();

// 2. Total Users
$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$total_users = $stmt->fetchColumn();

// 3. Active Subscriptions
$stmt = $pdo->query("SELECT COUNT(*) FROM businesses WHERE subscription_status = 'Active'");
$active_subs = $stmt->fetchColumn();

// 4. Revenue (Trial value for now)
$stmt = $pdo->query("SELECT SUM(amount_paid) FROM subscriptions WHERE payment_status = 'Paid'");
$total_revenue = $stmt->fetchColumn() ?? 0;

// Fetch Recent Businesses
$stmt = $pdo->query("SELECT * FROM businesses ORDER BY created_at DESC LIMIT 5");
$recent_businesses = $stmt->fetchAll();

// Fetch Recent Messages
$stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5");
$recent_messages = $stmt->fetchAll();
?>

<div class="row align-items-center mb-5">
    <div class="col-lg-6">
        <h2 class="fw-800 text-slate-800">Master Console Overview</h2>
        <p class="text-muted">You are managing <span class="fw-bold text-primary"><?php echo $total_businesses; ?></span> active business portals.</p>
    </div>
    <div class="col-lg-6 text-lg-end">
        <a href="businesses.php" class="btn btn-premium shadow-sm">
            <i class="fas fa-plus me-2"></i> Register New Business
        </a>
    </div>
</div>

<!-- Stats Grid -->
<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon-box bg-primary bg-opacity-10 text-primary">
                <i class="fas fa-store"></i>
            </div>
            <h6 class="text-muted text-uppercase small fw-bold">Total Businesses</h6>
            <h2 class="fw-800 mb-2"><?php echo $total_businesses; ?></h2>
            <div class="text-success small fw-bold"><i class="fas fa-arrow-up me-1"></i> 12% increase</div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon-box bg-success bg-opacity-10 text-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <h6 class="text-muted text-uppercase small fw-bold">Active Subscriptions</h6>
            <h2 class="fw-800 mb-2"><?php echo $active_subs; ?></h2>
            <div class="text-muted small fw-bold">across all tiers</div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon-box bg-warning bg-opacity-10 text-warning">
                <i class="fas fa-users"></i>
            </div>
            <h6 class="text-muted text-uppercase small fw-bold">Total Platform Users</h6>
            <h2 class="fw-800 mb-2"><?php echo $total_users; ?></h2>
            <div class="text-danger small fw-bold"><i class="fas fa-arrow-down me-1"></i> 4% churn</div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stats-card border-primary border-opacity-10">
            <div class="icon-box bg-info bg-opacity-10 text-info">
                <i class="fas fa-wallet"></i>
            </div>
            <h6 class="text-muted text-uppercase small fw-bold">Gross Platform Revenue</h6>
            <h2 class="fw-800 mb-2">₦<?php echo number_format($total_revenue); ?></h2>
            <div class="text-primary small fw-bold">lifetime earnings</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Businesses -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 px-4 d-flex align-items-center justify-content-between border-0">
                <h5 class="mb-0 fw-bold">Newly Registered Portals</h5>
                <a href="businesses.php" class="text-primary text-decoration-none small fw-bold">Manage All <i class="fas fa-chevron-right ms-1"></i></a>
            </div>
            <div class="table-responsive p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 border-0 text-muted small fw-bold text-uppercase">Business</th>
                            <th class="border-0 text-muted small fw-bold text-uppercase">Plan</th>
                            <th class="border-0 text-muted small fw-bold text-uppercase">Status</th>
                            <th class="border-0 text-muted small fw-bold text-uppercase">Date</th>
                            <th class="px-4 border-0 text-muted small fw-bold text-uppercase text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($recent_businesses as $business): ?>
                        <tr>
                            <td class="px-4 border-0">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <i class="fas fa-building small"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0 fw-bold small"><?php echo $business['name']; ?></p>
                                        <p class="mb-0 text-muted text-xs small" style="font-size: 0.75rem;"><?php echo $business['email']; ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="border-0 small text-muted">Plan ID: <?php echo $business['subscription_plan_id']; ?></td>
                            <td class="border-0">
                                <span class="badge badge-premium <?php 
                                    echo $business['subscription_status'] == 'Active' ? 'bg-success bg-opacity-10 text-success' : 'bg-warning bg-opacity-10 text-warning'; 
                                ?>">
                                    <?php echo $business['subscription_status']; ?>
                                </span>
                            </td>
                            <td class="border-0 small text-muted"><?php echo date('M d, Y', strtotime($business['created_at'])); ?></td>
                            <td class="px-4 border-0 text-end">
                                <a href="businesses.php?id=<?php echo $business['id']; ?>" class="btn btn-light btn-sm rounded-circle"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($recent_businesses)): ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">No businesses registered yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- System Feed / Messages -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-white py-3 px-4 d-flex align-items-center justify-content-between border-0">
                <h5 class="mb-0 fw-bold">Recent Inquiries</h5>
                <a href="messages.php" class="text-primary text-decoration-none small fw-bold">Read All</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php foreach($recent_messages as $msg): ?>
                    <div class="list-group-item px-4 py-3 border-0 border-bottom">
                        <div class="d-flex w-100 justify-content-between mb-1">
                            <h6 class="mb-0 fw-bold small h-title truncate-text"><?php echo $msg['subject']; ?></h6>
                            <small class="text-muted" style="font-size: 0.7rem;"><?php echo date('M d', strtotime($msg['created_at'])); ?></small>
                        </div>
                        <p class="mb-1 text-muted small truncate-text" style="font-size: 0.8rem;"><?php echo $msg['message']; ?></p>
                        <small class="text-primary fw-bold" style="font-size: 0.75rem;"><?php echo $msg['full_name']; ?></small>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($recent_messages)): ?>
                    <div class="text-center py-5 text-muted small">No messages received.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
