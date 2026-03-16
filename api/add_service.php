<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $business_id = $_SESSION['business_id'];
    $user_id = $_SESSION['user_id'];
    
    $title = clean($_POST['service_title'] ?? '');
    $description = clean($_POST['description'] ?? '');
    $item = clean($_POST['item_or_device'] ?? '');
    $customer_id = $_POST['customer_id'] ?: null;
    $cost = (float)($_POST['cost'] ?? 0);
    $status = $_POST['status'] ?? 'Pending';

    if (empty($title) || empty($item)) {
        echo json_encode(['success' => false, 'message' => 'Service title and item are required']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO services (business_id, customer_id, user_id, service_title, description, item_or_device, cost, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$business_id, $customer_id, $user_id, $title, $description, $item, $cost, $status]);
        
        logActivity($pdo, $business_id, $user_id, "Recorded service: $title (Mobile)", "Services");
        echo json_encode(['success' => true, 'message' => 'Service record created']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
