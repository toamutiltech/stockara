<div class="sidebar d-flex flex-column pb-5" id="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-cube text-primary me-2"></i>
        <span>Stockara</span>
    </div>
    
    <div class="nav-links mt-4">
        <p class="text-uppercase text-xs font-bold text-slate-500 px-4 mb-2 small opacity-50">Main Menu</p>
        
        <a href="dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
        
        <a href="businesses.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'businesses.php' ? 'active' : ''; ?>">
            <i class="fas fa-store"></i>
            <span>Businesses</span>
        </a>
        
        <a href="plans.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'plans.php' ? 'active' : ''; ?>">
            <i class="fas fa-credit-card"></i>
            <span>SaaS Plans</span>
        </a>
        
        <a href="subscriptions.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'subscriptions.php' ? 'active' : ''; ?>">
            <i class="fas fa-receipt"></i>
            <span>Transaction Logs</span>
        </a>
        
        <p class="text-uppercase text-xs font-bold text-slate-500 px-4 mt-4 mb-2 small opacity-50">System</p>
        
        <a href="messages.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'messages.php' ? 'active' : ''; ?>">
            <i class="fas fa-envelope"></i>
            <span>Messages</span>
        </a>
        
        <a href="newsletter.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'newsletter.php' ? 'active' : ''; ?>">
            <i class="fas fa-paper-plane"></i>
            <span>Newsletter</span>
        </a>
        
        <a href="super_admins.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'super_admins.php' ? 'active' : ''; ?>">
            <i class="fas fa-user-shield"></i>
            <span>Master Admins</span>
        </a>
        
        <a href="audit_logs.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'audit_logs.php' ? 'active' : ''; ?>">
            <i class="fas fa-shield-alt"></i>
            <span>Audit Logs</span>
        </a>
        
        <a href="settings.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
            <i class="fas fa-cog"></i>
            <span>SaaS Settings</span>
        </a>
    </div>
    
    <div class="mt-auto p-3">
        <div class="user-profile bg-white bg-opacity-10 p-3 rounded-4 mb-3 d-flex align-items-center">
            <div class="h-10 w-10 rounded-full bg-primary flex items-center justify-center text-white font-bold me-3" style="width: 40px; height: 40px;">
                <?php echo strtoupper(substr($super_admin_name, 0, 1)); ?>
            </div>
            <div class="flex-1 truncate">
                <p class="mb-0 fw-bold small text-white text-truncate"><?php echo $super_admin_name; ?></p>
                <p class="mb-0 text-white text-opacity-50 text-xs text-truncate" style="font-size: 0.7rem;">Super Admin Role</p>
            </div>
        </div>
        <a href="logout.php" class="nav-link text-danger border border-danger border-opacity-25 justify-content-center">
            <i class="fas fa-sign-out-alt"></i>
            <span>Sign Out</span>
        </a>
    </div>
</div>

<div class="main-content">
    <div class="topbar">
        <button class="btn d-lg-none" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        
        <div class="search-box d-none d-md-flex">
            <div class="input-group">
                <span class="input-group-text bg-light border-0"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control bg-light border-0" placeholder="Search data...">
            </div>
        </div>
        
        <div class="right-topbar d-flex align-items-center gap-3">
            <div class="notifications text-muted">
                <i class="fas fa-bell fa-lg"></i>
            </div>
            <div class="vr mx-2"></div>
            <div class="profile-info text-end d-none d-sm-block">
                <p class="mb-0 fw-bold small"><?php echo $super_admin_name; ?></p>
                <p class="mb-0 text-muted" style="font-size: 0.75rem;">Platform Master</p>
            </div>
            <div class="h-10 w-10 rounded-circle bg-light border flex items-center justify-center font-bold text-primary" style="width: 40px; height: 40px;">
                <?php echo strtoupper(substr($super_admin_name, 0, 1)); ?>
            </div>
        </div>
    </div>
    
    <div class="p-4 p-lg-5">
