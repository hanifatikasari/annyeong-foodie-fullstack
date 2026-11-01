<?= $this->extend('themes/indomarket/layout') ?>
<?= $this->section('content') ?>

<?php
$cart              = $cart ?? [];
$selectedCart      = $selectedCart ?? [];
$selectedTotal     = $selectedTotal ?? 0;
$selectedCount     = $selectedCount ?? 0;
$selectedItemCount = $selectedItemCount ?? 0;
$isAllSelected     = $isAllSelected ?? false;

// Pisahkan produk yang tersedia dan habis stok
$availableCount = 0;
foreach ($cart as $it) {
    if (($it['stok'] ?? 0) > 0) {
        $availableCount++;
    }
}
?>

<section class="cart-section py-4 py-md-5">
    <div class="container">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb bg-transparent p-0 small">
                <li class="breadcrumb-item"><a href="<?= site_url() ?>" class="text-muted">Home</a></li>
                <li class="breadcrumb-item active text-dark font-weight-bold" aria-current="page">Keranjang Belanja</li>
            </ol>
        </nav>

        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm mb-3" role="alert">
                <i class="fa fa-check-circle mr-2"></i><?= session()->getFlashdata('success') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-3" role="alert">
                <i class="fa fa-exclamation-circle mr-2"></i><?= session()->getFlashdata('error') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('info')): ?>
            <div class="alert alert-info alert-dismissible fade show shadow-sm mb-3" role="alert">
                <i class="fa fa-info-circle mr-2"></i><?= session()->getFlashdata('info') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap">
            <h3 class="font-weight-bold mb-0 cart-title">
                <i class="fa fa-shopping-cart mr-2 text-primary"></i>Keranjang Belanja
            </h3>
            <?php if (!empty($cart)): ?>
                <span class="text-muted small mt-1 mt-sm-0">
                    Total <strong id="headerTotalItems"><?= count($cart) ?></strong> produk dalam keranjang
                </span>
            <?php endif; ?>
        </div>

        <?php if (!empty($cart)): ?>
            <!-- Form Checkout Shopee Style -->
            <form action="<?= site_url('cart/checkout') ?>" method="post" id="cartCheckoutForm">
                <?= csrf_field() ?>

                <div class="row">
                    <!-- Cart Items Column -->
                    <div class="col-lg-8 mb-4">

                        <!-- SHOPEE STYLE: Card Header "Pilih Semua" Bar -->
                        <div class="card shadow-sm border-0 mb-3 select-all-bar">
                            <div class="card-body py-2 px-3 d-flex align-items-center justify-content-between flex-wrap">
                                <div class="custom-control custom-checkbox my-1">
                                    <input type="checkbox"
                                           class="custom-control-input"
                                           id="selectAllHeader"
                                           <?= ($isAllSelected && $availableCount > 0) ? 'checked' : '' ?>
                                           <?= ($availableCount === 0) ? 'disabled' : '' ?>>
                                    <label class="custom-control-label font-weight-bold text-dark cursor-pointer user-select-none" for="selectAllHeader">
                                        Pilih Semua (<span id="selectAllHeaderCount"><?= $availableCount ?></span>)
                                    </label>
                                </div>

                                <div class="d-flex align-items-center my-1">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger btn-remove-selected py-1 px-2"
                                            id="btnRemoveSelectedTop"
                                            title="Hapus produk yang dicentang">
                                        <i class="fa fa-trash-alt mr-1"></i>Hapus Pilihan
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Card List Items -->
                        <div class="card shadow-sm border-0" id="cartItemsCard">
                            <div class="card-body p-0">
                                <div class="cart-items-wrapper">
                                    <?php foreach ($cart as $productId => $item): ?>
                                        <?php
                                        $isOutOfStock = ($item['stok'] <= 0);
                                        $isSelected   = !empty($item['selected']) && !$isOutOfStock;
                                        $subtotal     = $item['price'] * $item['qty'];
                                        ?>
                                        <div class="cart-item-row p-3 border-bottom <?= $isSelected ? 'item-selected' : '' ?> <?= $isOutOfStock ? 'item-out-of-stock' : '' ?>"
                                             id="cartRow-<?= $item['product_id'] ?>"
                                             data-product-id="<?= $item['product_id'] ?>"
                                             data-price="<?= $item['price'] ?>"
                                             data-stok="<?= $item['stok'] ?>"
                                             data-qty="<?= $item['qty'] ?>">

                                            <div class="d-flex align-items-start">
                                                <!-- Checkbox -->
                                                <div class="cart-item-checkbox-wrap mr-3 pt-2">
                                                    <div class="custom-control custom-checkbox custom-checkbox-lg">
                                                        <input type="checkbox"
                                                               class="custom-control-input item-checkbox"
                                                               id="itemChk-<?= $item['product_id'] ?>"
                                                               name="selected_items[]"
                                                               value="<?= $item['product_id'] ?>"
                                                               <?= $isSelected ? 'checked' : '' ?>
                                                               <?= $isOutOfStock ? 'disabled' : '' ?>>
                                                        <label class="custom-control-label cursor-pointer" for="itemChk-<?= $item['product_id'] ?>">
                                                            <span class="sr-only">Pilih <?= esc($item['name']) ?></span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <!-- Product Thumbnail -->
                                                <div class="cart-item-thumb mr-3 flex-shrink-0">
                                                    <?php if (!empty($item['image'])): ?>
                                                        <img src="<?= base_url($item['image']) ?>"
                                                             alt="<?= esc($item['name']) ?>"
                                                             class="rounded border img-fluid cart-thumb-img"
                                                             loading="lazy">
                                                    <?php else: ?>
                                                        <div class="rounded border bg-light d-flex align-items-center justify-content-center cart-thumb-placeholder">
                                                            <i class="fa fa-bread-slice text-muted"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>

                                                <!-- Product Details & Controls -->
                                                <div class="cart-item-content flex-grow-1 min-w-0">
                                                    <div class="d-flex justify-content-between align-items-start flex-wrap">
                                                        <div class="cart-item-info pr-2 mb-2">
                                                            <h6 class="font-weight-bold mb-1 text-dark text-break cart-item-title">
                                                                <?= esc($item['name']) ?>
                                                            </h6>
                                                            <div class="cart-item-meta text-muted small mb-1">
                                                                <span class="mr-2">SKU: <?= esc($item['sku']) ?></span>
                                                                <?php if ($isOutOfStock): ?>
                                                                    <span class="badge badge-danger">Stok Habis</span>
                                                                <?php elseif ($item['stok'] <= 5): ?>
                                                                    <span class="badge badge-warning text-dark">Sisa <?= $item['stok'] ?> pcs</span>
                                                                <?php else: ?>
                                                                    <span class="badge badge-light border">Stok: <?= $item['stok'] ?></span>
                                                                <?php endif; ?>
                                                            </div>
                                                            
                                                            <div class="cart-item-price text-muted small">
                                                                Harga:
                                                                <span
                                                                    class="font-weight-bold text-dark item-price"
                                                                    id="price-<?= $item['product_id'] ?>"
                                                                >
                                                                    Rp <?= number_format($item['price'], 0, ',', '.') ?>
                                                                </span>
                                                            </div>
                                                        </div>

                                                        <!-- Delete Action -->
                                                        <div class="cart-item-action ml-auto">
                                                            <button type="button"
                                                                    class="btn btn-link text-danger p-1 btn-remove-item"
                                                                    data-product-id="<?= $item['product_id'] ?>"
                                                                    title="Hapus item ini">
                                                                <i class="far fa-trash-alt fa-lg"></i>
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <!-- Bottom Row: Subtotal & Stepper Controls -->
                                                    <div class="d-flex align-items-center justify-content-between flex-wrap pt-2 mt-1 border-top border-light">
                                                        <!-- Subtotal -->
                                                        <div class="cart-item-subtotal my-1">
                                                            <span class="text-muted small d-block d-sm-inline mr-1">Total:</span>
                                                            <span class="font-weight-bold text-primary h6 mb-0 item-subtotal"
                                                                  id="subtotal-<?= $item['product_id'] ?>">
                                                                Rp <?= number_format($subtotal, 0, ',', '.') ?>
                                                            </span>
                                                        </div>

                                                        <!-- Quantity Stepper -->
                                                        <div class="cart-item-qty my-1">
                                                            <div class="qty-control d-inline-flex align-items-center border rounded bg-white"
                                                                 style="overflow:hidden;">
                                                                <button type="button"
                                                                        class="btn-qty-control btn-qty-minus"
                                                                        data-product-id="<?= $item['product_id'] ?>"
                                                                        title="Kurangi kuantitas"
                                                                        <?= ($item['qty'] <= 1 || $isOutOfStock) ? 'disabled' : '' ?>>
                                                                    <i class="fa fa-minus"></i>
                                                                </button>
                                                                <span class="qty-display px-2 text-center"
                                                                      id="qty-<?= $item['product_id'] ?>">
                                                                    <?= $item['qty'] ?>
                                                                </span>
                                                                <button type="button"
                                                                        class="btn-qty-control btn-qty-plus"
                                                                        data-product-id="<?= $item['product_id'] ?>"
                                                                        title="Tambah kuantitas"
                                                                        <?= ($item['qty'] >= $item['stok'] || $isOutOfStock) ? 'disabled' : '' ?>>
                                                                    <i class="fa fa-plus"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Card Footer: Navigation & Clear Cart -->
                            <div class="card-footer bg-white py-3 d-flex justify-content-between align-items-center flex-wrap">
                                <a href="<?= site_url('products') ?>" class="btn btn-outline-secondary btn-sm my-1">
                                    <i class="fa fa-arrow-left mr-1"></i> Lanjut Belanja
                                </a>

                                <div class="d-flex align-items-center my-1">
                                    <button type="button"
                                            class="btn btn-outline-danger btn-sm btn-remove-selected mr-2"
                                            id="btnRemoveSelectedBottom">
                                        <i class="fa fa-trash-alt mr-1"></i> Hapus Pilihan
                                    </button>
                                    <a href="<?= site_url('cart/clear') ?>"
                                       class="btn btn-outline-secondary btn-sm"
                                       onclick="return confirm('Kosongkan seluruh produk dari keranjang belanja?')">
                                        Kosongkan Semua
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Order Summary Column (Desktop Sidebar) -->
                    <div class="col-lg-4">
                        <div class="card shadow-sm border-0 sticky-top cart-summary-card" style="top:24px; z-index:100;">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="mb-0 font-weight-bold text-dark">
                                    <i class="fa fa-receipt mr-2 text-primary"></i>Ringkasan Belanja
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2 small text-muted">
                                    <span>Total Produk Dipilih</span>
                                    <span>
                                        <strong id="summarySelectedCount" class="text-dark"><?= $selectedItemCount ?></strong> produk
                                        (<span id="summarySelectedQty"><?= $selectedCount ?></span> pcs)
                                    </span>
                                </div>

                                <!-- <div class="alert alert-light border small py-2 px-3 mb-3 text-muted">
                                    <i class="fa fa-info-circle text-primary mr-1"></i>
                                    Centang produk di sebelah kiri untuk menghitung dan memproses checkout.
                                </div> -->

                                <hr class="my-3">

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <span class="d-block text-muted small font-weight-bold">Total Harga</span>
                                        <small class="text-muted">(belum termasuk ongkir)</small>
                                    </div>
                                    <strong class="text-primary h4 mb-0 font-weight-bold" id="summaryTotal">
                                        Rp <?= number_format($selectedTotal, 0, ',', '.') ?>
                                    </strong>
                                </div>

                                <button type="submit"
                                        class="btn btn-primary btn-block btn-lg shadow-sm font-weight-bold py-3 btn-checkout-action"
                                        id="checkoutSubmitBtn"
                                        <?= ($selectedItemCount <= 0) ? 'disabled' : '' ?>>
                                    <i class="fa fa-shopping-bag mr-2"></i>Checkout (<span class="badge-checkout-count" id="btnCheckoutCount"><?= $selectedItemCount ?></span>)
                                </button>

                                <div class="text-center mt-3 small text-muted">
                                    <i class="fa fa-shield-alt text-success mr-1"></i>
                                    Jaminan Transaksi Aman & Terpercaya
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============================================================ -->
                <!-- SHOPEE STYLE: Sticky Bottom Bar (Khusus Mobile < 992px)      -->
                <!-- ============================================================ -->
                <div class="cart-sticky-mobile d-lg-none shadow">
                    <div class="container-fluid px-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <!-- Left: Select All Checkbox -->
                            <div class="d-flex align-items-center pl-1">
                                <div class="custom-control custom-checkbox custom-checkbox-lg">
                                    <input type="checkbox"
                                           class="custom-control-input"
                                           id="selectAllMobile"
                                           <?= ($isAllSelected && $availableCount > 0) ? 'checked' : '' ?>
                                           <?= ($availableCount === 0) ? 'disabled' : '' ?>>
                                    <label class="custom-control-label font-weight-bold text-dark small cursor-pointer user-select-none mb-0" for="selectAllMobile">
                                        Semua
                                    </label>
                                </div>
                            </div>

                            <!-- Right: Total & Checkout Button -->
                            <div class="d-flex align-items-center">
                                <div class="text-right mr-2 pr-1">
                                    <div class="text-muted text-xs line-height-1" style="font-size:11px;">Total:</div>
                                    <div class="font-weight-bold text-primary line-height-1" style="font-size:15px;" id="mobileSummaryTotal">
                                        Rp <?= number_format($selectedTotal, 0, ',', '.') ?>
                                    </div>
                                </div>
                                <button type="submit"
                                        class="btn btn-primary font-weight-bold btn-checkout-action px-3 py-2 btn-mobile-checkout"
                                        id="mobileCheckoutSubmitBtn"
                                        <?= ($selectedItemCount <= 0) ? 'disabled' : '' ?>>
                                    Checkout (<span id="btnCheckoutMobileCount"><?= $selectedItemCount ?></span>)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </form>

        <?php else: ?>
            <!-- Keranjang Kosong -->
            <div class="card shadow-sm border-0 py-5 my-4 text-center">
                <div class="card-body">
                    <div class="mb-4">
                        <div class="empty-cart-icon mx-auto rounded-circle d-flex align-items-center justify-content-center bg-light text-muted"
                             style="width:100px; height:100px; font-size:40px;">
                            <i class="fa fa-shopping-basket"></i>
                        </div>
                    </div>
                    <h4 class="font-weight-bold text-dark">Keranjang Belanja Anda Kosong</h4>
                    <p class="text-muted max-w-md mx-auto">
                        Belum ada roti atau kue lezat di keranjang Anda. Yuk, temukan roti artisan dan pastry favorit Anda sekarang!
                    </p>
                    <a href="<?= site_url('products') ?>" class="btn btn-primary btn-lg px-4 mt-2 shadow-sm font-weight-bold">
                        <i class="fa fa-store mr-2"></i>Mulai Belanja
                    </a>
                </div>
            </div>
        <?php endif; ?>

    </div>
