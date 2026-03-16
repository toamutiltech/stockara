<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

checkRole(['Admin', 'Manager']);

$business_id = $_SESSION['business_id'];
$id = (int)$_GET['id'];

// Check if product belongs to business
$stmt = $pdo->prepare("SELECT name FROM products WHERE id = ? AND business_id = ?");
$stmt->execute([$id, $business_id]);
$product = $stmt->fetch();

if ($product) {
    // Check if product is in any sales
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM sale_items WHERE product_id = ?");
    $stmt->execute([$id]);
    if ($stmt->fetchColumn() > 0) {
        redirect(BASE_URL . 'products/index.php', "Cannot delete product because it has sales history. Consider disabling it instead.", "danger");
    }

    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ? AND business_id = ?");
    $stmt->execute([$id, $business_id]);
    
    logActivity($pdo, $business_id, $_SESSION['user_id'], "Deleted product: " . $product['name'], "Inventory");
    redirect(BASE_URL . 'products/index.php', "Product deleted successfully.");
} else {
    redirect(BASE_URL . 'products/index.php', "Product not found.", "danger");
}
?>
