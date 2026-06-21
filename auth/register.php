<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (isLoggedIn()) {
    header('Location: ../dashboard.php');
    exit();
}

$error = '';
$success = '';

// Fetch available plans
$stmt = $pdo->prepare("SELECT * FROM subscription_plans ORDER BY price ASC");
$stmt->execute();
$plans = $stmt->fetchAll();

$selected_plan_id = isset($_GET['plan_id']) ? (int)$_GET['plan_id'] : 0;
if ($selected_plan_id === 0 && !empty($plans)) {
    $selected_plan_id = $plans[0]['id'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $business_name = clean($_POST['business_name']);
    $full_name = clean($_POST['full_name']);
    $username = clean($_POST['username']);
    $email = clean($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $plan_id = (int)$_POST['plan_id'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (!$plan_id) {
        $error = "Please select a subscription plan.";
    } else {
        // Check if username exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "Username is already taken.";
        } else {
            // Get selected plan details
            $stmt = $pdo->prepare("SELECT price, name FROM subscription_plans WHERE id = ?");
            $stmt->execute([$plan_id]);
            $selected_plan = $stmt->fetch();

            if (!$selected_plan) {
                $error = "Invalid plan selected.";
            } else {
                try {
                    $pdo->beginTransaction();

                    $is_paid_plan = ($selected_plan['price'] > 0);
                    $sub_status = $is_paid_plan ? 'Pending Payment' : 'Trial';
                    $expiry = $is_paid_plan ? date('Y-m-d') : date('Y-m-d', strtotime('+365 days'));

                    // 1. Create Business
                    $stmt = $pdo->prepare("INSERT INTO businesses (name, email, subscription_plan_id, subscription_status, subscription_expiry) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$business_name, $email, $plan_id, $sub_status, $expiry]);
                    $business_id = $pdo->lastInsertId();

                    // 2. Create Admin User
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO users (business_id, username, password, full_name, role, email) VALUES (?, ?, ?, ?, 'Admin', ?)");
                    $stmt->execute([$business_id, $username, $hashed_password, $full_name, $email]);
                    $user_id = $pdo->lastInsertId();

                    $pdo->commit();

                    // Auto Login
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['business_id'] = $business_id;
                    $_SESSION['business_name'] = $business_name;
                    $_SESSION['role'] = 'Admin';
                    $_SESSION['full_name'] = $full_name;
                    $_SESSION['subscription_status'] = $sub_status;
                    $_SESSION['subscription_expiry'] = $expiry;
                    $_SESSION['email'] = $email;

                    logActivity($pdo, $business_id, $user_id, "Registered new business and logged in", "Authentication");

                    // Redirect logic
                    if ($is_paid_plan) {
                        redirect('payment.php?plan_id=' . $plan_id, 'Please complete payment to activate your plan.');
                    } else {
                        redirect('../dashboard.php', 'Welcome to Stockara! Your trial has started.');
                    }

                } catch (Exception $e) {
                    $pdo->rollBack();
                    $error = "Registration failed: " . $e->getMessage();
                }
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
    <link rel="icon" href="<?php echo BASE_URL; ?>assest/img/stockara-logo.jpg" />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
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
            padding: 40px 0;
        }
        .login-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 800px;
            padding: 40px;
        }
        
        /* Custom Radio Buttons for Plans */
        .plan-radio {
            display: none;
        }
        .plan-card {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .plan-card:hover {
            border-color: #1cc88a;
            background-color: #f8fdfb;
        }
        .plan-radio:checked + .plan-card {
            border-color: #1cc88a;
            background-color: #e8f9f2;
            box-shadow: 0 4px 15px rgba(28, 200, 138, 0.2);
        }
        .plan-radio:checked + .plan-card .check-icon {
            color: #1cc88a;
            opacity: 1;
        }
        .check-icon {
            position: absolute;
            top: 15px;
            right: 15px;
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="login-card mx-auto">
                <div class="text-center mb-4">
                    <a href="<?php echo BASE_URL; ?>"><img src="<?php echo BASE_URL; ?>assest/img/stockara-logo.jpg" alt="Logo" height="50" class="mb-2 rounded shadow-sm"></a>
                    <h3 class="fw-800">Register Your Business</h3>
                    <p class="text-muted">Start managing your inventory and sales today.</p>
                </div>

                <?php if($error): ?>
                    <div class="alert alert-danger rounded-3"><i class="fas fa-exclamation-circle me-2"></i> <?php echo $error; ?></div>
                <?php endif; ?>

                <form action="" method="POST">
                    
                    <h5 class="fw-bold mb-3 mt-4"><i class="fas fa-box text-success me-2"></i> 1. Select a Plan</h5>
                    <div class="row g-3 mb-4">
                        <?php foreach($plans as $p): ?>
                        <div class="col-md-4">
                            <input type="radio" name="plan_id" id="plan_<?php echo $p['id']; ?>" class="plan-radio" value="<?php echo $p['id']; ?>" <?php echo $selected_plan_id == $p['id'] ? 'checked' : ''; ?>>
                            <label class="plan-card text-center position-relative w-100 h-100" for="plan_<?php echo $p['id']; ?>">
                                <i class="fas fa-check-circle check-icon"></i>
                                <h5 class="fw-bold mb-1"><?php echo $p['name']; ?></h5>
                                <?php if($p['price'] == 0): ?>
                                    <h3 class="text-success fw-bold mb-0">Free</h3>
                                <?php else: ?>
                                    <h3 class="text-success fw-bold mb-0">₦<?php echo number_format($p['price']); ?></h3>
                                    <small class="text-muted">/ month</small>
                                <?php endif; ?>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <h5 class="fw-bold mb-3"><i class="fas fa-store text-success me-2"></i> 2. Business Details</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Business Name</label>
                            <input type="text" name="business_name" class="form-control form-control-lg bg-light border-0" placeholder="e.g. TechHub Solutions" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Business Email</label>
                            <input type="email" name="email" class="form-control form-control-lg bg-light border-0" placeholder="business@example.com" required>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-3"><i class="fas fa-user text-success me-2"></i> 3. Admin Account</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Your Full Name</label>
                            <input type="text" name="full_name" class="form-control form-control-lg bg-light border-0" placeholder="John Doe" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Username</label>
                            <input type="text" name="username" class="form-control form-control-lg bg-light border-0" placeholder="admin" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Password</label>
                            <input type="password" name="password" class="form-control form-control-lg bg-light border-0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control form-control-lg bg-light border-0" required>
                        </div>
                    </div>
                    
                    <button type="submit" name="register" class="btn btn-success btn-lg w-100 mt-4 shadow-sm fw-bold">Create Account & Proceed</button>
                </form>
                
                <div class="text-center mt-4">
                    <p class="mb-0 text-muted">Already have an account? <a href="<?php echo BASE_URL; ?>auth/login.php" class="text-success fw-bold text-decoration-none">Login</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