</section>

<!-- ============================================================ -->
<!-- STYLES & RESPONSIVE TWEAKS (All dimensions friendly / 360px)  -->
<!-- ============================================================ -->
<style>
/* Custom Checkbox Hit Target */
.custom-checkbox-lg .custom-control-label::before,
.custom-checkbox-lg .custom-control-label::after {
    top: 0.15rem;
    width: 1.25rem;
    height: 1.25rem;
    border-radius: 4px;
}
.custom-checkbox-lg .custom-control-input:checked ~ .custom-control-label::before {
    background-color: #5e72e4;
    border-color: #5e72e4;
}

/* Row states */
.cart-item-row {
    transition: background-color 0.2s ease, transform 0.15s ease;
}
.cart-item-row.item-selected {
    background-color: #fbfcfe;
}
.cart-item-row.item-out-of-stock {
    opacity: 0.65;
    background-color: #fdfdfd;
}

/* Thumbnails */
.cart-thumb-img,
.cart-thumb-placeholder {
    width: 72px;
    height: 72px;
    object-fit: cover;
}

/* Quantity Control Stepper */
.qty-control {
    user-select: none;
    border-color: #e2e8f0 !important;
}
.btn-qty-control {
    border: none;
    background: transparent;
    padding: 6px 12px;
    font-size: 13px;
    cursor: pointer;
    color: #495057;
    transition: background 0.15s, color 0.15s;
    outline: none !important;
}
.btn-qty-control:hover:not(:disabled) {
    background: #f1f3f9;
    color: #5e72e4;
}
.btn-qty-control:disabled {
    cursor: not-allowed;
    opacity: 0.35;
}
.qty-display {
    min-width: 34px;
    font-weight: 600;
    font-size: 14px;
    color: #212529;
}

