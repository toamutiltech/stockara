<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
require_once 'includes/mailer.php';

// Handle Newsletter Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['newsletter_subject'])) {
    $subject = clean($_POST['newsletter_subject']);
    $body = $_POST['newsletter_body'];
    $audience = clean($_POST['audience']);

    $emails = [];
    
    if ($audience == 'all' || $audience == 'businesses') {
        $stmt = $pdo->query("SELECT email FROM businesses WHERE email IS NOT NULL AND email != ''");
        while ($row = $stmt->fetch()) {
            $emails[] = $row['email'];
        }
    }
    
    if ($audience == 'all' || $audience == 'users') {
        $stmt = $pdo->query("SELECT email FROM users WHERE email IS NOT NULL AND email != ''");
        while ($row = $stmt->fetch()) {
            $emails[] = $row['email'];
        }
    }
    
    // Remove duplicates
    $emails = array_unique($emails);
    
    $success_count = 0;
    $fail_count = 0;
    
    foreach ($emails as $email) {
        if (send_email($email, $subject, nl2br($body))) {
            $success_count++;
        } else {
            $fail_count++;
        }
    }
    
    if ($success_count > 0) {
        redirect('newsletter.php', "Newsletter sent to $success_count recipients." . ($fail_count > 0 ? " ($fail_count failed)" : ""));
    } else {
        redirect('newsletter.php', "Failed to send newsletter. No valid recipients or SMTP error.", "danger");
    }
}
?>

<div class="row align-items-center mb-5">
    <div class="col-lg-6">
        <h2 class="fw-800 text-slate-800">Newsletter Broadcast</h2>
        <p class="text-muted">Send updates, announcements, or promotional emails to <span class="fw-bold">Stockara</span> users.</p>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-lg-8 mx-auto">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-4 px-4 border-0 border-bottom">
                <h5 class="mb-0 fw-bold"><i class="fas fa-paper-plane text-primary me-2"></i> Compose Broadcast</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Audience</label>
                        <select name="audience" class="form-select bg-light border-0 py-2 rounded-3" required>
                            <option value="all">All Registered Users & Businesses</option>
                            <option value="businesses">Business Owners Only</option>
                            <option value="users">Staff/Team Members Only</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Email Subject</label>
                        <input type="text" name="newsletter_subject" class="form-control bg-light border-0 py-2 rounded-3" placeholder="Enter subject line..." required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">Message Content</label>
                        <textarea name="newsletter_body" class="form-control bg-light border-0 py-2 rounded-3" rows="10" placeholder="Type your newsletter content here... HTML is supported if structured properly, otherwise use plain text." required></textarea>
                        <div class="form-text small text-muted mt-2"><i class="fas fa-info-circle me-1"></i> Line breaks will be automatically preserved.</div>
                    </div>
                    
                    <div class="text-end">
                        <button type="reset" class="btn btn-light rounded-pill px-4 small fw-bold me-2">Clear</button>
                        <button type="submit" class="btn btn-premium px-5 shadow-sm" onclick="return confirm('Are you sure you want to broadcast this message to the selected audience?');"><i class="fas fa-paper-plane me-2"></i> Send Broadcast</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
