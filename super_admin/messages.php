<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
require_once 'includes/mailer.php';

// Handle Reply
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reply_message_id'])) {
    $id = clean($_POST['reply_message_id']);
    $email = clean($_POST['reply_email']);
    $subject = "Re: " . clean($_POST['reply_subject']);
    $reply_body = $_POST['reply_body']; 
    
    if (send_email($email, $subject, nl2br($reply_body))) {
        $stmt = $pdo->prepare("UPDATE contact_messages SET status = 'Replied' WHERE id = ?");
        $stmt->execute([$id]);
        redirect('messages.php', "Reply sent successfully!");
    } else {
        redirect('messages.php', "Failed to send reply. Please check SMTP settings.", "danger");
    }
}

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
        <p class="text-muted">Review and respond to inquiries from prospective <span class="fw-bold">Stockara</span> users.</p>
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
                                <li><a class="dropdown-item py-2 small fw-bold" href="javascript:void(0)" onclick='openReplyModal(<?php echo json_encode($msg); ?>)'><i class="fas fa-reply text-primary me-2"></i> Reply by Email</a></li>
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

<!-- Reply Modal -->
<div class="modal fade" id="replyModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 px-4 pt-4">
                <h5 class="modal-title fw-800 text-slate-800"><i class="fas fa-reply text-primary me-2"></i> Reply to Contact</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="replyForm" method="POST" class="modal-body p-4">
                <input type="hidden" name="reply_message_id" id="reply_message_id">
                <input type="hidden" name="reply_email" id="reply_email_hidden">
                <input type="hidden" name="reply_subject" id="reply_subject_hidden">
                
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small fw-bold text-muted">To</label>
                        <input type="email" id="reply_email_display" class="form-control bg-light border-0 py-2 rounded-3" disabled>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label small fw-bold text-muted">Original Subject</label>
                        <input type="text" id="reply_subject_display" class="form-control bg-light border-0 py-2 rounded-3" disabled>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label small fw-bold text-muted">Reply Message</label>
                        <textarea name="reply_body" class="form-control bg-light border-0 py-2 rounded-3" rows="6" placeholder="Type your reply here..." required></textarea>
                    </div>
                </div>
                
                <div class="mt-4 text-end">
                    <button type="button" class="btn btn-light rounded-pill px-4 small fw-bold me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-premium px-5 shadow-sm">Send Reply</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openReplyModal(msg) {
    $('#reply_message_id').val(msg.id);
    $('#reply_email_hidden').val(msg.email);
    $('#reply_email_display').val(msg.email);
    $('#reply_subject_hidden').val(msg.subject);
    $('#reply_subject_display').val("Re: " + msg.subject);
    
    $('#replyModal').modal('show');
}
</script>

<?php require_once 'includes/footer.php'; ?>
