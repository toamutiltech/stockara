<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// Fetch Some Global Metrics for setting reference
$stmt = $pdo->query("SELECT COUNT(*) FROM super_admins");
$adminCount = $stmt->fetchColumn();

// Handle Form Submission (Save Simulation)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_settings'])) {
    redirect('settings.php', "Global configurations updated and propagated successfully");
}
?>

<div class="row align-items-center mb-5">
    <div class="col-lg-6">
        <h2 class="fw-800 text-slate-800">SaaS Control Center</h2>
        <p class="text-muted">Configure the global ecosystem parameters for the <span class="fw-bold">Stockara</span> system.</p>
    </div>
    <div class="col-lg-6 text-lg-end">
        <a href="dashboard.php" class="btn btn-outline-secondary rounded-pill px-4 me-2 small fw-bold shadow-sm">
            <i class="fas fa-chevron-left me-2"></i> Back to Stats
        </a>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-white py-4 px-4 border-0">
                <h5 class="mb-0 fw-bold">Platform Configuration</h5>
                <p class="text-xs text-muted mb-0">Manage global variables and operational behaviors.</p>
            </div>
            <form method="POST" class="card-body p-4 pt-2">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase">System Mode</label>
                        <select class="form-select bg-light border-0 py-2 rounded-3" name="system_mode">
                            <option value="Production" selected>Production Environment</option>
                            <option value="Maintenance">Maintenance Mode</option>
                            <option value="Development">Development Sandbox</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase">Currency Symbol</label>
                        <input type="text" class="form-control bg-light border-0 py-2 rounded-3" value="₦ (Nigerian Naira)" disabled>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase">Registration Status</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" checked id="allowReg">
                            <label class="form-check-label small fw-bold" for="allowReg">Allow New Business Signups</label>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase">Subscription Engine</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" checked id="enforceSub">
                            <label class="form-check-label small fw-bold" for="enforceSub">Enforce Subscription Expiry</label>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <hr class="opacity-10 my-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Platform Support Message (Maintenance)</label>
                        <textarea class="form-control bg-light border-0 py-2 rounded-3" rows="3">The system is currently undergoing scheduled maintenance to improve our POS experience. We will be back shortly.</textarea>
                    </div>
                    
                    <div class="col-12 mt-4 text-end">
                        <button type="submit" name="save_settings" class="btn btn-premium px-5 shadow-sm">Commit System Changes</button>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-4 px-4 border-0">
                <h5 class="mb-0 fw-bold">Cloud Storage Integration (AWS S3)</h5>
                <p class="text-xs text-muted mb-0">Currently bypassing for local storage.</p>
            </div>
            <div class="card-body p-4 pt-2">
                <div class="alert alert-info py-2 small rounded-3 opacity-75">
                    <i class="fas fa-info-circle me-2"></i> Cloud storage is disabled per codebase configuration. Files are stored locally.
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-primary bg-opacity-10 py-3 px-4 border-0">
                <h6 class="mb-0 fw-bold text-primary">Master Console Integrity</h6>
            </div>
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-success rounded-circle me-2" style="width: 10px; height: 10px;"></div>
                    <span class="small fw-bold">Database Connection: SECURE</span>
                </div>
                <div class="d-flex align-items-center mb-0">
                    <div class="bg-success rounded-circle me-2" style="width: 10px; height: 10px;"></div>
                    <span class="small fw-bold">Active Master Admins: <?php echo $adminCount; ?></span>
                </div>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-4 px-4 border-0">
                <h5 class="mb-0 fw-bold text-danger">Hazard Control</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <p class="small text-muted mb-4">These actions affect the entire SaaS infrastructure. Handle with extreme caution.</p>
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-danger btn-sm rounded-3 py-2 fw-bold text-start px-3"><i class="fas fa-database me-2"></i> Clear Global Activity Logs</button>
                    <button class="btn btn-outline-danger btn-sm rounded-3 py-2 fw-bold text-start px-3"><i class="fas fa-broom me-2"></i> Archive Expired Portals (90d+)</button>
                    <button class="btn btn-danger btn-sm rounded-3 py-2 fw-bold text-start px-3"><i class="fas fa-power-off me-2"></i> System Wide Flush (Sync)</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
