<?= $this->extend('themes/indomarket/layout') ?>
<?= $this->section('content') ?>

<section class="cart-section py-5">
    <div class="container">
        <h3 class="font-weight-bold mb-4">
            <i class="fa fa-shopping-cart mr-2 text-primary"></i>Keranjang Belanja
        </h3>

        <?php if (!empty($cart)): ?>
            <div class="row">
                <!-- Cart Items -->
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm border-0" id="cartTableWrapper">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table mb-0" id="cartTable">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="min-width:200px;">Produk</th>
                                            <th class="text-center">Harga Satuan</th>
                                            <th class="text-center" style="min-width:140px;">Jumlah</th>
                                            <th class="text-center">Subtotal</th>
                                            <th class="text-center">Hapus</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($cart as $item): ?>
                                            <tr data-product-id="<?= $item['product_id'] ?>"
                                                data-price="<?= $item['price'] ?>"
                                                data-stok="<?= $item['stok'] ?>">

                                                <!-- Produk -->
                                                <td class="align-middle">
                                                    <div class="d-flex align-items-center">
                                                        <?php if (!empty($item['image'])): ?>
                                                            <img src="<?= base_url($item['image']) ?>"
                                                                 width="60" height="60"
                                                                 class="rounded mr-3"
                                                                 style="object-fit:cover;">
                                                        <?php else: ?>
                                                            <div class="bg-light rounded mr-3 d-flex align-items-center justify-content-center"
                                                                 style="width:60px;height:60px;flex-shrink:0;">
                                                                <i class="fa fa-image text-muted"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div>
                                                            <strong class="d-block"><?= esc($item['name']) ?></strong>
                                                            <small class="text-muted">SKU: <?= esc($item['sku']) ?></small>
                                                            <small class="text-muted d-block">Stok: <?= $item['stok'] ?></small>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- Harga -->
                                                <td class="align-middle text-center text-nowrap">
                                                    Rp <?= number_format($item['price']) ?>
                                                </td>

                                                <!-- *** FIX ISSUE #7: Shopee-style [-] qty [+] *** -->
                                                <td class="align-middle text-center">
                                                    <div class="qty-control d-inline-flex align-items-center border rounded"
                                                         style="overflow:hidden;">
                                                        <button type="button"
                                                                class="btn-qty-control btn-qty-minus"
                                                                data-product-id="<?= $item['product_id'] ?>"
                                                                style="border:none;background:transparent;padding:6px 12px;font-size:16px;cursor:pointer;color:#333;"
                                                                <?= $item['qty'] <= 1 ? 'disabled style="opacity:0.4;"' : '' ?>>
                                                            &#8722;
                                                        </button>
                                                        <span class="qty-display"
                                                              id="qty-<?= $item['product_id'] ?>"
                                                              style="min-width:32px;text-align:center;font-weight:600;font-size:15px;padding:0 4px;">
                                                            <?= $item['qty'] ?>
                                                        </span>
                                                        <button type="button"
                                                                class="btn-qty-control btn-qty-plus"
                                                                data-product-id="<?= $item['product_id'] ?>"
                                                                style="border:none;background:transparent;padding:6px 12px;font-size:16px;cursor:pointer;color:#333;"
                                                                <?= $item['qty'] >= $item['stok'] ? 'disabled style="opacity:0.4;"' : '' ?>>
                                                            &#43;
                                                        </button>
                                                    </div>
                                                </td>

                                                <!-- Subtotal -->
                                                <td class="align-middle text-center text-nowrap font-weight-bold text-primary"
                                                    id="subtotal-<?= $item['product_id'] ?>">
                                                    Rp <?= number_format($item['price'] * $item['qty']) ?>
                                                </td>

                                                <!-- Hapus -->
                                                <td class="align-middle text-center">
                                                    <button type="button"
                                                            class="btn btn-danger btn-sm btn-remove-item"
                                                            data-product-id="<?= $item['product_id'] ?>"
                                                            title="Hapus produk ini">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                            <a href="<?= site_url('products') ?>" class="btn btn-outline-secondary btn-sm">
                                <i class="fa fa-arrow-left mr-1"></i> Lanjut Belanja
                            </a>
                            <a href="<?= site_url('cart/clear') ?>"
                               class="btn btn-outline-danger btn-sm"
                               onclick="return confirm('Kosongkan seluruh keranjang?')">
                                <i class="fa fa-trash mr-1"></i> Kosongkan Keranjang
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 sticky-top" style="top:20px;">
                        <div class="card-header bg-primary text-white border-0">
                            <h6 class="mb-0 font-weight-bold">Ringkasan Pesanan</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total Item</span>
                                <span id="summaryCount">
                                    <?= array_sum(array_column($cart, 'qty')) ?> item
                                </span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total Harga</strong>
                                <strong class="text-primary h5 mb-0" id="summaryTotal">
                                    Rp <?= number_format($total) ?>
                                </strong>
                            </div>
                            <a href="<?= site_url('checkout') ?>" class="btn btn-success btn-block btn-lg shadow-sm">
                                <i class="fa fa-lock mr-1"></i> Checkout Sekarang
                            </a>
                            <p class="text-muted text-center small mt-2 mb-0">
                                <i class="fa fa-shield mr-1"></i> Transaksi aman & terenkripsi
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- Keranjang Kosong -->
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fa fa-shopping-cart fa-5x text-muted"></i>
                </div>
                <h4 class="text-muted font-weight-bold">Keranjang Anda Kosong</h4>
                <p class="text-muted">Yuk, mulai belanja produk Korean food favorit Anda!</p>
                <a href="<?= site_url('products') ?>" class="btn btn-primary btn-lg mt-2">
                    <i class="fa fa-shopping-bag mr-1"></i> Mulai Belanja
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- *** FIX ISSUE #7: JavaScript untuk [-] qty [+] dengan AJAX *** -->
<script>
(function() {
    var csrfToken = '<?= csrf_hash() ?>';
    var csrfName  = '<?= csrf_token() ?>';

    // ---- Helper: format number dengan separator ----
    function formatRupiah(angka) {
        return parseInt(angka).toLocaleString('id-ID');
    }

    // ---- Update cart via AJAX ----
    function updateCartAjax(productId, action, onSuccess) {
        var formData = new FormData();
        formData.append('product_id', productId);
        formData.append('action', action);
        formData.append(csrfName, csrfToken);

        fetch('<?= site_url("cart/update") ?>', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.success) {
                // Update CSRF token jika dikembalikan
                if (data.csrf_token) csrfToken = data.csrf_token;

                // Panggil callback dengan data terbaru
                onSuccess(data);

                // Update cart count di header
                document.getElementById('cartCountBadge').textContent = data.count;
            }
        })
        .catch(function(err) {
            console.error('Cart update error:', err);
        });
    }

    // ---- Remove item via AJAX ----
    function removeItemAjax(productId, onSuccess) {
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
            if (data.success) {
                onSuccess(data);
                document.getElementById('cartCountBadge').textContent = data.count;
            }
        })
        .catch(function(err) {
            console.error('Remove error:', err);
        });
    }

    // ---- Event: Tombol MINUS ----
    document.querySelectorAll('.btn-qty-minus').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var productId = this.dataset.productId;
            var row       = document.querySelector('tr[data-product-id="' + productId + '"]');
            var qtySpan   = document.getElementById('qty-' + productId);
            var currentQty = parseInt(qtySpan.textContent) || 1;

            if (currentQty <= 1) {
                // Konfirmasi hapus jika qty sudah 1
                if (confirm('Hapus produk ini dari keranjang?')) {
                    removeItemAjax(productId, function(data) {
                        row.remove();
                        updateSummary(data.cartTotal, data.count);
                        if (data.count === 0) location.reload();
                    });
                }
                return;
            }

            updateCartAjax(productId, 'decrement', function(data) {
                qtySpan.textContent = data.newQty;
                document.getElementById('subtotal-' + productId).textContent =
                    'Rp ' + data.subtotal;
                updateSummary(data.cartTotal, data.count);

                // Re-enable plus button
                var plusBtn = row.querySelector('.btn-qty-plus');
                if (plusBtn) plusBtn.disabled = false;

                // Disable minus jika qty = 1
                if (data.newQty <= 1) btn.disabled = true;
            });
        });
    });

    // ---- Event: Tombol PLUS ----
    document.querySelectorAll('.btn-qty-plus').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var productId = this.dataset.productId;
            var row       = document.querySelector('tr[data-product-id="' + productId + '"]');
            var qtySpan   = document.getElementById('qty-' + productId);
            var stok      = parseInt(row.dataset.stok) || 99;
            var currentQty = parseInt(qtySpan.textContent) || 0;

            if (currentQty >= stok) {
                showToast('Stok hanya tersisa ' + stok + '.', 'warning');
                return;
            }

            updateCartAjax(productId, 'increment', function(data) {
                qtySpan.textContent = data.newQty;
                document.getElementById('subtotal-' + productId).textContent =
                    'Rp ' + data.subtotal;
                updateSummary(data.cartTotal, data.count);

                // Re-enable minus button
                var minusBtn = row.querySelector('.btn-qty-minus');
                if (minusBtn) minusBtn.disabled = false;

                // Disable plus jika sudah max stok
                if (data.newQty >= stok) btn.disabled = true;
            });
        });
    });

    // ---- Event: Tombol HAPUS ----
    document.querySelectorAll('.btn-remove-item').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var productId = this.dataset.productId;
            var row       = document.querySelector('tr[data-product-id="' + productId + '"]');

            if (!confirm('Hapus produk ini dari keranjang?')) return;

            removeItemAjax(productId, function(data) {
                row.remove();
                updateSummary(data.cartTotal, data.count);
                if (data.count === 0) location.reload();
            });
        });
    });

    // ---- Update ringkasan total ----
    function updateSummary(total, count) {
        var summaryTotal = document.getElementById('summaryTotal');
        var summaryCount = document.getElementById('summaryCount');
        if (summaryTotal) summaryTotal.textContent = 'Rp ' + total;
        if (summaryCount) summaryCount.textContent = count + ' item';
    }

    // ---- Toast notification ----
    function showToast(msg, type) {
        type = type || 'info';
        var colors = { success: '#28a745', danger: '#dc3545', warning: '#ffc107', info: '#17a2b8' };
        var toast = document.createElement('div');
        toast.style.cssText = [
            'position:fixed;bottom:24px;right:24px;z-index:9999;',
            'background:' + (colors[type] || colors.info) + ';color:#fff;',
            'padding:12px 20px;border-radius:8px;',
            'box-shadow:0 4px 15px rgba(0,0,0,.2);',
            'font-size:14px;min-width:220px;',
            'animation:fadeIn .3s ease;'
        ].join('');
        toast.textContent = msg;
        document.body.appendChild(toast);
        setTimeout(function() {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity .3s';
            setTimeout(function() { toast.remove(); }, 300);
        }, 3000);
    }
})();
</script>

<style>
.btn-qty-control:hover { background: #f8f9fa !important; }
.btn-qty-control:disabled { cursor: not-allowed !important; opacity: 0.4; }
.qty-control { user-select: none; }
</style>

<?= $this->endSection() ?>