<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

checkRole(['Admin', 'Manager', 'Cashier']);

$sale_id = (int)$_GET['id'];
$business_id = $_SESSION['business_id'];

// Get Sale details
$stmt = $pdo->prepare("SELECT s.*, b.name as biz_name, b.address as biz_addr, b.phone as biz_phone, b.receipt_footer, u.full_name as cashier FROM sales s JOIN businesses b ON s.business_id = b.id JOIN users u ON s.user_id = u.id WHERE s.id = ? AND s.business_id = ?");
$stmt->execute([$sale_id, $business_id]);
$sale = $stmt->fetch();

if (!$sale) {
    die("Sale not found.");
}

// Get Sale items
$stmt = $pdo->prepare("SELECT si.*, p.name FROM sale_items si JOIN products p ON si.product_id = p.id WHERE si.sale_id = ?");
$stmt->execute([$sale_id]);
$items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt #<?php echo $sale_id; ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; font-size: 14px; width: 80mm; margin: 0 auto; padding: 10px; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .header { margin-bottom: 20px; }
        .header h2 { margin: 0; text-transform: uppercase; }
        .info { border-bottom: 1px dashed #000; padding-bottom: 10px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; border-bottom: 1px solid #000; }
        td { padding: 5px 0; }
        .total-row { border-top: 1px solid #000; margin-top: 10px; padding-top: 10px; }
        .footer { margin-top: 20px; font-size: 12px; border-top: 1px dashed #000; padding-top: 10px; }
        @media print {
            .no-print { display: none; }
            body { margin: 0; width: 80mm; }
        }
    </style>
</head>
<body>

<div class="no-print" style="margin-bottom: 20px;">
    <button onclick="window.print()">Print Receipt</button>
    <a href="<?php echo BASE_URL; ?>pos/index.php">Back to POS</a>
</div>

<div class="header text-center">
    <h2><?php echo $sale['biz_name']; ?></h2>
    <p><?php echo $sale['biz_addr']; ?><br>
    Tel: <?php echo $sale['biz_phone']; ?></p>
</div>

<div class="info">
    <p>Receipt: #<?php echo str_pad($sale['id'], 6, '0', STR_PAD_LEFT); ?><br>
    Date: <?php echo date('d-M-Y H:i', strtotime($sale['created_at'])); ?><br>
    Cashier: <?php echo $sale['cashier']; ?></p>
</div>

<table>
    <thead>
        <tr>
            <th>Item</th>
            <th class="text-center">Qty</th>
            <th class="text-end">Sub</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($items as $item): ?>
        <tr>
            <td><?php echo $item['name']; ?></td>
            <td class="text-center"><?php echo $item['quantity']; ?></td>
            <td class="text-end"><?php echo number_format($item['subtotal'], 2); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="total-row">
    <div style="display: flex; justify-content: space-between;">
        <span>Subtotal:</span>
        <span>₦<?php echo number_format($sale['total_amount'], 2); ?></span>
    </div>
    <?php if($sale['discount_amount'] > 0): ?>
    <div style="display: flex; justify-content: space-between;">
        <span>Discount:</span>
        <span>-₦<?php echo number_format($sale['discount_amount'], 2); ?></span>
    </div>
    <?php endif; ?>
    <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 16px; margin-top: 5px;">
        <span>TOTAL:</span>
        <span>₦<?php echo number_format($sale['grand_total'], 2); ?></span>
    </div>
</div>

<div class="info" style="border-top: 1px dashed #000; margin-top: 10px; padding-top: 10px;">
    <p>Payment: <?php echo $sale['payment_method']; ?></p>
</div>

<div class="footer text-center">
    <p><?php echo $sale['receipt_footer'] ?: 'Thank you for your patronage!'; ?></p>
    <p>Powered by Stockara</p>
</div>

<script>
    // window.onload = function() { window.print(); }
</script>

</body>
</html>
