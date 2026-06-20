<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// Handle Actions (Mark Read, Delete)
if (isset($_GET['action'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];
    
    if ($action == 'read') {
        $stmt = $pdo->prepare("UPDATE contact_messages SET status = 'Read' WHERE id = ?");
        $stmt->execute([$id]);
        redirect('messages.php', "Message marked as read");
    } elseif ($action == 'delete') {
        $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
        $stmt->execute([$id]);
        redirect('messages.php', "Message deleted permanently");
    }
}

// Fetch All Messages
$stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
$messages = $stmt->fetchAll();
?>

<div class="row align-items-center mb-5">
    <div class="col-lg-6">
        <h2 class="fw-800 text-slate-800">Support Terminal</h2>
        <p class="text-muted">Review and respond to inquiries from prospective <span class="fw-bold">KeepRecord</span> users.</p>
    </div>
    <div class="col-lg-6 text-lg-end">
        <a href="dashboard.php" class="btn btn-outline-secondary rounded-pill px-4 me-2 small fw-bold shadow-sm">
            <i class="fas fa-chevron-left me-2"></i> Back to Stats
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
    <div class="card-header bg-white py-4 px-4 border-0">
        <h5 class="mb-0 fw-bold">Inbound Inquiries</h5>
    </div>
    <div class="table-responsive p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="px-4 border-0 text-muted small fw-bold text-uppercase">Sender</th>
                    <th class="border-0 text-muted small fw-bold text-uppercase">Subject / Preview</th>
                    <th class="border-0 text-muted small fw-bold text-uppercase">Status</th>
                    <th class="border-0 text-muted small fw-bold text-uppercase">Date Received</th>
                    <th class="px-4 border-0 text-muted small fw-bold text-uppercase text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($messages as $msg): ?>
                <tr class="<?php echo $msg['status'] == 'Unread' ? 'bg-primary bg-opacity-10' : ''; ?>">
                    <td class="px-4 border-0 py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <i class="fas fa-user small"></i>
                            </div>
                            <div>
                                <p class="mb-0 fw-bold small"><?php echo $msg['full_name']; ?></p>
                                <p class="mb-0 text-muted text-xs small" style="font-size: 0.75rem;"><?php echo $msg['email']; ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="border-0">
                        <p class="mb-0 fw-bold small"><?php echo $msg['subject']; ?></p>
                        <p class="mb-0 text-muted small text-truncate" style="max-width: 300px;"><?php echo $msg['message']; ?></p>
                    </td>
                    <td class="border-0">
                        <span class="badge badge-premium <?php 
                            echo $msg['status'] == 'Unread' ? 'bg-danger bg-opacity-10 text-danger' : ($msg['status'] == 'Read' ? 'bg-info bg-opacity-10 text-info' : 'bg-success bg-opacity-10 text-success'); 
                        ?>">
                            <?php echo $msg['status']; ?>
                        </span>
                    </td>
                    <td class="border-0 small text-muted"><?php echo date('M d, Y h:i A', strtotime($msg['created_at'])); ?></td>
                    <td class="px-4 border-0 text-end">
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm rounded-circle border shadow-sm" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4 mt-2">
                                <li><a class="dropdown-item py-2 small fw-bold" href="mailto:<?php echo $msg['email']; ?>"><i class="fas fa-reply text-primary me-2"></i> Reply by Email</a></li>
                                <?php if($msg['status'] == 'Unread'): ?>
                                <li><a class="dropdown-item py-2 small fw-bold" href="messages.php?id=<?php echo $msg['id']; ?>&action=read"><i class="fas fa-check-circle text-info me-2"></i> Mark as Read</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider opacity-50"></li>
                                <li><a class="dropdown-item py-2 small fw-bold text-danger" href="messages.php?id=<?php echo $msg['id']; ?>&action=delete" onclick="return confirm('Delete this inquiry permanently?')"><i class="fas fa-trash me-2"></i> Delete Message</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($messages)): ?>
                <tr><td colspan="5" class="text-center py-5 text-muted small">No inquiries received yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
