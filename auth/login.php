<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (isLoggedIn()) {
    header('Location: ../dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = clean($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $pdo->prepare("SELECT u.*, b.name as business_name, b.subscription_status, b.subscription_expiry, b.subscription_plan_id FROM users u JOIN businesses b ON u.business_id = b.id WHERE u.username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] == 0) {
                $error = "Your account is disabled.";
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['business_id'] = $user['business_id'];
                $_SESSION['business_name'] = $user['business_name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['subscription_status'] = $user['subscription_status'];
                $_SESSION['subscription_expiry'] = $user['subscription_expiry'];
                
                logActivity($pdo, $user['business_id'], $user['id'], "User logged in", "Authentication");
                
                redirect('../dashboard.php', "Welcome back, " . $user['full_name']);
            }
        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Stockara</title>
    <meta name="title" content="Stockara | All-in-One Inventory, POS & Service Management System">
    <meta name="description" content="Manage your products, sales, and services from a single powerful system. Stockara helps businesses stay organized, improve efficiency, and grow with confidence.">
    <meta name="keywords" content="Stockara, inventory management system, POS software, service record system, business management tool, barcode scanner POS, repair shop software, pharmacy inventory, warehouse management">
    <meta name="author" content="Stockara Tech">

    <meta property="og:type" content="website">
    <meta property="og:url" content="https://getstockara.com.ng/">
    <meta property="og:title" content="Stockara | Modern Inventory & POS">
    <meta property="og:description" content="Streamline your sales and services with our cloud-ready management platform.">
    <meta property="og:image" content="<?php echo BASE_URL; ?>assets/img/stockara.jpg">

    <link rel="icon" href="<?php echo BASE_URL; ?>assest/img/stockara-logo.jpg" />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
            padding: 40px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header i {
            font-size: 3rem;
            color: #4e73df;
            margin-bottom: 10px;
        }
        .btn-primary {
            background: #4e73df;
            border: none;
            padding: 12px;
            font-weight: 600;
        }
        .btn-primary:hover {
            background: #224abe;
        }
        .form-control {
            padding: 12px;
            border-radius: 8px;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header text-center">
        <img src="../assest/img/stockara-logo.jpg" alt="Logo" height="60" class="mb-3 rounded shadow">
       
        <p class="text-muted small">Modern Inventory & POS</p>
    </div>

    <?php if($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" placeholder="Enter username" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Enter password" required>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="remember">
            <label class="form-check-label" for="remember">Remember me</label>
        </div>
        <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
    </form>
    
    <div class="text-center mt-4">
        <p class="mb-0">Don't have an account? <a href="register.php" class="text-primary fw-bold">Register Business</a></p>
    </div>
</div>

</body>
</html>