/* Sticky Bottom Bar Mobile */
.cart-sticky-mobile {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 1030;
    background: #ffffff;
    border-top: 1px solid #e9ecef;
    padding: 10px 8px;
    box-shadow: 0 -3px 15px rgba(0, 0, 0, 0.08);
}
.btn-mobile-checkout {
    font-size: 14px;
    border-radius: 6px;
    white-space: nowrap;
}

/* Extra padding on bottom when mobile sticky is present */
@media (max-width: 991.98px) {
    body {
        padding-bottom: 75px !important;
    }
}

/* Extra Small Devices (e.g. 360px viewport Galaxy A55) */
@media (max-width: 575.98px) {
    .cart-thumb-img,
    .cart-thumb-placeholder {
        width: 60px;
        height: 60px;
    }
    .cart-item-title {
        font-size: 14px;
        line-height: 1.3;
    }
    .cart-item-checkbox-wrap {
        margin-right: 0.5rem !important;
    }
    .cart-item-thumb {
        margin-right: 0.6rem !important;
    }
    .btn-qty-control {
        padding: 4px 8px;
        font-size: 11px;
    }
    .qty-display {
        min-width: 28px;
        font-size: 13px;
    }
    .item-subtotal {
        font-size: 15px !important;
    }
}

/* Disabled checkout state */
.btn-checkout-action:disabled {
    cursor: not-allowed;
    opacity: 0.55;
    background-color: #adb5bd !important;
    border-color: #adb5bd !important;
}
</style>

