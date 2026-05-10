<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1>Transaksi Penjualan Baru</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/penjualan') ?>">Penjualan</a></li>
                    <li class="breadcrumb-item active">Baru</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <?= view('admin/shared/flash_message') ?>

        <form method="post" action="<?= site_url('admin/penjualan/simpan') ?>" id="formPenjualan">
        <?= csrf_field() ?>
        <div class="row">
            <!-- PILIH PRODUK -->
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Pilih Produk</h3></div>
                    <div class="card-body">
                        <div class="input-group mb-3">
                            <input type="text" id="searchProduk" class="form-control"
                                   placeholder="Ketik nama produk...">
                        </div>
                        <div class="row" id="productGrid">
                            <?php foreach ($products as $p): ?>
                            <div class="col-md-4 product-card mb-2"
                                 data-name="<?= strtolower($p->name) ?>"
                                 data-id="<?= $p->id ?>"
                                 data-price="<?= $p->price ?>"
                                 data-stok="<?= $p->qty ?>"
                                 data-hpp="<?= $p->hpp_total ?>">
                                <div class="card card-outline card-primary h-100"
                                     style="cursor:pointer"
                                     onclick="addToCart(<?= $p->id ?>, '<?= esc($p->name) ?>', <?= $p->price ?>, <?= $p->qty ?>, <?= $p->hpp_total ?>)">
                                    <div class="card-body p-2 text-center">
                                        <strong><?= esc($p->name) ?></strong><br>
                                        <span class="text-success">Rp <?= number_format($p->price) ?></span><br>
                                        <small class="text-muted">Stok: <?= $p->qty ?></small>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KERANJANG -->
            <div class="col-md-5">
                <div class="card card-success">
                    <div class="card-header"><h3 class="card-title"><i class="fas fa-shopping-cart"></i> Keranjang</h3></div>
                    <div class="card-body p-0">
                        <table class="table table-sm mb-0" id="cartTable">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-center" style="width:80px">Qty</th>
                                    <th class="text-right">Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="cartBody">
                                <tr id="emptyRow"><td colspan="4" class="text-center text-muted py-3">Keranjang kosong</td></tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-right">Total</th>
                                    <th class="text-right" id="totalDisplay">Rp 0</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="card-footer">
                        <div class="form-group row mb-2">
                            <label class="col-sm-5 col-form-label">Diskon (Rp)</label>
                            <div class="col-sm-7">
                                <input type="number" name="diskon" id="diskonInput"
                                       class="form-control form-control-sm" value="0" min="0">
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label class="col-sm-5 col-form-label">Metode Bayar</label>
                            <div class="col-sm-7">
                                <select name="pembayaran" id="pembayaranSelect" class="form-control form-control-sm">
                                    <option value="Cash">Cash</option>
                                    <option value="QRIS">QRIS</option>
                                    <option value="Transfer">Transfer</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row mb-2" id="uangRow">
                            <label class="col-sm-5 col-form-label">Uang Diterima</label>
                            <div class="col-sm-7">
                                <input type="number" name="uang_diterima" id="uangInput"
                                       class="form-control form-control-sm" value="0">
                            </div>
                        </div>

                        <div class="alert alert-info py-1 mb-2" id="summaryBox">
                            <table class="table table-sm table-borderless mb-0">
                                <tr><td>Subtotal</td><td class="text-right" id="subTotal">Rp 0</td></tr>
                                <tr><td>Diskon</td><td class="text-right text-danger" id="diskonDisplay">Rp 0</td></tr>
                                <tr class="border-top"><td><strong>Total Bayar</strong></td><td class="text-right"><strong id="totalBayar">Rp 0</strong></td></tr>
                                <tr><td>Kembalian</td><td class="text-right text-warning" id="kembalian">Rp 0</td></tr>
                            </table>
                        </div>

                        <button type="submit" class="btn btn-success btn-block" id="btnBayar" disabled>
                            <i class="fas fa-check"></i> Proses Pembayaran
                        </button>
                        <a href="<?= site_url('admin/penjualan') ?>" class="btn btn-secondary btn-block mt-1">Batal</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden items container -->
        <div id="hiddenItems"></div>
        </form>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
