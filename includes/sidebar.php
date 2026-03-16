<!-- Sidebar -->
<nav id="sidebar">
    <div class="sidebar-header d-flex align-items-center justify-content-center">
        <img src="<?php echo BASE_URL; ?>assest/img/stockara-logo.jpg" alt="Logo" height="35" class="me-2 rounded shadow-sm">
    </div>

    <ul class="list-unstyled components">
        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL; ?>dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        </li>
        <li class="<?php echo strpos($_SERVER['PHP_SELF'], '/products/') !== false ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL; ?>products/index.php"><i class="fas fa-boxes"></i> Products</a>
        </li>
        <li class="<?php echo strpos($_SERVER['PHP_SELF'], '/pos/') !== false ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL; ?>pos/index.php"><i class="fas fa-shopping-cart"></i> POS Sales</a>
        </li>
        <li class="<?php echo strpos($_SERVER['PHP_SELF'], '/services/') !== false ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL; ?>services/index.php"><i class="fas fa-tools"></i> Services</a>
        </li>
        <li class="<?php echo strpos($_SERVER['PHP_SELF'], '/customers/') !== false ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL; ?>customers/index.php"><i class="fas fa-users"></i> Customers</a>
        </li>
        <li class="<?php echo strpos($_SERVER['PHP_SELF'], '/reports/') !== false ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL; ?>reports/index.php"><i class="fas fa-chart-line"></i> Reports</a>
        </li>
        <li class="<?php echo strpos($_SERVER['PHP_SELF'], '/expenses/') !== false ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL; ?>expenses/index.php"><i class="fas fa-wallet"></i> Expenses</a>
        </li>
        <?php if($_SESSION['role'] == 'Admin'): ?>
        <li class="<?php echo strpos($_SERVER['PHP_SELF'], '/users/') !== false ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL; ?>users/index.php"><i class="fas fa-user-shield"></i> Users</a>
        </li>
        <?php endif; ?>
        <li class="<?php echo strpos($_SERVER['PHP_SELF'], '/settings/index.php') !== false ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL; ?>settings/index.php"><i class="fas fa-cog"></i> Settings</a>
        </li>
        <?php if($_SESSION['role'] == 'Admin'): ?>
        <li class="<?php echo strpos($_SERVER['PHP_SELF'], '/settings/billing.php') !== false ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL; ?>settings/billing.php"><i class="fas fa-credit-card"></i> Billing & SaaS</a>
        </li>
        <li class="<?php echo strpos($_SERVER['PHP_SELF'], '/settings/logs.php') !== false ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL; ?>settings/logs.php"><i class="fas fa-history"></i> Activity Logs</a>
        </li>
        <?php endif; ?>
        <li>
            <a href="<?php echo BASE_URL; ?>auth/logout.php" class="text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </li>
    </ul>

    <!-- Subscription Status Badge -->
    <div class="px-4 py-2 mt-4 mx-3 rounded bg-light border">
        <div class="text-xs text-muted text-uppercase mb-1">Subscription</div>
        <?php 
        $expiry = new DateTime($_SESSION['subscription_expiry']);
        $today = new DateTime();
        $diff = $today->diff($expiry);
        $days = (int)$diff->format("%r%a");
        
        $color = 'text-success';
        if($days <= 3) $color = 'text-danger';
        else if($days <= 7) $color = 'text-warning';
        ?>
        <div class="h6 mb-0 fw-bold <?php echo $color; ?>">
            <?php echo $days > 0 ? $days . " Days Left" : ($days == 0 ? "Expires Today" : "Expired"); ?>
        </div>
        <a href="<?php echo BASE_URL; ?>settings/billing.php" class="text-xs">Manage Plan</a>
    </div>
</nav>
<!-- End Sidebar -->

<div id="content-wrapper">
    <!-- Topbar -->
    <nav class="topbar">
        <button type="button" id="sidebarCollapse" class="btn btn-link d-md-none me-3">
            <i class="fa fa-bars"></i>
        </button>
        
        <div class="d-none d-sm-inline-block me-auto">
            <h5 class="mb-0 text-gray-800"><?php echo $_SESSION['business_name'] ?? 'Stockara System'; ?></h5>
        </div>

        <ul class="list-unstyled d-flex align-items-center mb-0 ms-auto">
            <li class="me-3">
                <button id="darkModeToggle" class="btn btn-sm btn-outline-secondary rounded-circle">
                    <i class="fas fa-moon"></i>
                </button>
            </li>
            <li class="dropdown">
                <a class="nav-link dropdown-toggle text-gray-600" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                    <span class="me-2 d-none d-lg-inline text-gray-600 small"><?php echo $_SESSION['full_name'] ?? 'Admin'; ?></span>
                    <img class="img-profile rounded-circle" width="30" src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['full_name'] ?? 'Admin'); ?>&background=4e73df&color=fff">
                </a>
                <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in">
                    <a class="dropdown-item" href="<?php echo BASE_URL; ?>settings/profile.php">
                        <i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>
                        Profile
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>auth/logout.php">
                        <i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>
                        Logout
                    </a>
                </div>
            </li>
        </ul>
    </nav>
    <!-- End Topbar -->

    <div class="container-fluid px-4">
        <?php if(isset($_SESSION['notice'])): ?>
            <div class="alert alert-<?php echo $_SESSION['notice']['type']; ?> alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['notice']['msg']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['notice']); ?>
        <?php endif; ?>
