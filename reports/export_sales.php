<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

checkRole(['Admin', 'Manager']);

$business_id = $_SESSION['business_id'];
$start_date = $_GET['start'] ?? date('Y-m-01');
$end_date = $_GET['end'] ?? date('Y-m-d');

// Fetch sales with customer names
$stmt = $pdo->prepare("SELECT s.*, c.name as customer_name, u.full_name as user_name 
                      FROM sales s 
                      LEFT JOIN customers c ON s.customer_id = c.id 
                      LEFT JOIN users u ON s.user_id = u.id 
                      WHERE s.business_id = ? AND DATE(s.created_at) BETWEEN ? AND ? 
                      ORDER BY s.created_at DESC");
$stmt->execute([$business_id, $start_date, $end_date]);
$sales = $stmt->fetchAll();

// CSV Header
$filename = "sales_report_" . $start_date . "_to_" . $end_date . ".csv";
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '";');

$output = fopen('php://output', 'w');
fputcsv($output, ['ID', 'Date', 'Customer', 'Cashier', 'Subtotal', 'Discount', 'Tax', 'Grand Total', 'Payment Method', 'Status']);

foreach ($sales as $sale) {
    fputcsv($output, [
        $sale['id'],
        $sale['created_at'],
        $sale['customer_name'] ?: 'Guest',
        $sale['user_name'],
        $sale['total_amount'],
        $sale['discount_amount'],
        $sale['tax_amount'],
        $sale['grand_total'],
        $sale['payment_method'],
        $sale['status']
    ]);
}

fclose($output);
exit();
?>
