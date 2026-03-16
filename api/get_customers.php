<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$business_id = $_SESSION['business_id'];

$stmt = $pdo->prepare("SELECT id, name, phone FROM customers WHERE business_id = ? ORDER BY name ASC");
$stmt->execute([$business_id]);
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'customers' => $customers]);
?>
