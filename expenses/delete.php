<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

checkRole(['Admin']);

$business_id = $_SESSION['business_id'];
$id = (int)$_GET['id'];

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM expenses WHERE id = ? AND business_id = ?");
    $stmt->execute([$id, $business_id]);
    
    logActivity($pdo, $business_id, $_SESSION['user_id'], "Deleted expense record #$id", "Finance");
    redirect(BASE_URL . 'expenses/index.php', "Record deleted successfully.");
} else {
    redirect(BASE_URL . 'expenses/index.php', "Invalid ID.");
}
?>
