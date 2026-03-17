<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stockara - Inventory & POS Management</title>
    <meta name="title" content="Stockara | All-in-One Inventory, POS & Service Management System">
    <meta name="description" content="Manage your products, sales, and services from a single powerful system. Stockara helps businesses stay organized, improve efficiency, and grow with confidence.">
    <meta name="keywords" content="Stockara, inventory management system, POS software, service record system, business management tool, barcode scanner POS, repair shop software, pharmacy inventory, warehouse management">
    <meta name="author" content="Stockara Tech">

    <meta property="og:type" content="website">
    <meta property="og:url" content="https://stockara.toamultitech.tech/">
    <meta property="og:title" content="Stockara | Modern Inventory & POS">
    <meta property="og:description" content="Streamline your sales and services with our cloud-ready management platform.">
    <meta property="og:image" content="<?php echo BASE_URL; ?>assets/img/stockara.jpg">

    <link rel="icon" href="<?php echo BASE_URL; ?>assest/img/stockara-logo.jpg" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --dark-bg: #1a1c23;
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8f9fc;
            overflow-x: hidden;
        }

        #wrapper {
            display: flex;
            width: 100%;
        }

        #content-wrapper {
            width: 100%;
            height: 100vh;
            overflow-y: auto;
            background-color: #f8f9fc;
            transition: all 0.3s;
        }

        /* Sidebar Styling */
        #sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
            color: #fff;
            transition: all 0.3s;
            z-index: 1000;
        }

        #sidebar .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        #sidebar ul.components {
            padding: 20px 0;
        }

        #sidebar ul li a {
            padding: 12px 25px;
            display: block;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            font-size: 1rem;
            transition: 0.3s;
        }

        #sidebar ul li a:hover {
            color: #fff;
            background: rgba(255,255,255,0.1);
        }

        #sidebar ul li.active > a {
            color: #fff;
            background: rgba(255,255,255,0.2);
            font-weight: 600;
        }

        #sidebar ul li a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        /* Topbar Styling */
        .topbar {
            height: 70px;
            background: #fff;
            display: flex;
            align-items: center;
            padding: 0 25px;
            box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15);
            margin-bottom: 25px;
        }

        /* Dark Mode Support */
        body.dark-mode {
            background-color: var(--dark-bg);
            color: #e1e1e1;
        }

        body.dark-mode #content-wrapper {
            background-color: var(--dark-bg);
        }

        body.dark-mode .topbar {
            background: #24262d;
            border-bottom: 1px solid #333;
        }

        body.dark-mode .card {
            background: #24262d;
            border-color: #333;
            color: #fff;
        }
        
        body.dark-mode .table {
            color: #e1e1e1;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 24px;
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 15px 20px;
            font-weight: 700;
            color: var(--primary-color);
        }

        /* Responsive Sidebar */
        @media (max-width: 768px) {
            #sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            #sidebar.active {
                margin-left: 0;
            }
        }
    </style>
</head>
<body class="<?php echo isset($_COOKIE['dark_mode']) && $_COOKIE['dark_mode'] == 'enabled' ? 'dark-mode' : ''; ?>">
    <div id="wrapper">
