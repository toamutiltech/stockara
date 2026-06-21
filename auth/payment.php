<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$plan_id = isset($_GET['plan_id']) ? (int)$_GET['plan_id'] : 0;
if (!$plan_id) {
    redirect('../dashboard.php');
}

$stmt = $pdo->prepare("SELECT * FROM subscription_plans WHERE id = ?");
$stmt->execute([$plan_id]);
$plan = $stmt->fetch();

if (!$plan || $plan['price'] == 0) {
    redirect('../dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Payment - Stockara</title>
    <link rel="icon" href="<?php echo BASE_URL; ?>assest/img/stockara-logo.jpg" />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #f8f9fc 0%, #eaeffa 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 0;
        }
        .payment-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 500px;
            padding: 40px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="payment-card mx-auto text-center">
                <div class="mb-4">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-credit-card fa-2x"></i>
                    </div>
                    <h3 class="fw-800">Complete Payment</h3>
                    <p class="text-muted">You're almost there! Complete your payment to activate your account.</p>
                </div>
                
                <div class="bg-light rounded-4 p-4 mb-4 text-start border">
                    <h5 class="fw-bold mb-1 text-primary"><?php echo htmlspecialchars($plan['name']); ?> Plan</h5>
                    <p class="small text-muted border-bottom pb-3 mb-3"><?php echo htmlspecialchars($plan['description']); ?></p>
                    
                    <label class="small fw-bold text-muted mb-1">Select Duration</label>
                    <select class="form-select border-0 bg-white mb-3 shadow-sm" id="months" onchange="updatePriceLabel()">
                        <option value="1">1 Month</option>
                        <option value="3">3 Months</option>
                        <option value="6">6 Months</option>
                        <option value="12">1 Year (2 Months FREE!)</option>
                    </select>

                    <div class="d-flex justify-content-between align-items-end mt-4">
                        <span class="text-muted small fw-bold text-uppercase">Total Payable</span>
                        <h2 class="fw-800 text-dark mb-0" id="total_price">₦<?php echo number_format($plan['price'], 2); ?></h2>
                    </div>
                </div>

                <button onclick="initPayment()" class="btn btn-primary btn-lg w-100 shadow-sm fw-bold rounded-pill">Pay Securely Now</button>
                
                <div class="mt-4 opacity-50">
                    <img src="https://checkout.paystack.com/assets/img/pstk-badge.png" alt="Paystack" height="30">
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
const monthlyPrice = <?php echo $plan['price']; ?>;
const planId = <?php echo $plan['id']; ?>;
const planName = "<?php echo addslashes($plan['name']); ?>";

function updatePriceLabel() {
    const months = parseInt(document.getElementById('months').value);
    let total = monthlyPrice * months;
    
    if (months === 12) {
        total = monthlyPrice * 10; // 2 months free
    }
    
    document.getElementById('total_price').innerText = '₦' + total.toLocaleString(undefined, {minimumFractionDigits: 2});
}

function initPayment() {
    const months = parseInt(document.getElementById('months').value);
    let finalAmount = monthlyPrice * months;
    
    if (months === 12) {
        finalAmount = monthlyPrice * 10;
    }

    const email = "<?php echo $_SESSION['email'] ?? 'customer@example.com'; ?>";
    
    let handler = PaystackPop.setup({
        key: 'pk_live_e879c0bf5537e2c768ca7d744fde1d73640bae4c', // Replace with test key if needed
        email: email,
        amount: finalAmount * 100, // in kobo
        currency: "NGN",
        ref: 'SKR-' + Math.floor((Math.random() * 1000000000) + 1),
        callback: function(response){
            window.location.href = "<?php echo BASE_URL; ?>settings/process_payment.php?reference=" + response.reference + "&plan_id=" + planId + "&months=" + months;
        },
        onClose: function(){
            alert('Transaction was not completed.');
        }
    });
    handler.openIframe();
}
</script>
</body>
</html>
