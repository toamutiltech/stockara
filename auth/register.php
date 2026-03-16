<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $business_name = clean($_POST['business_name']);
    $full_name = clean($_POST['full_name']);
    $username = clean($_POST['username']);
    $email = clean($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if username exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "Username is already taken.";
        } else {
            try {
                $pdo->beginTransaction();

                // 1. Create Business
                $expiry = date('Y-m-d', strtotime('+14 days'));
                $stmt = $pdo->prepare("INSERT INTO businesses (name, email, subscription_expiry) VALUES (?, ?, ?)");
                $stmt->execute([$business_name, $email, $expiry]);
                $business_id = $pdo->lastInsertId();

                // 2. Create Admin User
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (business_id, username, password, full_name, role, email) VALUES (?, ?, ?, ?, 'Admin', ?)");
                $stmt->execute([$business_id, $username, $hashed_password, $full_name, $email]);

                $pdo->commit();
                $success = "Business registered successfully! You can now <a href='" . BASE_URL . "auth/login.php'>Login</a>.";
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = "Registration failed: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Business - Stockara</title>
            <meta name="title" content="Stockara | All-in-One Inventory, POS & Service Management System">
    <meta name="description" content="Manage your products, sales, and services from a single powerful system. Stockara helps businesses stay organized, improve efficiency, and grow with confidence.">
    <meta name="keywords" content="Stockara, inventory management system, POS software, service record system, business management tool, barcode scanner POS, repair shop software, pharmacy inventory, warehouse management">
    <meta name="author" content="Stockara Tech">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://stockara.io/">
    <meta property="og:title" content="Stockara | Modern Inventory & POS">
    <meta property="og:description" content="Streamline your sales and services with our cloud-ready management platform.">
    <meta property="og:image" content="<?php echo BASE_URL; ?>assest/img/og-image.jpg">

    <link rel="icon" href="<?php echo BASE_URL; ?>assest/img/stockara-logo.jpg" />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }
        .login-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 500px;
            padding: 40px;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="text-center mb-4">
        <a href="<?php echo BASE_URL; ?>"><img src="<?php echo BASE_URL; ?>assest/img/stockara-logo.jpg" alt="Logo" height="50" class="mb-2 rounded shadow-sm"></a>
        <h3>Register Your Business</h3>
        <p class="text-muted">Start managing your inventory and sales today.</p>
    </div>

    <?php if($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label">Business Name</label>
                <input type="text" name="business_name" class="form-control" placeholder="e.g. TechHub Solutions" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Your Full Name</label>
                <input type="text" name="full_name" class="form-control" placeholder="John Doe" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="admin" required>
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="business@example.com" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
        </div>
        <button type="submit" name="register" class="btn btn-success w-100 py-2">Register Business</button>
    </form>
    
    <div class="text-center mt-4">
        <p class="mb-0">Already have an account? <a href="<?php echo BASE_URL; ?>auth/login.php" class="text-success fw-bold">Login</a></p>
    </div>
</div>

</body>
</html>
