<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if (!isLoggedIn()) exit(json_encode(['success' => false, 'message' => 'Unauthorized']));

$business_id = $_SESSION['business_id'];
$user_id = $_SESSION['user_id'];

$customer_id = $_POST['customer_id'] ?: null;
$payment_method = $_POST['payment_method'];
$discount = (float)$_POST['discount'];
$items = $_POST['items'] ?? [];

if (empty($items)) {
    exit(json_encode(['success' => false, 'message' => 'Cart is empty']));
}

try {
    $pdo->beginTransaction();

    // 1. Calculate totals
    $subtotal = 0;
    foreach($items as $item) {
        $subtotal += ($item['price'] * $item['qty']);
    }
    $tax = (float)$_POST['tax'];
    $grand_total = ($subtotal - $discount) + $tax;

    // 2. Insert Sale
    $stmt = $pdo->prepare("INSERT INTO sales (business_id, user_id, customer_id, total_amount, discount_amount, tax_amount, grand_total, payment_method) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$business_id, $user_id, $customer_id == 0 ? null : $customer_id, $subtotal, $discount, $tax, $grand_total, $payment_method]);
    $sale_id = $pdo->lastInsertId();

    // 3. Insert Items and update stock
    foreach($items as $item) {
        $item_subtotal = $item['price'] * $item['qty'];
        $stmt = $pdo->prepare("INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$sale_id, $item['id'], $item['qty'], $item['price'], $item_subtotal]);

        // Deduct Stock
        $stmt = $pdo->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
        $stmt->execute([$item['qty'], $item['id']]);
    }

    logActivity($pdo, $business_id, $user_id, "Completed sale: #$sale_id", "POS");
    
    $pdo->commit();
    echo json_encode(['success' => true, 'id' => $sale_id]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
