<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/db.php';

// Authentication Check for Super Admin
if (!isset($_SESSION['super_admin_id'])) {
    header("Location: " . BASE_URL . "super_admin/login.php");
    exit();
}

$super_admin_name = $_SESSION['super_admin_name'] ?? 'Super Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stockara Super Admin | Management Console</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary: #4e73df;
            --primary-dark: #224abe;
            --secondary: #1cc88a;
            --dark: #1a1c23;
            --light: #f8f9fc;
            --sidebar-width: 280px;
            --topbar-height: 70px;
            --premium-gradient: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f3f4f6;
            color: #374151;
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: #0f172a;
            color: #fff;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.1) transparent;
        }

        .sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
        }

        .sidebar-brand {
            height: var(--topbar-height);
            display: flex;
            align-items: center;
            padding: 0 25px;
            background: rgba(0,0,0,0.1);
        }

        .sidebar-brand span {
            font-size: 1.5rem;
            font-weight: 800;
            background: var(--premium-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-link {
            padding: 12px 25px;
            color: #94a3b8;
            font-weight: 500;
            border-radius: 12px;
            margin: 5px 15px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
        }

        .nav-link i {
            width: 30px;
            font-size: 1.1rem;
        }

        .nav-link:hover {
            color: #fff;
            background: rgba(255,255,255,0.05);
        }

        .nav-link.active {
            color: #fff;
            background: var(--primary);
            box-shadow: 0 4px 15px rgba(78, 115, 223, 0.4);
        }

        /* Main Content wrapper */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s;
        }

        /* Topbar */
        .topbar {
            height: var(--topbar-height);
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        }

        .stats-card {
            background: #fff;
            border-radius: 20px;
            padding: 25px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            transition: transform 0.3s;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .icon-box {
            width: 50px;
            height: 50px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }

        .btn-premium {
            background: var(--premium-gradient);
            color: #fff;
            border-radius: 12px;
            padding: 10px 25px;
            font-weight: 600;
            border: none;
            transition: 0.3s;
        }

        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.4);
            color: #fff;
        }

        .table-responsive {
            background: #fff;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        }

        .badge-premium {
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 600;
        }

        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
