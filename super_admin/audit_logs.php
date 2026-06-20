<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// Fetch Global Activity Logs
$stmt = $pdo->query("SELECT a.*, b.name as business_name, u.full_name as user_name 
                    FROM activity_logs a 
                    LEFT JOIN businesses b ON a.business_id = b.id 
                    LEFT JOIN users u ON a.user_id = u.id 
                    ORDER BY a.created_at DESC LIMIT 100");
$logs = $stmt->fetchAll();
?>

<div class="row align-items-center mb-5">
    <div class="col-lg-6">
        <h2 class="fw-800 text-slate-800">Audit & Trace</h2>
        <p class="text-muted">Monitor global system activity and user actions across all <span class="fw-bold">Stockara</span> instances.</p>
    </div>
    <div class="col-lg-6 text-lg-end">
        <a href="dashboard.php" class="btn btn-outline-secondary rounded-pill px-4 me-2 small fw-bold shadow-sm">
            <i class="fas fa-chevron-left me-2"></i> Back to Stats
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
    <div class="card-header bg-white py-4 px-4 border-0 d-flex align-items-center justify-content-between">
        <h5 class="mb-0 fw-bold">System Operations Audit</h5>
        <button class="btn btn-sm btn-light border small font-bold">Purge Old Logs <i class="fas fa-trash-alt ms-1 text-danger"></i></button>
    </div>
    <div class="table-responsive p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="px-4 border-0 text-muted small fw-bold text-uppercase">Activity Detail</th>
                    <th class="border-0 text-muted small fw-bold text-uppercase">Module</th>
                    <th class="border-0 text-muted small fw-bold text-uppercase">Authorized Person</th>
                    <th class="border-0 text-muted small fw-bold text-uppercase">Associated Business</th>
                    <th class="px-4 border-0 text-muted small fw-bold text-uppercase text-end">Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($logs as $log): ?>
                <tr>
                    <td class="px-4 border-0 py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-secondary bg-opacity-10 text-secondary rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <i class="fas fa-fingerprint small"></i>
                            </div>
                            <div>
                                <p class="mb-0 fw-bold small"><?php echo $log['action']; ?></p>
                                <p class="mb-0 text-muted text-xs small" style="font-size: 0.7rem;">Reference ID: #<?php echo $log['id']; ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="border-0">
                        <span class="badge bg-light text-dark border px-2 py-1 small rounded-3" style="font-size: 0.7rem;">
                            <?php echo $log['module']; ?>
                        </span>
                    </td>
                    <td class="border-0 small fw-bold text-primary"><?php echo $log['user_name'] ?: 'System'; ?></td>
                    <td class="border-0 small text-muted"><?php echo $log['business_name'] ?: 'Global Context'; ?></td>
                    <td class="px-4 border-0 text-end small text-muted"><?php echo date('M d, Y h:i:s A', strtotime($log['created_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($logs)): ?>
                <tr><td colspan="5" class="text-center py-5 text-muted small">No activities logged yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
