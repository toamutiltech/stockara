<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

checkRole(['Admin', 'Manager', 'Cashier']);

$business_id = $_SESSION['business_id'];
$id = (int)$_GET['id'];

// Get Product
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND business_id = ?");
$stmt->execute([$id, $business_id]);
$product = $stmt->fetch();

if (!$product) {
    die("Product not found.");
}

$barcode = $product['barcode'];
$name = $product['name'];
$price = number_format($product['selling_price'], 2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Barcode - <?php echo $name; ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .barcode-card {
            width: 50mm;
            border: 1px solid #eee;
            padding: 10px;
            text-align: center;
            margin-bottom: 20px;
            display: inline-block;
        }
        .name { font-size: 12px; font-weight: bold; margin-bottom: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .price { font-size: 14px; font-weight: bold; margin-bottom: 5px; }
        .barcode-img { width: 100%; height: auto; }
        .barcode-text { font-size: 10px; margin-top: 5px; letter-spacing: 2px; }
        @media print {
            .no-print { display: none; }
            .barcode-card { border: none; }
        }
    </style>
</head>
<body>

<div class="no-print" style="margin-bottom: 20px;">
    <button onclick="window.print()">Print Labels</button>
    <input type="number" id="qty" value="1" min="1" style="width: 50px;"> labels
    <button onclick="duplicateLabels()">Apply Quantity</button>
    <a href="<?php echo BASE_URL; ?>products/index.php">Back to Products</a>
</div>

<div id="labelsContainer">
    <div class="barcode-card">
        <div class="name"><?php echo $name; ?></div>
        <div class="price">₦<?php echo $price; ?></div>
        <!-- Using a public barcode generator API for simplicity in this demo environment -->
        <img class="barcode-img" src="https://bwipjs-api.metafloor.com/?bcid=code128&text=<?php echo urlencode($barcode); ?>&scale=2&rotate=N&includetext=false" alt="Barcode">
        <div class="barcode-text"><?php echo $barcode; ?></div>
    </div>
</div>

<script>
function duplicateLabels() {
    const qty = document.getElementById('qty').value;
    const container = document.getElementById('labelsContainer');
    const original = container.querySelector('.barcode-card');
    container.innerHTML = '';
    for(let i=0; i<qty; i++) {
        container.appendChild(original.cloneNode(true));
    }
}
</script>

</body>
</html>
