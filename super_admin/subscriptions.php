<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// Handle Actions (Mark Paid, Delete)
if (isset($_GET['action'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];
    
    if ($action == 'mark_paid') {
        $stmt = $pdo->prepare("UPDATE subscriptions SET payment_status = 'Paid' WHERE id = ?");
        $stmt->execute([$id]);
        
        // After marking as paid, update business status to Active (optional logic)
        $stmt = $pdo->prepare("SELECT business_id FROM subscriptions WHERE id = ?");
        $stmt->execute([$id]);
        $bid = $stmt->fetchColumn();
        
        $stmt = $pdo->prepare("UPDATE businesses SET subscription_status = 'Active' WHERE id = ?");
        $stmt->execute([$bid]);
        
        redirect('subscriptions.php', "Subscription marked as PAID and business activated");
    } elseif ($action == 'delete') {
        $stmt = $pdo->prepare("DELETE FROM subscriptions WHERE id = ?");
        $stmt->execute([$id]);
        redirect('subscriptions.php', "Subscription record removed");
    }
}

// Fetch All Subscriptions
$stmt = $pdo->query("SELECT s.*, b.name as business_name, p.name as plan_name 
                    FROM subscriptions s 
                    JOIN businesses b ON s.business_id = b.id 
                    JOIN subscription_plans p ON s.plan_id = p.id 
                    ORDER BY s.created_at DESC");
$subscriptions = $stmt->fetchAll();
?>

<div class="row align-items-center mb-5">
    <div class="col-lg-6">
        <h2 class="fw-800 text-slate-800">Transaction Logs</h2>
        <p class="text-muted">Monitor and manage all <span class="fw-bold">Payment History</span> across the SaaS network.</p>
    </div>
    <div class="col-lg-6 text-lg-end">
        <a href="dashboard.php" class="btn btn-outline-secondary rounded-pill px-4 me-2 small fw-bold shadow-sm">
            <i class="fas fa-chevron-left me-2"></i> Back to Stats
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
    <div class="card-header bg-white py-4 px-4 border-0">
        <h5 class="mb-0 fw-bold">Recent Billing Cycles</h5>
    </div>
    <div class="table-responsive p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="px-4 border-0 text-muted small fw-bold text-uppercase">Reference</th>
                    <th class="border-0 text-muted small fw-bold text-uppercase">Portal Name</th>
                    <th class="border-0 text-muted small fw-bold text-uppercase">Tier</th>
                    <th class="border-0 text-muted small fw-bold text-uppercase">Financial Status</th>
                    <th class="border-0 text-muted small fw-bold text-uppercase">Amount (₦)</th>
                    <th class="border-0 text-muted small fw-bold text-uppercase">Expiry Date</th>
                    <th class="px-4 border-0 text-muted small fw-bold text-uppercase text-end">Control</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($subscriptions as $sub): ?>
                <tr>
                    <td class="px-4 border-0 py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <i class="fas fa-receipt small"></i>
                            </div>
                            <div>
                                <p class="mb-0 fw-bold small">Ref: <?php echo $sub['payment_reference'] ?: $sub['id']; ?></p>
                                <p class="mb-0 text-muted text-xs small" style="font-size: 0.75rem;"><?php echo date('M d, Y', strtotime($sub['created_at'])); ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="border-0 small fw-bold"><?php echo $sub['business_name']; ?></td>
                    <td class="border-0">
                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1 small rounded-3" style="font-size: 0.7rem;">
                            <?php echo $sub['plan_name']; ?>
                        </span>
                    </td>
                    <td class="border-0">
                        <span class="badge badge-premium <?php 
                            echo $sub['payment_status'] == 'Paid' ? 'bg-success bg-opacity-10 text-success' : ($sub['payment_status'] == 'Pending' ? 'bg-warning bg-opacity-10 text-warning' : 'bg-danger bg-opacity-10 text-danger'); 
                        ?>">
                            <?php echo $sub['payment_status']; ?>
                        </span>
                    </td>
                    <td class="border-0 small fw-bold">₦<?php echo number_format($sub['amount_paid'], 2); ?></td>
                    <td class="border-0 small text-muted"><?php echo date('M d, Y', strtotime($sub['end_date'])); ?></td>
                    <td class="px-4 border-0 text-end">
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm rounded-circle border shadow-sm" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4 mt-2">
                                <?php if($sub['payment_status'] == 'Pending'): ?>
                                <li><a class="dropdown-item py-2 small fw-bold" href="subscriptions.php?id=<?php echo $sub['id']; ?>&action=mark_paid"><i class="fas fa-check-circle text-success me-2"></i> Confirm Payment</a></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item py-2 small fw-bold" href="#"><i class="fas fa-arrow-down-long text-primary me-2"></i> Download Invoice</a></li>
                                <li><hr class="dropdown-divider opacity-50"></li>
                                <li><a class="dropdown-item py-2 small fw-bold text-danger" href="subscriptions.php?id=<?php echo $sub['id']; ?>&action=delete" onclick="return confirm('Delete this record?')"><i class="fas fa-trash me-2"></i> Delete Trace</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($subscriptions)): ?>
                <tr><td colspan="7" class="text-center py-5 text-muted small">No subscription records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
