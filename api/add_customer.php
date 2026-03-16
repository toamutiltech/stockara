<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$business_id = $_SESSION['business_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = clean($_POST['name']);
    $phone = clean($_POST['phone']);

    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Name is required']);
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO customers (business_id, name, phone) VALUES (?, ?, ?)");
        $stmt->execute([$business_id, $name, $phone]);
        $id = $pdo->lastInsertId();

        echo json_encode(['success' => true, 'id' => $id, 'name' => $name]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
