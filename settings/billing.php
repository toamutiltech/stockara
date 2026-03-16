<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

checkRole(['Admin']);
$business_id = $_SESSION['business_id'];

// Get Current Plan
$stmt = $pdo->prepare("SELECT b.*, p.name as plan_name, p.price as plan_price, p.description as plan_desc 
                       FROM businesses b 
                       LEFT JOIN subscription_plans p ON b.subscription_plan_id = p.id 
                       WHERE b.id = ?");
$stmt->execute([$business_id]);
$biz = $stmt->fetch();

// Get Available Plans (excluding current if active)
$stmt = $pdo->prepare("SELECT * FROM subscription_plans WHERE price > 0 ORDER BY price ASC");
$stmt->execute();
$plans = $stmt->fetchAll();

// Get Payment History
$stmt = $pdo->prepare("SELECT s.*, p.name as plan_name FROM subscriptions s JOIN subscription_plans p ON s.plan_id = p.id WHERE s.business_id = ? ORDER BY s.created_at DESC");
$stmt->execute([$business_id]);
$history = $stmt->fetchAll();

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Billing & Subscriptions</h1>
</div>

<div class="row">
    <!-- Current Plan Status -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card shadow h-100 py-2 border-left-primary">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Current Plan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $biz['plan_name']; ?></div>
                        <p class="text-muted small mt-2"><?php echo $biz['plan_desc']; ?></p>
                    </div>
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-muted text-uppercase">Status</div>
                        <?php 
                        $badge = 'bg-success';
                        if($biz['subscription_status'] == 'Expired') $badge = 'bg-danger';
                        if($biz['subscription_status'] == 'Trial') $badge = 'bg-info';
                        ?>
                        <span class="badge <?php echo $badge; ?>"><?php echo $biz['subscription_status']; ?></span>
                    </div>
                    <div class="text-end">
                        <div class="text-xs text-muted text-uppercase">Expires On</div>
                        <span class="fw-bold"><?php echo date('d M Y', strtotime($biz['subscription_expiry'])); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upgrade Plans -->
    <div class="col-xl-8 col-md-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 bg-white">
                <h6 class="m-0 font-weight-bold text-primary">Available Subscription Plans</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach($plans as $p): ?>
                    <div class="col-md-6 mb-3">
                        <div class="border rounded p-3 text-center h-100 d-flex flex-column justify-content-between">
                            <div>
                                <h5 class="fw-bold"><?php echo $p['name']; ?></h5>
                                <h3 class="text-primary fw-bold">₦<?php echo number_format($p['price'], 2); ?><small class="text-muted" style="font-size: 0.5em;">/mo</small></h3>
                                <ul class="list-unstyled text-start small mt-3">
                                    <li><i class="fas fa-check text-success me-2"></i> <?php echo $p['max_products'] == -1 ? 'Unlimited' : $p['max_products']; ?> Products</li>
                                    <li><i class="fas fa-check text-success me-2"></i> <?php echo $p['max_users'] == -1 ? 'Unlimited' : $p['max_users']; ?> Users</li>
                                    <li><i class="fas fa-check text-success me-2"></i> <?php echo $p['description']; ?></li>
                                </ul>
                            </div>
                            <button onclick="subscribePlan(<?php echo $p['id']; ?>, '<?php echo $p['name']; ?>', <?php echo $p['price']; ?>)" class="btn btn-outline-primary w-100 mt-3">Select Plan</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment History -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Subscription History</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Plan</th>
                        <th>Amount Paid</th>
                        <th>Validity Period</th>
                        <th>Ref</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($history)): ?>
                        <tr><td colspan="6" class="text-center">No payment history found.</td></tr>
                    <?php else: ?>
                        <?php foreach($history as $h): ?>
                        <tr>
                            <td><?php echo date('d M Y', strtotime($h['created_at'])); ?></td>
                            <td><?php echo $h['plan_name']; ?></td>
                            <td>₦<?php echo number_format($h['amount_paid'], 2); ?></td>
                            <td><?php echo date('d M', strtotime($h['start_date'])); ?> - <?php echo date('d M Y', strtotime($h['end_date'])); ?></td>
                            <td><small class="text-muted"><?php echo $h['payment_reference']; ?></small></td>
                            <td><span class="badge bg-success"><?php echo $h['payment_status']; ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
function subscribePlan(planId, planName, amount) {
    const email = "<?php echo $_SESSION['email'] ?? 'customer@example.com'; ?>"; // Ideally fetch from DB
    
    // In a real app, you'd call your backend to get an initializing ref
    // For this demo, we use a simple JS handler
    
    let handler = PaystackPop.setup({
        key: 'pk_test_cca6c3e9497340ab06f6d036e0d9cf758b70fb22', // Replace with your public key
        email: email,
        amount: amount * 100, // in kobo
        currency: "NGN",
        ref: 'SKR-' + Math.floor((Math.random() * 1000000000) + 1),
        callback: function(response){
            // Send to backend for verification
            window.location.href = "<?php echo BASE_URL; ?>settings/process_payment.php?reference=" + response.reference + "&plan_id=" + planId;
        },
        onClose: function(){
            alert('Transaction was not completed, window closed.');
        }
    });
    handler.openIframe();
}
</script>

<?php include '../includes/footer.php'; ?>
