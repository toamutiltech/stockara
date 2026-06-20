<?php
require_once '../includes/functions.php';
require_once '../includes/db.php';

if (isset($_SESSION['super_admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

// Check if there are any super admins
$stmt = $pdo->query("SELECT COUNT(*) FROM super_admins");
$adminCount = $stmt->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean($_POST['username']);
    $password = $_POST['password'];

    // If no admin exists, first login will create the default admin (Development behavior)
    if ($adminCount == 0) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO super_admins (username, password, full_name, email) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $hashed_password, 'Platform Administrator', 'admin@keeprecord.com']);
        $adminCount = 1;
    }

    $stmt = $pdo->prepare("SELECT * FROM super_admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['super_admin_id'] = $admin['id'];
        $_SESSION['super_admin_name'] = $admin['full_name'];
        redirect('dashboard.php', "Welcome back, " . $admin['full_name']);
    } else {
        $error = "Invalid master credentials.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KeepRecord | Master Console Login</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #4e73df;
            --gradient: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: #f3f4f6;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background-image: radial-gradient(#d1d5db 0.5px, transparent 0.5px);
            background-size: 10px 10px;
        }

        .login-card {
            width: 100%;
            max-width: 450px;
            background: #fff;
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.05);
            padding: 50px;
            position: relative;
            z-index: 10;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: -10px;
            left: -10px;
            right: -10px;
            bottom: -10px;
            background: var(--gradient);
            border-radius: 40px;
            z-index: -1;
            opacity: 0.05;
        }

        .logo-box {
            width: 70px;
            height: 70px;
            background: var(--gradient);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 2rem;
            margin: 0 auto 30px;
            box-shadow: 0 10px 20px rgba(78, 115, 223, 0.3);
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 20px;
            background: #f9f9fb;
            border: 2px solid #f1f1f4;
            transition: 0.3s;
        }

        .form-control:focus {
            background: #fff;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(78, 115, 223, 0.1);
        }

        .btn-login {
            background: var(--gradient);
            color: #fff;
            border-radius: 12px;
            padding: 12px;
            font-weight: 700;
            border: none;
            width: 100%;
            margin-top: 20px;
            box-shadow: 0 10px 20px rgba(78, 115, 223, 0.2);
            transition: 0.3s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px rgba(78, 115, 223, 0.3);
            color: #fff;
        }

        .floating-circles div {
            position: absolute;
            background: var(--gradient);
            border-radius: 50%;
            opacity: 0.1;
            z-index: 1;
        }
    </style>
</head>
<body>
    <div class="floating-circles d-none d-md-block">
        <div style="width: 300px; height: 300px; top: -150px; left: -150px;"></div>
        <div style="width: 200px; height: 200px; bottom: -100px; right: -100px;"></div>
    </div>

    <div class="login-card text-center">
        <div class="logo-box">
            <i class="fas fa-shield-alt"></i>
        </div>
        
        <h3 class="fw-800 mb-1">Master Console</h3>
        <p class="text-muted small mb-4">Authorize to access the SaaS management portal.</p>
        
        <?php if ($adminCount == 0): ?>
            <div class="alert alert-info py-2 small rounded-3">
                <i class="fas fa-info-circle me-2"></i> Initial setup mode: First login creates the admin.
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger py-2 small rounded-3">
                <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3 text-start">
                <label class="form-label small fw-bold text-muted">Master Username</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0"><i class="fas fa-user text-muted"></i></span>
                    <input type="text" name="username" class="form-control border-0" placeholder="admin" required autofocus>
                </div>
            </div>
            
            <div class="mb-4 text-start">
                <label class="form-label small fw-bold text-muted">Master Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0"><i class="fas fa-lock text-muted"></i></span>
                    <input type="password" name="password" class="form-control border-0" placeholder="••••••••" required>
                </div>
            </div>
            
            <button type="submit" class="btn btn-login">Authorize Entry</button>
        </form>
        
        <div class="mt-5 pt-3">
            <p class="small text-muted mb-0">&copy; <?php echo date('Y'); ?> KeepRecord SaaS Framework</p>
            <a href="../index.php" class="text-decoration-none small font-bold">Return to Public Portal</a>
        </div>
    </div>
</body>
</html>
