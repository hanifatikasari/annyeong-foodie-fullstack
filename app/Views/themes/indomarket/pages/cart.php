<?= $this->extend('themes/indomarket/layout') ?>
<?= $this->section('content') ?>

<section class="cart-section py-5">
    <div class="container">
        <h3 class="font-weight-bold mb-4"><i class="fa fa-shopping-cart mr-2"></i>Keranjang Belanja</h3>

        <?php if (!empty($cart)): ?>
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Produk</th>
                                            <th class="text-center">Harga</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-center">Subtotal</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($cart as $item): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php if (!empty($item['image'])): ?>
                                                            <img src="<?= base_url($item['image']) ?>" width="60" class="rounded mr-3">
                                                        <?php endif; ?>
                                                        <div>
                                                            <strong><?= esc($item['name']) ?></strong><br>
                                                            <small class="text-muted">SKU: <?= esc($item['sku']) ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle">Rp <?= number_format($item['price']) ?></td>
                                                <td class="text-center align-middle" style="width:120px;">
                                                    <form action="<?= site_url('cart/update') ?>" method="post" class="d-inline-flex">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                                        <div class="input-group input-group-sm">
                                                            <input type="number" name="qty" value="<?= $item['qty'] ?>" min="1" max="<?= $item['stok'] ?>" class="form-control text-center" style="width:50px;">
                                                            <div class="input-group-append">
                                                                <button class="btn btn-outline-secondary" type="submit"><i class="fa fa-sync fa-xs"></i></button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </td>
                                                <td class="text-center align-middle font-weight-bold text-primary">
                                                    Rp <?= number_format($item['price'] * $item['qty']) ?>
                                                </td>
                                                <td class="align-middle">
                                                    <form action="<?= site_url('cart/remove') ?>" method="post">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                                        <button class="btn btn-danger btn-sm" onclick="return confirm('Hapus produk ini?')">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <a href="<?= site_url('products') ?>" class="btn btn-outline-secondary btn-sm">
                                <i class="fa fa-arrow-left mr-1"></i> Lanjut Belanja
                            </a>
                            <a href="<?= site_url('cart/clear') ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Kosongkan keranjang?')">
                                <i class="fa fa-trash mr-1"></i> Kosongkan
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mt-4 mt-lg-0">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white"><h6 class="mb-0">Ringkasan Pesanan</h6></div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total Item</span>
                                <span><?= array_sum(array_column($cart, 'qty')) ?> item</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="font-weight-bold">Total Harga</span>
                                <span class="font-weight-bold text-primary h5">Rp <?= number_format($total) ?></span>
                            </div>
                            <a href="<?= site_url('checkout') ?>" class="btn btn-primary btn-block btn-lg">
                                <i class="fa fa-lock mr-1"></i> Checkout Sekarang
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fa fa-shopping-cart fa-5x text-muted mb-4"></i>
                <h4 class="text-muted">Keranjang Anda Kosong</h4>
                <p>Yuk, mulai belanja produk favorit Anda!</p>
                <a href="<?= site_url('products') ?>" class="btn btn-primary">Mulai Belanja</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>