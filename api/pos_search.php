<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) exit(json_encode(['success' => false]));

$business_id = $_SESSION['business_id'];
$q = clean($_GET['q'] ?? '');

if ($q) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE business_id = ? AND (barcode = ? OR name LIKE ? OR sku = ?) LIMIT 1");
    $stmt->execute([$business_id, $q, "%$q%", $q]);
    $product = $stmt->fetch();

    if ($product) {
        echo json_encode(['success' => true, 'product' => $product]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}
?>
