<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

checkRole(['Admin']);

$business_id = $_SESSION['business_id'];

// Get logs with user names
$stmt = $pdo->prepare("SELECT al.*, u.full_name FROM activity_logs al LEFT JOIN users u ON al.user_id = u.id WHERE al.business_id = ? ORDER BY al.id DESC LIMIT 100");
$stmt->execute([$business_id]);
$logs = $stmt->fetchAll();

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Activity Logs</h1>
    <span class="text-muted small">Showing last 100 activities</span>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-white">
        <h6 class="m-0 font-weight-bold text-primary">System Audit Trail</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Time</th>
                        <th>User</th>
                        <th>Module</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($logs)): ?>
                        <tr><td colspan="4" class="text-center py-4">No activity recorded yet.</td></tr>
                    <?php else: ?>
                        <?php foreach($logs as $log): ?>
                        <tr>
                            <td><?php echo date('d M Y, H:i:s', strtotime($log['created_at'])); ?></td>
                            <td><strong><?php echo $log['full_name'] ?: 'System'; ?></strong></td>
                            <td><span class="badge bg-light text-dark border"><?php echo $log['module']; ?></span></td>
                            <td><?php echo $log['action']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
