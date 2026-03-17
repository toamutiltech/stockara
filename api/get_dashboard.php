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
$user_id = $_SESSION['user_id'];

try {
    // Get last 5 sales
    $stmt = $pdo->prepare("SELECT s.*, c.name as customer_name 
                          FROM sales s 
                          LEFT JOIN customers c ON s.customer_id = c.id 
                          WHERE s.business_id = ? 
                          ORDER BY s.created_at DESC LIMIT 5");
    $stmt->execute([$business_id]);
    $recent_sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get last 5 services
    $stmt = $pdo->prepare("SELECT s.*, c.name as customer_name 
                          FROM services s 
                          LEFT JOIN customers c ON s.customer_id = c.id 
                          WHERE s.business_id = ? 
                          ORDER BY s.created_at DESC LIMIT 5");
    $stmt->execute([$business_id]);
    $recent_services = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get daily stats
    $today = date('Y-m-d');
    $stmt = $pdo->prepare("SELECT SUM(grand_total) as total_sales FROM sales WHERE business_id = ? AND DATE(created_at) = ?");
    $stmt->execute([$business_id, $today]);
    $daily_sales = $stmt->fetch(PDO::FETCH_ASSOC)['total_sales'] ?? 0;

    $stmt = $pdo->prepare("SELECT COUNT(*) as pending_services FROM services WHERE business_id = ? AND status = 'Pending'");
    $stmt->execute([$business_id]);
    $pending_services = $stmt->fetch(PDO::FETCH_ASSOC)['pending_services'] ?? 0;

    echo json_encode([
        'success' => true,
        'recent_sales' => $recent_sales,
        'recent_services' => $recent_services,
        'stats' => [
            'daily_sales' => $daily_sales,
            'pending_services' => $pending_services
        ]
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
