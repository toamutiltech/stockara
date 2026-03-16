<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

checkRole(['Admin', 'Manager']);

$business_id = $_SESSION['business_id'];

// Get Business current settings
$stmt = $pdo->prepare("SELECT * FROM businesses WHERE id = ?");
$stmt->execute([$business_id]);
$biz = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_settings'])) {
    $name = clean($_POST['name']);
    $address = clean($_POST['address']);
    $phone = clean($_POST['phone']);
    $email = clean($_POST['email']);
    $currency = clean($_POST['currency']);
    $tax = (float)$_POST['tax_rate'];
    $footer = clean($_POST['receipt_footer']);

    try {
        $stmt = $pdo->prepare("UPDATE businesses SET name = ?, address = ?, phone = ?, email = ?, currency = ?, tax_rate = ?, receipt_footer = ? WHERE id = ?");
        $stmt->execute([$name, $address, $phone, $email, $currency, $tax, $footer, $business_id]);
        
        // Handle Logo Upload
        if (!empty($_FILES['logo']['name'])) {
            $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
            $filename = "logo_" . $business_id . "." . $ext;
            $target = "../uploads/logos/" . $filename;
            
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $target)) {
                $stmt = $pdo->prepare("UPDATE businesses SET logo = ? WHERE id = ?");
                $stmt->execute([$filename, $business_id]);
            }
        }

        logActivity($pdo, $business_id, $_SESSION['user_id'], "Updated business settings", "Settings");
        redirect(BASE_URL . 'settings/index.php', "Settings updated successfully.");
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Business Settings</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">General Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3 text-center">
                            <?php if($biz['logo']): ?>
                                <img src="<?php echo BASE_URL; ?>uploads/logos/<?php echo $biz['logo']; ?>" class="img-thumbnail mb-2" style="max-height: 100px;">
                            <?php else: ?>
                                <div class="bg-light d-inline-flex align-items-center justify-content-center p-4 rounded mb-2" style="width: 100px; height: 100px;">
                                    <i class="fas fa-image fa-2x text-muted"></i>
                                </div>
                            <?php endif; ?>
                            <div class="mb-3">
                                <label class="form-label d-block">Business Logo</label>
                                <input type="file" name="logo" class="form-control form-control-sm mx-auto" style="max-width: 300px;">
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Business Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo $biz['name']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo $biz['phone']; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo $biz['email']; ?>">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2"><?php echo $biz['address']; ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">POS & Receipt Settings</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Currency Symbol</label>
                            <input type="text" name="currency" class="form-control" value="<?php echo $biz['currency']; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tax Rate (%)</label>
                            <input type="number" step="0.01" name="tax_rate" class="form-control" value="<?php echo $biz['tax_rate']; ?>">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Receipt Footer Message</label>
                            <textarea name="receipt_footer" class="form-control" rows="2"><?php echo $biz['receipt_footer']; ?></textarea>
                        </div>
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" name="update_settings" class="btn btn-primary px-5 btn-lg">Save Settings</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">System Info</h6>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Total Users
                        <span class="badge bg-primary rounded-pill">
                            <?php 
                                $s = $pdo->prepare("SELECT COUNT(*) FROM users WHERE business_id = ?");
                                $s->execute([$business_id]);
                                echo $s->fetchColumn();
                            ?>
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Active Products
                        <span class="badge bg-success rounded-pill">
                            <?php 
                                $s = $pdo->prepare("SELECT COUNT(*) FROM products WHERE business_id = ?");
                                $s->execute([$business_id]);
                                echo $s->fetchColumn();
                            ?>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
