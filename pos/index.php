<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

checkRole(['Admin', 'Manager', 'Cashier']);

$business_id = $_SESSION['business_id'];

// Get Business Settings (Tax Rate)
$stmt = $pdo->prepare("SELECT tax_rate, currency FROM businesses WHERE id = ?");
$stmt->execute([$business_id]);
$settings = $stmt->fetch();
$tax_rate = (float)($settings['tax_rate'] ?? 0);
$currency = $settings['currency'] ?? '₦';

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="row">
    <!-- Left Column: POS Actions -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-9">
                        <div class="input-group">
                            <span class="input-group-text bg-primary text-white"><i class="fas fa-barcode"></i></span>
                            <input type="text" id="barcodeInput" class="form-control form-control-lg" placeholder="Scan barcode or type name..." autofocus>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-outline-primary btn-lg w-100" data-bs-toggle="modal" data-bs-target="#cameraModal">
                            <i class="fas fa-camera"></i> Camera
                        </button>
                    </div>
                </div>

                <div class="table-responsive" style="min-height: 400px;">
                    <table class="table table-hover align-middle" id="posCartTable">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th width="150">Price</th>
                                <th width="150">Qty</th>
                                <th width="150">Subtotal</th>
                                <th width="50"></th>
                            </tr>
                        </thead>
                        <tbody id="cartItems">
                            <!-- Items appear here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Summary & Checkout -->
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">Summary</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Customer</label>
                    <div class="input-group">
                        <select id="customerSelect" class="form-select">
                            <option value="0">Walk-in Customer</option>
                            <!-- AJAX Load customers -->
                        </select>
                        <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#addCustomerModal"><i class="fas fa-plus"></i></button>
                    </div>
                </div>

                <hr>

                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal:</span>
                    <span class="fw-bold" id="cartSubtotal"><?php echo $currency; ?>0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Tax (<?php echo $tax_rate; ?>%):</span>
                    <span class="fw-bold" id="cartTax"><?php echo $currency; ?>0.00</span>
                </div>
                <div class="mb-3">
                    <label class="form-label d-flex justify-content-between">
                        <span>Discount:</span>
                        <select id="discountType" class="form-select form-select-sm p-0 px-1 border-0 fw-bold text-primary" style="width: auto;">
                            <option value="fixed">Fixed (<?php echo $currency; ?>)</option>
                            <option value="percent">Percent (%)</option>
                        </select>
                    </label>
                    <input type="number" id="cartDiscount" class="form-control form-control-sm text-end" value="0">
                </div>
                
                <hr>
                
                <div class="d-flex justify-content-between mb-4 mt-2">
                    <h4 class="m-0">Total:</h4>
                    <h4 class="m-0 text-primary" id="cartGrandTotal"><?php echo $currency; ?>0.00</h4>
                </div>

                <div class="mb-3">
                    <label class="form-label">Payment Method</label>
                    <select id="paymentMethod" class="form-select form-select-lg">
                        <option value="Cash">Cash</option>
                        <option value="Card">Card</option>
                        <option value="Transfer">Transfer</option>
                    </select>
                </div>

                <button id="checkoutBtn" class="btn btn-primary btn-lg w-100 py-3 mt-2 fw-bold">
                    <i class="fas fa-check-circle me-2"></i> COMPLETE SALE
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Camera Modal -->
<div class="modal fade" id="cameraModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Scan Barcode</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="reader" style="width: 100%;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addCustomerForm">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" id="custName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" id="custPhone" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Save Customer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Scripts for POS -->
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
let cart = [];
const base_currency = "<?php echo $currency; ?>";
const tax_rate = <?php echo $tax_rate; ?>;
const BASE_URL = "<?php echo BASE_URL; ?>";