<!-- ============================================================ -->
<!-- JAVASCRIPT: Dynamic Calculation & AJAX Interaction          -->
<!-- ============================================================ -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var csrfToken = '<?= csrf_hash() ?>';
    var csrfName  = '<?= csrf_token() ?>';

    var selectAllHeader    = document.getElementById('selectAllHeader');
    var selectAllMobile    = document.getElementById('selectAllMobile');
    var itemCheckboxes     = document.querySelectorAll('.item-checkbox');
    var btnRemoveSelectedTop    = document.getElementById('btnRemoveSelectedTop');
    var btnRemoveSelectedBottom = document.getElementById('btnRemoveSelectedBottom');
    var cartCheckoutForm   = document.getElementById('cartCheckoutForm');

    // -------------------------------------------------------------
    // Helper: Format Rupiah (e.g. 150000 -> "150.000")
    // -------------------------------------------------------------
    function formatRupiah(num) {
        return parseInt(num || 0).toLocaleString('id-ID');
    }

    // -------------------------------------------------------------
    // Update CSRF token di form dan memori
    // -------------------------------------------------------------
    function updateCsrf(newToken) {
        if (!newToken) return;
        csrfToken = newToken;
        var csrfInputs = document.querySelectorAll('input[name="' + csrfName + '"]');
        csrfInputs.forEach(function(input) {
            input.value = newToken;
        });
    }

    // -------------------------------------------------------------
    // Toast Notification Modern
    // -------------------------------------------------------------
    function showToast(msg, type) {
        type = type || 'info';
        var bgClass = 'alert-info';
        if (type === 'success') bgClass = 'alert-success';
        if (type === 'danger' || type === 'error') bgClass = 'alert-danger';
        if (type === 'warning') bgClass = 'alert-warning';

        var toast = document.createElement('div');
        toast.className = 'alert ' + bgClass + ' alert-dismissible shadow-lg position-fixed';
        toast.style.cssText = 'bottom:24px; right:20px; z-index:99999; min-width:280px; max-width:90%; border-radius:8px; animation:fadeIn .25s ease;';
        toast.innerHTML = msg + '<button type="button" class="close ml-2" data-dismiss="alert">&times;</button>';
        document.body.appendChild(toast);

        setTimeout(function() {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity .3s ease';
            setTimeout(function() { toast.remove(); }, 300);
        }, 3200);
    }

    // -------------------------------------------------------------
    // KALKULASI DINAMIS CLIENT-SIDE (Shopee Style)
    // Menghitung HANYA item yang checkbox-nya dicentang
    // -------------------------------------------------------------
    function recalculateSelected() {
        var totalAmount   = 0;
        var selectedCount = 0; // distinct items
        var selectedQty   = 0; // total pieces
        var totalSelectable = 0;

        var currentCheckboxes = document.querySelectorAll('.item-checkbox:not(:disabled)');
        totalSelectable = currentCheckboxes.length;

        currentCheckboxes.forEach(function(chk) {
            var row = document.getElementById('cartRow-' + chk.value);
            if (!row) return;

            var price = parseInt(row.dataset.price) || 0;
            var qty   = parseInt(row.dataset.qty) || 1;

            if (chk.checked) {
                selectedCount++;
                selectedQty += qty;
                totalAmount += (price * qty);
                row.classList.add('item-selected');
            } else {
                row.classList.remove('item-selected');
            }
        });

        // Update teks summary desktop
        var summaryTotal         = document.getElementById('summaryTotal');
        var summarySelectedCount = document.getElementById('summarySelectedCount');
        var summarySelectedQty   = document.getElementById('summarySelectedQty');
        var btnCheckoutCount     = document.getElementById('btnCheckoutCount');

        if (summaryTotal) summaryTotal.textContent = 'Rp ' + formatRupiah(totalAmount);
        if (summarySelectedCount) summarySelectedCount.textContent = selectedCount;
        if (summarySelectedQty) summarySelectedQty.textContent = selectedQty;
        if (btnCheckoutCount) btnCheckoutCount.textContent = selectedCount;

        // Update summary mobile
        var mobileSummaryTotal       = document.getElementById('mobileSummaryTotal');
        var btnCheckoutMobileCount   = document.getElementById('btnCheckoutMobileCount');
        if (mobileSummaryTotal) mobileSummaryTotal.textContent = 'Rp ' + formatRupiah(totalAmount);
        if (btnCheckoutMobileCount) btnCheckoutMobileCount.textContent = selectedCount;

        // Status Tombol Checkout
        var checkoutButtons = document.querySelectorAll('.btn-checkout-action');
        var isEnabled = (selectedCount > 0);
        checkoutButtons.forEach(function(btn) {
            btn.disabled = !isEnabled;
        });

        // Sinkronisasi status checkbox "Pilih Semua"
        var isAll = (totalSelectable > 0 && selectedCount === totalSelectable);
        if (selectAllHeader) {
            selectAllHeader.checked = isAll;
            selectAllHeader.disabled = (totalSelectable === 0);
        }
        if (selectAllMobile) {
            selectAllMobile.checked = isAll;
            selectAllMobile.disabled = (totalSelectable === 0);
        }
        var selectAllHeaderCount = document.getElementById('selectAllHeaderCount');
        if (selectAllHeaderCount) selectAllHeaderCount.textContent = totalSelectable;
    }

    // Jalankan kalkulasi pertama kali
    recalculateSelected();

    
    // -------------------------------------------------------------
    // SINKRONISASI HARGA OTOMATIS DARI DATABASE
    // Memeriksa harga terbaru setiap 10 detik.
    // -------------------------------------------------------------
    var priceSyncInProgress = false;

    function syncPricesFromServer() {
        if (priceSyncInProgress || document.hidden) {
            return;
        }

        priceSyncInProgress = true;

        var formData = new FormData();
        formData.append(csrfName, csrfToken);

        fetch('<?= site_url("cart/syncPrices") ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(function(res) {
            if (!res.ok) {
                throw new Error('Gagal mengambil harga terbaru.');
            }
            return res.json();
        })
        .then(function(data) {
            if (data.csrf_token) {
                updateCsrf(data.csrf_token);
            }

            if (!data.success || !Array.isArray(data.items)) {
                return;
            }

            data.items.forEach(function(item) {
                var row = document.getElementById(
                    'cartRow-' + item.product_id
                );

                if (!row) {
                    return;
                }

                var newPrice = parseInt(item.price, 10) || 0;
                var qty      = parseInt(row.dataset.qty, 10) || 0;
                var oldPrice = parseInt(row.dataset.price, 10) || 0;

                // Perbarui hanya jika harga benar-benar berubah.
                if (oldPrice !== newPrice) {
                    row.dataset.price = newPrice;

                    var priceElement = document.getElementById(
                        'price-' + item.product_id
                    );

                    if (priceElement) {
                        priceElement.textContent =
                            'Rp ' + formatRupiah(newPrice);
                    }

                    var subtotalElement = document.getElementById(
                        'subtotal-' + item.product_id
                    );

                    if (subtotalElement) {
                        subtotalElement.textContent =
                            'Rp ' + formatRupiah(newPrice * qty);
                    }
                }
            });

            // Hitung ulang total berdasarkan checkbox yang aktif.
            // Status checkbox tidak diubah.
            recalculateSelected();
        })
        .catch(function(err) {
            // Kegagalan sementara tidak menghapus isi keranjang.
            console.error('Price sync error:', err);
        })
        .finally(function() {
            priceSyncInProgress = false;
        });
    }

    // Periksa setiap 10 detik tanpa me-refresh halaman.
    setInterval(syncPricesFromServer, 10000);

    // Periksa kembali saat pembeli kembali ke tab keranjang.
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            syncPricesFromServer();
        }
    });


    // -------------------------------------------------------------
    // EVENT: TOGGLE CHECKBOX ITEM
    // -------------------------------------------------------------
    document.querySelectorAll('.item-checkbox').forEach(function(chk) {
        chk.addEventListener('change', function() {
            var productId = this.value;
            var isChecked = this.checked ? 1 : 0;

            // Update kalkulasi langsung seketika
            recalculateSelected();

            // Kirim AJAX ke server untuk sinkronisasi session
            var formData = new FormData();
            formData.append('product_id', productId);
            formData.append('selected', isChecked);
            formData.append(csrfName, csrfToken);

            fetch('<?= site_url("cart/toggleSelect") ?>', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.csrf_token) updateCsrf(data.csrf_token);
            })
            .catch(function(err) {
                console.error('Toggle select error:', err);
            });
        });
    });

    // -------------------------------------------------------------
    // EVENT: PILIH SEMUA (SELECT ALL)
    // -------------------------------------------------------------
    function handleSelectAll(isChecked) {
        var currentCheckboxes = document.querySelectorAll('.item-checkbox:not(:disabled)');
        currentCheckboxes.forEach(function(chk) {
            chk.checked = isChecked;
        });

        recalculateSelected();

        // AJAX update ke session
        var formData = new FormData();
        formData.append('selected', isChecked ? 1 : 0);
        formData.append(csrfName, csrfToken);

        fetch('<?= site_url("cart/selectAll") ?>', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.csrf_token) updateCsrf(data.csrf_token);
        })
        .catch(function(err) {
            console.error('Select all error:', err);
        });
    }

    if (selectAllHeader) {
        selectAllHeader.addEventListener('change', function() {
            if (selectAllMobile) selectAllMobile.checked = this.checked;
            handleSelectAll(this.checked);
        });
    }

    if (selectAllMobile) {
        selectAllMobile.addEventListener('change', function() {
            if (selectAllHeader) selectAllHeader.checked = this.checked;
            handleSelectAll(this.checked);
        });
    }

    // -------------------------------------------------------------
    // EVENT: STEPPER QUANTITY ([-] & [+])
    // -------------------------------------------------------------
    function updateQtyAjax(productId, action, btnTrigger) {
        var row = document.getElementById('cartRow-' + productId);
        if (!row) return;

        var formData = new FormData();
        formData.append('product_id', productId);
        formData.append('action', action);
        formData.append(csrfName, csrfToken);

        if (btnTrigger) btnTrigger.disabled = true;

        fetch('<?= site_url("cart/update") ?>', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (btnTrigger) btnTrigger.disabled = false;
            if (data.csrf_token) updateCsrf(data.csrf_token);

            if (data.success) {
                // Update tampilan qty & dataset
                var qtySpan = document.getElementById('qty-' + productId);
                if (qtySpan) qtySpan.textContent = data.newQty;
                row.dataset.qty = data.newQty;

                // Update subtotal item
                var subtotalSpan = document.getElementById('subtotal-' + productId);
                if (subtotalSpan) subtotalSpan.textContent = 'Rp ' + data.subtotal;

                // Atur status disabled tombol minus/plus
                var stok     = parseInt(row.dataset.stok) || 999;
                var minusBtn = row.querySelector('.btn-qty-minus');
                var plusBtn  = row.querySelector('.btn-qty-plus');

                if (minusBtn) minusBtn.disabled = (data.newQty <= 1);
                if (plusBtn)  plusBtn.disabled  = (data.newQty >= stok);

                // Update cart badge di header
                var badge = document.getElementById('cartCountBadge');
                if (badge) badge.textContent = data.count;
                var cartCounts = document.querySelectorAll('.cart-count');
                cartCounts.forEach(function(el) { el.textContent = data.count; });

                // Recalculate
                recalculateSelected();
            }
        })
        .catch(function(err) {
            if (btnTrigger) btnTrigger.disabled = false;
            console.error('Update qty error:', err);
        });
    }

    // Tombol MINUS
    document.querySelectorAll('.btn-qty-minus').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var productId = this.dataset.productId;
            var row       = document.getElementById('cartRow-' + productId);
            var currentQty = parseInt(row.dataset.qty) || 1;

            if (currentQty <= 1) return;
            updateQtyAjax(productId, 'decrement', this);
        });
    });

    // Tombol PLUS
    document.querySelectorAll('.btn-qty-plus').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var productId = this.dataset.productId;
            var row       = document.getElementById('cartRow-' + productId);
            var stok      = parseInt(row.dataset.stok) || 999;
            var currentQty = parseInt(row.dataset.qty) || 0;

            if (currentQty >= stok) {
                showToast('Stok produk hanya tersisa ' + stok + ' pcs.', 'warning');
                return;
            }
            updateQtyAjax(productId, 'increment', this);
        });
    });

    // -------------------------------------------------------------
    // EVENT: HAPUS SATU ITEM
    // -------------------------------------------------------------
    document.querySelectorAll('.btn-remove-item').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var productId = this.dataset.productId;
            var row       = document.getElementById('cartRow-' + productId);

            if (!confirm('Hapus produk ini dari keranjang belanja Anda?')) {
                return;
            }

            var formData = new FormData();
            formData.append('product_id', productId);
            formData.append(csrfName, csrfToken);

            fetch('<?= site_url("cart/remove") ?>', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.csrf_token) updateCsrf(data.csrf_token);
                if (data.success) {
                    if (row) {
                        row.style.transition = 'all .3s ease';
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(20px)';
                        setTimeout(function() {
                            row.remove();
                            recalculateSelected();
                            // Update total item di header
                            var remainingRows = document.querySelectorAll('.cart-item-row');
                            var headerTotal = document.getElementById('headerTotalItems');
                            if (headerTotal) headerTotal.textContent = remainingRows.length;
                            if (remainingRows.length === 0) {
                                location.reload();
                            }
                        }, 300);
                    }

                    // Update badge keranjang
                    var badge = document.getElementById('cartCountBadge');
                    if (badge) badge.textContent = data.count;
                    var cartCounts = document.querySelectorAll('.cart-count');
                    cartCounts.forEach(function(el) { el.textContent = data.count; });

                    showToast('Produk berhasil dihapus dari keranjang.', 'success');
                }
            })
            .catch(function(err) {
                console.error('Remove item error:', err);
            });
        });
    });

    // -------------------------------------------------------------
    // EVENT: HAPUS PRODUK YANG DICENTANG (BULK REMOVE)
    // -------------------------------------------------------------
    function handleRemoveSelected() {
        var checked = document.querySelectorAll('.item-checkbox:checked');
        if (checked.length === 0) {
            showToast('Silakan pilih produk yang ingin Anda hapus terlebih dahulu.', 'warning');
            return;
        }

        if (!confirm('Hapus ' + checked.length + ' produk terpilih dari keranjang belanja?')) {
            return;
        }

        var formData = new FormData();
        checked.forEach(function(chk) {
            formData.append('selected_items[]', chk.value);
        });
        formData.append(csrfName, csrfToken);

        fetch('<?= site_url("cart/removeSelected") ?>', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.csrf_token) updateCsrf(data.csrf_token);
            if (data.success) {
                checked.forEach(function(chk) {
                    var r = document.getElementById('cartRow-' + chk.value);
                    if (r) r.remove();
                });
                recalculateSelected();
                showToast('Produk terpilih berhasil dihapus.', 'success');

                var remainingRows = document.querySelectorAll('.cart-item-row');
                var headerTotal = document.getElementById('headerTotalItems');
                if (headerTotal) headerTotal.textContent = remainingRows.length;

                // Update badge keranjang
                var badge = document.getElementById('cartCountBadge');
                if (badge) badge.textContent = data.count;
                var cartCounts = document.querySelectorAll('.cart-count');
                cartCounts.forEach(function(el) { el.textContent = data.count; });

                if (remainingRows.length === 0) {
                    location.reload();
                }
            }
        })
        .catch(function(err) {
            console.error('Remove selected error:', err);
        });
    }

    if (btnRemoveSelectedTop) {
        btnRemoveSelectedTop.addEventListener('click', handleRemoveSelected);
    }
    if (btnRemoveSelectedBottom) {
        btnRemoveSelectedBottom.addEventListener('click', handleRemoveSelected);
    }

    // -------------------------------------------------------------
    // VALIDASI SUBMIT FORM CHECKOUT
    // -------------------------------------------------------------
    if (cartCheckoutForm) {
        cartCheckoutForm.addEventListener('submit', function(e) {
            var checked = document.querySelectorAll('.item-checkbox:checked');
            if (checked.length === 0) {
                e.preventDefault();
                showToast('Silakan pilih (centang) minimal 1 produk untuk checkout.', 'warning');
            }
        });
    }
});
</script>

<?= $this->endSection() ?>
