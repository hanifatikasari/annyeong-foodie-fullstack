<?= $this->extend('themes/indomarket/layout') ?>
<?= $this->section('content') ?>

<section class="checkout-section py-5">
    <div class="container">
        <h3 class="font-weight-bold mb-4"><i class="fa fa-lock mr-2"></i>Checkout</h3>

        <?php if (!empty(session('errors'))): ?>
            <div class="alert alert-danger">
                <?php foreach (session('errors') as $e): ?>
                    <div><?= esc($e) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('checkout/process') ?>" method="post">
            <?= csrf_field() ?>
            <div class="row">
                <!-- Form Pengiriman -->
                <div class="col-lg-7 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white"><h6 class="mb-0">Data Pengiriman</h6></div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Nama Penerima <span class="text-danger">*</span></label>
                                <input type="text" name="nama_penerima" class="form-control"
                                       value="<?= old('nama_penerima', $user->first_name . ' ' . $user->last_name) ?>" required>
                            </div>
                            <div class="form-group">
                                <label>No. HP Penerima <span class="text-danger">*</span></label>
                                <input type="text" name="no_hp_penerima" class="form-control"
                                       value="<?= old('no_hp_penerima', $user->phone ?? '') ?>" required
                                       placeholder="contoh: 08123456789">
                            </div>
                            <div class="form-group">
                                <label>Alamat Lengkap <span class="text-danger">*</span></label>
                                <textarea name="alamat_pengiriman" class="form-control" rows="3" required
                                          placeholder="Jl. ..., RT/RW, Kelurahan, Kecamatan, Kota"><?= old('alamat_pengiriman', $user->alamat ?? '') ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Catatan Order</label>
                                <input type="text" name="catatan_order" class="form-control"
                                       placeholder="Catatan untuk penjual (opsional)"
                                       value="<?= old('catatan_order') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm mt-3">
                        <div class="card-header bg-primary text-white"><h6 class="mb-0">Metode Pembayaran</h6></div>
                        <div class="card-body">
                            <div class="custom-control custom-radio mb-2">
                                <input type="radio" id="qris" name="pembayaran" value="QRIS" class="custom-control-input" required checked>
                                <label class="custom-control-label" for="qris">
                                    <i class="fa fa-qrcode mr-1 text-primary"></i> QRIS
                                    <small class="text-muted d-block ml-4">Scan QR di aplikasi dompet digital Anda</small>
                                </label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="transfer" name="pembayaran" value="Transfer" class="custom-control-input">
                                <label class="custom-control-label" for="transfer">
                                    <i class="fa fa-university mr-1 text-success"></i> Transfer Bank
                                    <small class="text-muted d-block ml-4">BCA: 1234567890 a/n Annyeong Foodie</small>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="col-lg-5">
                    <div class="card shadow-sm">
                        <div class="card-header bg-dark text-white"><h6 class="mb-0">Ringkasan Pesanan</h6></div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead class="bg-light">
                                        <tr><th>Produk</th><th class="text-right">Subtotal</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($cart as $item): ?>
                                            <tr>
                                                <td>
                                                    <?= esc($item['name']) ?>
                                                    <small class="text-muted">× <?= $item['qty'] ?></small>
                                                </td>
                                                <td class="text-right text-nowrap">Rp <?= number_format($item['price'] * $item['qty']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-primary">
                                            <th>TOTAL</th>
                                            <th class="text-right">Rp <?= number_format($total) ?></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-success btn-block btn-lg" onclick="return confirm('Konfirmasi pesanan?')">
                                <i class="fa fa-check-circle mr-1"></i> Buat Pesanan
                            </button>
                            <a href="<?= site_url('cart') ?>" class="btn btn-outline-secondary btn-block btn-sm mt-2">
                                <i class="fa fa-arrow-left mr-1"></i> Kembali ke Keranjang
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<?= $this->endSection() ?>