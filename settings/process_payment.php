<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

checkRole(['Admin']);

$business_id = $_SESSION['business_id'];
$reference = $_GET['reference'] ?? '';
$plan_id = (int)($_GET['plan_id'] ?? 0);

if (!$reference || !$plan_id) {
    redirect(BASE_URL . 'settings/billing.php', "Invalid payment data.", "danger");
}

/* 
|--------------------------------------------------
| PAYSTACK VERIFICATION (SERVER-SIDE)
|--------------------------------------------------
| In a production app, you would verify this ref with Paystack API
*/

try {
    // Get Plan Info
    $stmt = $pdo->prepare("SELECT p.*, b.subscription_expiry FROM subscription_plans p, businesses b WHERE p.id = ? AND b.id = ?");
    $stmt->execute([$plan_id, $business_id]);
    $data = $stmt->fetch();
    $plan = $data;
    $biz = $data;

    if (!$plan) throw new Exception("Invalid plan selected.");

    $pdo->beginTransaction();

    $months = (int)($_GET['months'] ?? 1);
    
    // 1. Calculate new expiry
    // If current subscription is active, add to it. If expired, start from today.
    $current_expiry = $biz['subscription_expiry']; // Better to check DB value
    $today = date('Y-m-d');
    
    $start_date = ($today > $current_expiry) ? $today : $current_expiry;
    $end_date = date('Y-m-d', strtotime($start_date . " + $months months"));

    // Calculate actual amount paid based on the 2-months-free rule
    $amount_paid = $plan['price'] * $months;
    if ($months === 12) {
        $amount_paid = $plan['price'] * 10;
    }

    // 2. Record Subscription History
    $stmt = $pdo->prepare("INSERT INTO subscriptions (business_id, plan_id, amount_paid, start_date, end_date, payment_reference, payment_status) VALUES (?, ?, ?, ?, ?, ?, 'Paid')");
    $stmt->execute([$business_id, $plan_id, $amount_paid, $start_date, $end_date, $reference]);

    // 3. Update Business Table
    $stmt = $pdo->prepare("UPDATE businesses SET subscription_plan_id = ?, subscription_status = 'Active', subscription_expiry = ? WHERE id = ?");
    $stmt->execute([$plan_id, $end_date, $business_id]);

    // 4. Update Session
    $_SESSION['subscription_status'] = 'Active';
    $_SESSION['subscription_expiry'] = $end_date;

    logActivity($pdo, $business_id, $_SESSION['user_id'], "Renewed subscription: " . $plan['name'], "Finance");

    $pdo->commit();
    redirect(BASE_URL . 'settings/billing.php', "Subscription activated! Your new expiry is " . date('d M Y', strtotime($end_date)));

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    redirect(BASE_URL . 'settings/billing.php', "Error: " . $e->getMessage(), "danger");
}
?>
