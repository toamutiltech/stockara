<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$business_id = $_SESSION['business_id'];
$search = clean($_GET['search'] ?? '');

$query = "SELECT id, name, selling_price, quantity, barcode, sku FROM products WHERE business_id = ? AND quantity > 0";
$params = [$business_id];

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR barcode = ? OR sku = ?)";
    $params[] = "%$search%";
    $params[] = $search;
    $params[] = $search;
}

$query .= " ORDER BY name ASC LIMIT 50";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $products = $stmt->fetchAll();
    echo json_encode(['success' => true, 'products' => $products]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