let cart = {};

function addToCart(id, name, price, stok, hpp) {
    if (cart[id]) {
        if (cart[id].qty >= stok) {
            alert('Stok ' + name + ' hanya tersisa ' + stok + ' porsi!');
            return;
        }
        cart[id].qty++;
    } else {
        cart[id] = { id, name, price, stok, hpp, qty: 1 };
    }
    renderCart();
}

function removeFromCart(id) {
    delete cart[id];
    renderCart();
}

function updateQty(id, val) {
    val = parseInt(val);
    if (val <= 0) { removeFromCart(id); return; }
    if (val > cart[id].stok) { alert('Stok hanya ' + cart[id].stok); val = cart[id].stok; }
    cart[id].qty = val;
    renderCart();
}

function renderCart() {
    const body    = document.getElementById('cartBody');
    const hidden  = document.getElementById('hiddenItems');
    const empty   = document.getElementById('emptyRow');
    let total     = 0;
    let html      = '';
    let hiddenHtml = '';
    let i = 0;

    for (const id in cart) {
        const item    = cart[id];
        const subtotal = item.price * item.qty;
        total += subtotal;

        html += `<tr>
            <td>${item.name}</td>
            <td><input type="number" value="${item.qty}" min="1" max="${item.stok}"
                       class="form-control form-control-sm text-center"
                       onchange="updateQty(${id}, this.value)" style="width:60px"></td>
            <td class="text-right">Rp ${subtotal.toLocaleString('id-ID')}</td>
            <td><button type="button" class="btn btn-danger btn-xs" onclick="removeFromCart(${id})"><i class="fas fa-times"></i></button></td>
        </tr>`;
        hiddenHtml += `<input type="hidden" name="items[${i}][product_id]" value="${id}">
                       <input type="hidden" name="items[${i}][qty]" value="${item.qty}">`;
        i++;
    }

    body.innerHTML = Object.keys(cart).length ? html : '<tr><td colspan="4" class="text-center text-muted py-3">Keranjang kosong</td></tr>';
    hidden.innerHTML = hiddenHtml;

    document.getElementById('totalDisplay').textContent = 'Rp ' + total.toLocaleString('id-ID');
    updateSummary(total);
    document.getElementById('btnBayar').disabled = Object.keys(cart).length === 0;
}

function updateSummary(total) {
    const diskon     = parseInt(document.getElementById('diskonInput').value) || 0;
    const uang       = parseInt(document.getElementById('uangInput').value) || 0;
    const totalBayar = Math.max(0, total - diskon);
    const kembalian  = Math.max(0, uang - totalBayar);

    document.getElementById('subTotal').textContent    = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('diskonDisplay').textContent = 'Rp ' + diskon.toLocaleString('id-ID');
    document.getElementById('totalBayar').textContent  = 'Rp ' + totalBayar.toLocaleString('id-ID');
    document.getElementById('kembalian').textContent   = 'Rp ' + kembalian.toLocaleString('id-ID');
}

document.getElementById('diskonInput').addEventListener('input', () => {
    const total = Object.values(cart).reduce((s, i) => s + i.price * i.qty, 0);
    updateSummary(total);
});
document.getElementById('uangInput').addEventListener('input', () => {
    const total = Object.values(cart).reduce((s, i) => s + i.price * i.qty, 0);
    updateSummary(total);
});
document.getElementById('pembayaranSelect').addEventListener('change', function() {
    document.getElementById('uangRow').style.display = this.value === 'Cash' ? '' : 'none';
});

// Pencarian produk
document.getElementById('searchProduk').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.product-card').forEach(el => {
        el.style.display = el.dataset.name.includes(q) ? '' : 'none';
    });
});
</script>
<?= $this->endSection() ?>