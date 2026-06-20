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

<style>
    .pos-search-container {
        position: relative;
    }
    #searchResults {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 1050;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        max-height: 400px;
        overflow-y: auto;
        display: none;
    }
    .search-item {
        padding: 12px 15px;
        cursor: pointer;
        border-bottom: 1px solid #f1f1f1;
        transition: background 0.2s;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .search-item:hover {
        background-color: #f8f9fc;
    }
    .search-item .item-details {
        flex: 1;
    }
    .search-item .item-name {
        font-weight: 600;
        display: block;
        color: #333;
    }
    .search-item .item-meta {
        font-size: 0.85rem;
        color: #888;
    }
    .search-item .item-price {
        font-weight: 700;
        color: var(--primary-color);
    }
    .search-item .item-stock {
        font-size: 0.75rem;
        padding: 2px 6px;
        border-radius: 4px;
        margin-left: 10px;
    }
    .cart-qty-input {
        width: 60px !important;
        text-align: center;
        border-radius: 0;
    }
    .shadow-sm-hover:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
</style>

<div class="row g-4">
    <!-- Left Column: POS Actions -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-md-9">
                        <div class="pos-search-container">
                            <div class="input-group">
                                <span class="input-group-text bg-primary text-white border-0"><i class="fas fa-search"></i></span>
                                <input type="text" id="barcodeInput" class="form-control form-control-lg border-0 bg-light" placeholder="Search product name or scan barcode..." autofocus autocomplete="off">
                            </div>
                            <div id="searchResults" class="border"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary btn-lg w-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#cameraModal">
                            <i class="fas fa-camera me-1"></i> Scan
                        </button>
                    </div>
                </div>

                <div class="table-responsive" style="min-height: 450px;">
                    <table class="table table-hover align-middle border-0" id="posCartTable">
                        <thead class="table-light border-0">
                            <tr>
                                <th class="border-0">Product</th>
                                <th width="150" class="border-0">Price</th>
                                <th width="180" class="border-0 text-center">Qty</th>
                                <th width="150" class="border-0">Subtotal</th>
                                <th width="50" class="border-0"></th>
                            </tr>
                        </thead>
                        <tbody id="cartItems">
                            <!-- Items appear here -->
                        </tbody>
                    </table>
                    <div id="emptyCartMsg" class="text-center py-5 text-muted">
                        <i class="fas fa-shopping-cart fa-3x mb-3 opacity-50"></i>
                        <p>Your cart is empty. Start by searching for a product.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Summary & Checkout -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold mb-0">Sale Summary</h5>
            </div>
            <div class="card-body p-4">
                <div class="mb-4">
                    <label class="form-label small fw-bold text-uppercase text-muted">Customer Selection</label>
                    <div class="input-group">
                        <select id="customerSelect" class="form-select border-0 bg-light">
                            <option value="0">Walk-in Customer</option>
                            <!-- AJAX Load customers -->
                        </select>
                        <button class="btn btn-light border-0" data-bs-toggle="modal" data-bs-target="#addCustomerModal"><i class="fas fa-plus"></i></button>
                    </div>
                </div>

                <div class="summary-details py-3">
                    <div class="d-flex justify-content-between mb-3 text-muted">
                        <span>Subtotal:</span>
                        <span class="fw-bold" id="cartSubtotal"><?php echo $currency; ?>0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 text-muted">
                        <span>Tax (<?php echo $tax_rate; ?>%):</span>
                        <span class="fw-bold" id="cartTax"><?php echo $currency; ?>0.00</span>
                    </div>
                    
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label small fw-bold text-uppercase text-muted mb-0">Discount</label>
                            <select id="discountType" class="form-select form-select-sm p-0 px-2 border-0 fw-bold text-primary bg-transparent" style="width: auto;">
                                <option value="fixed">Fixed (<?php echo $currency; ?>)</option>
                                <option value="percent">Percent (%)</option>
                            </select>
                        </div>
                        <input type="number" id="cartDiscount" class="form-control border-0 bg-light text-end" value="0">
                    </div>
                    
                    <hr class="opacity-10">
                    
                    <div class="d-flex justify-content-between mb-4 mt-2 py-2">
                        <h4 class="fw-bold m-0">Grand Total:</h4>
                        <h4 class="fw-bold m-0 text-primary" id="cartGrandTotal"><?php echo $currency; ?>0.00</h4>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-uppercase text-muted">Payment Method</label>
                    <div class="row g-2">
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="paymentMethod" id="payCash" value="Cash" checked>
                            <label class="btn btn-outline-primary w-100 py-3" for="payCash">
                                <i class="fas fa-money-bill-wave d-block mb-1"></i> Cash
                            </label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="paymentMethod" id="payCard" value="Card">
                            <label class="btn btn-outline-primary w-100 py-3" for="payCard">
                                <i class="fas fa-credit-card d-block mb-1"></i> Card
                            </label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="paymentMethod" id="payTransfer" value="Transfer">
                            <label class="btn btn-outline-primary w-100 py-3" for="payTransfer">
                                <i class="fas fa-exchange-alt d-block mb-1"></i> Trf
                            </label>
                        </div>
                    </div>
                </div>

                <button id="checkoutBtn" class="btn btn-primary btn-lg w-100 py-3 shadow-sm fw-bold">
                    <i class="fas fa-check-circle me-2"></i> COMPLETE SALE
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Camera Modal -->
<div class="modal fade" id="cameraModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Scan Barcode</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="reader" style="width: 100%;" class="rounded overflow-hidden"></div>
                <p class="text-center text-muted small mt-3 mb-0">Hold the barcode in front of the camera.</p>
            </div>
        </div>
    </div>
</div>

<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">New Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="addCustomerForm">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Name</label>
                        <input type="text" id="custName" class="form-control border-0 bg-light" required placeholder="Full Name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Phone</label>
                        <input type="text" id="custPhone" class="form-control border-0 bg-light" placeholder="Phone Number">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2">Save Customer</button>
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

    // 1. Search & Suggestions Logic
    let searchTimeout;
    $('#barcodeInput').on('input', function() {
        let query = $(this).val();
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            $('#searchResults').hide();
            return;
        }

        searchTimeout = setTimeout(function() {
            $.getJSON(BASE_URL + 'api/get_products.php', {search: query}, function(data) {
                if (data.success && data.products.length > 0) {
                    let html = '';
                    data.products.forEach(p => {
                        html += `
                            <div class="search-item" onclick='addToCart(${JSON.stringify(p).replace(/'/g, "&apos;")}); $("#searchResults").hide(); $("#barcodeInput").val("");'>
                                <div class="item-details">
                                    <span class="item-name">${p.name}</span>
                                    <span class="item-meta">${p.barcode ? '<i class="fas fa-barcode"></i> ' + p.barcode : 'SKU: ' + p.sku}</span>
                                    <span class="item-stock bg-light text-muted">${p.quantity} in stock</span>
                                </div>
                                <div class="item-price">${base_currency}${parseFloat(p.selling_price).toFixed(2)}</div>
                            </div>
                        `;
                    });
                    $('#searchResults').html(html).show();
                } else {
                    $('#searchResults').hide();
                }
            });
        }, 300);
    });

    // Close search results on outside click
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.pos-search-container').length) {
            $('#searchResults').hide();
        }
    });

    // Exact Match Add (for Barcode Scanners)
    $('#barcodeInput').on('keypress', function(e) {
        if(e.which == 13) {
            let query = $(this).val();
            if(query) {
                addProductByBarcode(query);
                $(this).val('');
                $('#searchResults').hide();
            }
        }
    });

    // 2. Add Customer
    $('#addCustomerForm').on('submit', function(e) {
        e.preventDefault();
        let name = $('#custName').val();
        let phone = $('#custPhone').val();
        $.post(BASE_URL + 'api/add_customer.php', {name: name, phone: phone}, function(res) {
            let data = typeof res === 'string' ? JSON.parse(res) : res;
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
    function addProductByBarcode(barcode) {
        $.getJSON(BASE_URL + 'api/pos_search.php', {q: barcode}, function(data) {
            if(data.success) {
                addToCart(data.product);
            } else {
                // If barcode not found, don't alert yet, let the live search work its magic
            }
        });
    }

    window.addToCart = function(product) {
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
        
        if (cart.length === 0) {
            $('#emptyCartMsg').show();
        } else {
            $('#emptyCartMsg').hide();
        }

        cart.forEach((item, index) => {
            let rowSubtotal = item.price * item.qty;
            subtotal += rowSubtotal;
            html += `
                <tr>
                    <td>
                        <div class="fw-bold">${item.name}</div>
                        <div class="small text-muted">${base_currency}${item.price.toFixed(2)} / unit</div>
                    </td>
                    <td class="text-muted">${base_currency}${item.price.toFixed(2)}</td>
                    <td>
                        <div class="d-flex justify-content-center align-items-center">
                            <button class="btn btn-sm btn-light border-0 rounded-circle" onclick="updateQty(${index}, -1)" style="width: 30px; height: 30px;"><i class="fas fa-minus small"></i></button>
                            <input type="text" class="form-control cart-qty-input border-0 bg-transparent fw-bold mx-2" value="${item.qty}" readonly>
                            <button class="btn btn-sm btn-light border-0 rounded-circle" onclick="updateQty(${index}, 1)" style="width: 30px; height: 30px;"><i class="fas fa-plus small"></i></button>
                        </div>
                    </td>
                    <td class="fw-bold text-dark">${base_currency}${rowSubtotal.toFixed(2)}</td>
                    <td class="text-end"><button class="btn btn-sm text-danger opacity-75 hover-opacity-100" onclick="removeFromCart(${index})"><i class="fas fa-trash-alt"></i></button></td>
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
        $('#cartTax').text(base_currency + taxAmount.toLocaleString(undefined, {minimumFractionDigits: 2}));
        $('#cartGrandTotal').text(base_currency + grandTotal.toLocaleString(undefined, {minimumFractionDigits: 2}));
    }

    $('#cartDiscount, #discountType').on('input change', function() {
        let subtotal = cart.reduce((acc, obj) => acc + (obj.price * obj.qty), 0);
        updateSummary(subtotal);
    });

    // Sale Process
    function processSale() {
        let subtotal = cart.reduce((acc, obj) => acc + (obj.price * obj.qty), 0);
        let discountVal = parseFloat($('#cartDiscount').val()) || 0;
        let discountType = $('#discountType').val();
        let discountAmount = (discountType === 'percent') ? (subtotal * discountVal) / 100 : discountVal;
        
        let payment_method = $('input[name="paymentMethod"]:checked').val();

        let saleData = {
            customer_id: $('#customerSelect').val(),
            payment_method: payment_method,
            discount: discountAmount,
            tax: (subtotal - discountAmount) * tax_rate / 100,
            items: cart
        };

        $.post(BASE_URL + 'api/pos_process.php', saleData, function(response) {
            let res = typeof response === 'string' ? JSON.parse(response) : response;
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
                addProductByBarcode(decodedText);
                $('#cameraModal').modal('hide');
                html5QrCode.stop();
            },
            (errorMessage) => { /* ignore */ }
        );
    });
    $('#cameraModal').on('hidden.bs.modal', function () {
        html5QrCode.stop().catch(err => console.log(err));
    });
});
</script>

<?php include '../includes/footer.php'; ?>
