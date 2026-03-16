<?php
// functions.php - Core helper functions

session_start();

// Base URL Configuration
define('BASE_URL', '/keeprecord/'); 

/**
 * Sanitize input data
 */
function clean($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Redirect with notification
 */
function redirect($url, $msg = '', $type = 'success') {
    if ($msg) {
        $_SESSION['notice'] = ['msg' => $msg, 'type' => $type];
    }
    header("Location: $url");
    exit();
}

/**
 * Check User Role
 */
function checkRole($roles) {
    if (!isLoggedIn()) {
        redirect(BASE_URL . 'auth/login.php', 'Please login first', 'danger');
    }
    
    // SaaS Subscription Check
    checkSubscription();

    if (!in_array($_SESSION['role'], (array)$roles)) {
        redirect(BASE_URL . 'dashboard.php', 'You do not have permission to access this page', 'warning');
    }
}

/**
 * Get Setting Value
 */
function getSetting($pdo, $key, $business_id) {
    // This is a placeholder for a settings table fetch
    $stmt = $pdo->prepare("SELECT * FROM businesses WHERE id = ?");
    $stmt->execute([$business_id]);
    $business = $stmt->fetch();
    return $business[$key] ?? '';
}

/**
 * Generate unique Barcode
 */
function generateSKU($pdo, $business_id) {
    $prefix = "KR" . $business_id;
    $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
    $sku = $prefix . $random;
    
    // Check if exists
    $stmt = $pdo->prepare("SELECT id FROM products WHERE sku = ?");
    $stmt->execute([$sku]);
    if ($stmt->fetch()) {
        return generateSKU($pdo, $business_id);
    }
    return $sku;
}

/**
 * Log Activity
 */
function logActivity($pdo, $business_id, $user_id, $action, $module) {
    $stmt = $pdo->prepare("INSERT INTO activity_logs (business_id, user_id, action, module) VALUES (?, ?, ?, ?)");
    $stmt->execute([$business_id, $user_id, $action, $module]);
}

/**
 * Check if business subscription is active
 */
function checkSubscription() {
    if (!isset($_SESSION['subscription_expiry'])) return true; // Just in case
    
    $today = date('Y-m-d');
    $expiry = $_SESSION['subscription_expiry'];
    
    if ($today > $expiry) {
        // Only redirect if we are not already on the billing page
        $current_page = basename($_SERVER['PHP_SELF']);
        if ($current_page != 'billing.php' && $current_page != 'logout.php') {
            $_SESSION['notice'] = ['msg' => 'Your subscription has expired. Please renew to continue.', 'type' => 'danger'];
            header("Location: " . BASE_URL . "settings/billing.php");
            exit();
        }
    }
}
?>