$(document).ready(function() {
    loadCustomers();

    // 1. Search Logic
    $('#barcodeInput').on('keypress', function(e) {
        if(e.which == 13) {
            let query = $(this).val();
            if(query) {
                addProductBySearch(query);
                $(this).val('');
            }
        }
    });

    // 2. Add Customer
    $('#addCustomerForm').on('submit', function(e) {
        e.preventDefault();
        let name = $('#custName').val();
        let phone = $('#custPhone').val();
        $.post(BASE_URL + 'api/add_customer.php', {name: name, phone: phone}, function(res) {
            let data = JSON.parse(res);
            if(data.success) {
                $('#addCustomerModal').modal('hide');
                loadCustomers(data.id);
                $('#addCustomerForm')[0].reset();
            } else {
                alert(data.message);
            }
        });
    });

    // 3. Checkout
    $('#checkoutBtn').on('click', function() {
        if(cart.length == 0) {
            alert("Cart is empty!");
            return;
        }
        processSale();
    });

    // 4. Load Customers
    function loadCustomers(selectedId = 0) {
        $.getJSON(BASE_URL + 'api/get_customers.php', function(data) {
            if(data.success) {
                let html = '<option value="0">Walk-in Customer</option>';
                data.customers.forEach(c => {
                    html += `<option value="${c.id}" ${c.id == selectedId ? 'selected' : ''}>${c.name} (${c.phone})</option>`;
                });
                $('#customerSelect').html(html);
            }
        });
    }

    // 5. Cart Logic
    function addProductBySearch(query) {
        $.getJSON(BASE_URL + 'api/pos_search.php', {q: query}, function(data) {
            if(data.success) {
                addToCart(data.product);
            } else {
                alert("Product not found!");
            }
        });
    }

    function addToCart(product) {
        let existing = cart.find(item => item.id == product.id);
        if(existing) {
            existing.qty++;
        } else {
            cart.push({
                id: product.id,
                name: product.name,
                price: parseFloat(product.selling_price),
                qty: 1
            });
        }
        renderCart();
    }

    function renderCart() {
        let html = '';
        let subtotal = 0;
        cart.forEach((item, index) => {
            let rowSubtotal = item.price * item.qty;
            subtotal += rowSubtotal;
            html += `
                <tr>
                    <td>${item.name}</td>
                    <td>${base_currency}${item.price.toFixed(2)}</td>
                    <td>
                        <div class="input-group input-group-sm" style="width: 100px;">
                            <button class="btn btn-outline-secondary px-2" onclick="updateQty(${index}, -1)">-</button>
                            <input type="text" class="form-control text-center p-0" value="${item.qty}" readonly>
                            <button class="btn btn-outline-secondary px-2" onclick="updateQty(${index}, 1)">+</button>
                        </div>
                    </td>
                    <td class="fw-bold">${base_currency}${rowSubtotal.toFixed(2)}</td>
                    <td><button class="btn btn-sm text-danger" onclick="removeFromCart(${index})"><i class="fas fa-trash"></i></button></td>
                </tr>
            `;
        });
        $('#cartItems').html(html);
        updateSummary(subtotal);
    }

    window.updateQty = function(index, delta) {
        cart[index].qty += delta;
        if(cart[index].qty < 1) cart[index].qty = 1;
        renderCart();
    }

    window.removeFromCart = function(index) {
        cart.splice(index, 1);
        renderCart();
    }

    function updateSummary(subtotal) {
        let discountVal = parseFloat($('#cartDiscount').val()) || 0;
        let discountType = $('#discountType').val();
        let discountAmount = 0;

        if (discountType === 'percent') {
            discountAmount = (subtotal * discountVal) / 100;
        } else {
            discountAmount = discountVal;
        }

        let taxableAmount = subtotal - discountAmount;
        let taxAmount = (taxableAmount * tax_rate) / 100;
        let grandTotal = taxableAmount + taxAmount;
        
        $('#cartSubtotal').text(base_currency + subtotal.toLocaleString(undefined, {minimumFractionDigits: 2}));
        $('#cartTax').text(base_currency + tax_amount.toLocaleString(undefined, {minimumFractionDigits: 2}));
        $('#cartGrandTotal').text(base_currency + grandTotal.toLocaleString(undefined, {minimumFractionDigits: 2}));
    }

    $('#cartDiscount, #discountType').on('input change', function() {
        renderCart();
    });

    // Sale Process
    function processSale() {
        let subtotal = cart.reduce((acc, obj) => acc + (obj.price * obj.qty), 0);
        let discountVal = parseFloat($('#cartDiscount').val()) || 0;
        let discountType = $('#discountType').val();
        let discountAmount = (discountType === 'percent') ? (subtotal * discountVal) / 100 : discountVal;

        let saleData = {
            customer_id: $('#customerSelect').val(),
            payment_method: $('#paymentMethod').val(),
            discount: discountAmount,
            tax: (subtotal - discountAmount) * tax_rate / 100,
            items: cart
        };

        $.post(BASE_URL + 'api/pos_process.php', saleData, function(response) {
            let res = JSON.parse(response);
            if(res.success) {
                window.location.href = BASE_URL + 'pos/receipt.php?id=' + res.id;
            } else {
                alert("Error: " + res.message);
            }
        });
    }

    // Camera Scanner
    const html5QrCode = new Html5Qrcode("reader");
    $('#cameraModal').on('shown.bs.modal', function () {
        html5QrCode.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: { width: 250, height: 250 } },
            (decodedText) => {
                addProductBySearch(decodedText);
                $('#cameraModal').modal('hide');
                html5QrCode.stop();
            },
            (errorMessage) => { /* ignore */ }
        );
    });
    $('#cameraModal').on('hidden.bs.modal', function () {
        html5QrCode.stop();
    });
});
</script>

<?php include '../includes/footer.php'; ?>
