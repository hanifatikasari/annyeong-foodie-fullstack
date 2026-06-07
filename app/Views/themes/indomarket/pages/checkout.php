<?= $this->extend('themes/indomarket/layout') ?>
<?= $this->section('content') ?>

<?php
/**
 * Checkout View
 *
 * FIX ISSUE #4:
 * - name="no_hp_penerima" HARUS sama persis dengan validation rule
 * - name="alamat_pengiriman" HARUS sama persis dengan validation rule
 * - Preload user data dengan null-safe (??)
 *
 * FIX ISSUE #8:
 * - Guard: $user bisa null, gunakan $user->phone ?? ''
 */
$user = $user ?? null;
?>

<section class="checkout-section py-5">
    <div class="container">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb bg-transparent p-0">
                <li class="breadcrumb-item"><a href="<?= site_url() ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url('cart') ?>">Keranjang</a></li>
                <li class="breadcrumb-item active">Checkout</li>
            </ol>
        </nav>

        <h3 class="font-weight-bold mb-4">
            <i class="fa fa-lock mr-2 text-primary"></i>Checkout
        </h3>

        <!-- Validation Errors -->
        <?php if (!empty(session('errors'))): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong><i class="fa fa-exclamation-triangle mr-1"></i>Mohon perbaiki kesalahan berikut:</strong>
                <ul class="mb-0 mt-1 pl-4">
                    <?php foreach (session('errors') as $fieldName => $errMsg): ?>
                        <li><?= esc($errMsg) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('checkout/process') ?>" method="post" id="checkoutForm">
            <?= csrf_field() ?>

            <div class="row">
                <!-- ============================
                     KOLOM KIRI: Form Pengiriman
                     ============================ -->
                <div class="col-lg-7 mb-4">

                    <!-- Data Penerima -->
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-header bg-primary text-white border-0">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="fa fa-map-marker mr-2"></i>Data Pengiriman
                            </h6>
                        </div>
                        <div class="card-body">
                            <!-- Nama Penerima -->
                            <div class="form-group">
                                <label for="nama_penerima">
                                    Nama Penerima <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       id="nama_penerima"
                                       name="nama_penerima"
                                       class="form-control <?= session('errors.nama_penerima') ? 'is-invalid' : '' ?>"
                                       value="<?= old('nama_penerima', ($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?>"
                                       placeholder="Nama lengkap penerima"
                                       required>
                                <?php if (session('errors.nama_penerima')): ?>
                                    <div class="invalid-feedback"><?= session('errors.nama_penerima') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- No HP Penerima
                                 FIX ISSUE #4: field name HARUS "no_hp_penerima" -->
                            <div class="form-group">
                                <label for="no_hp_penerima">
                                    No. HP Penerima <span class="text-danger">*</span>
                                </label>
                                <input type="tel"
                                       id="no_hp_penerima"
                                       name="no_hp_penerima"
                                       class="form-control <?= session('errors.no_hp_penerima') ? 'is-invalid' : '' ?>"
                                       value="<?= old('no_hp_penerima', $user->phone ?? '') ?>"
                                       placeholder="contoh: 08123456789"
                                       required>
                                <?php if (session('errors.no_hp_penerima')): ?>
                                    <div class="invalid-feedback"><?= session('errors.no_hp_penerima') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Alamat Pengiriman
                                 FIX ISSUE #4: field name HARUS "alamat_pengiriman" -->
                            <div class="form-group">
                                <label for="alamat_pengiriman">
                                    Alamat Lengkap <span class="text-danger">*</span>
                                </label>
                                <textarea id="alamat_pengiriman"
                                          name="alamat_pengiriman"
                                          class="form-control <?= session('errors.alamat_pengiriman') ? 'is-invalid' : '' ?>"
                                          rows="3"
                                          placeholder="Jl. ..., RT/RW, Kelurahan, Kecamatan, Kota, Provinsi, Kode Pos"
                                          required><?= old('alamat_pengiriman', $user->alamat ?? '') ?></textarea>
                                <?php if (session('errors.alamat_pengiriman')): ?>
                                    <div class="invalid-feedback"><?= session('errors.alamat_pengiriman') ?></div>
                                <?php endif; ?>
                                <small class="text-muted">
                                    <i class="fa fa-info-circle mr-1"></i>
                                    Isi alamat lengkap agar pengiriman tepat sasaran.
                                    <a href="<?= site_url('account/profile') ?>" target="_blank">Update profil</a>
                                    untuk mengisi otomatis.
                                </small>
                            </div>

                            <!-- Catatan Order -->
                            <div class="form-group mb-0">
                                <label for="catatan_order">Catatan Order (opsional)</label>
                                <input type="text"
                                       id="catatan_order"
                                       name="catatan_order"
                                       class="form-control"
                                       placeholder="Catatan khusus untuk penjual..."
                                       value="<?= old('catatan_order') ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Metode Pembayaran -->
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-primary text-white border-0">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="fa fa-credit-card mr-2"></i>Metode Pembayaran
                            </h6>
                        </div>
                        <div class="card-body">

                            <!-- ========================================
                                OPSI 1: Midtrans (Direkomendasikan)
                                Kartu Kredit, GoPay, OVO, Dana, VA Bank
                                ======================================== -->
                            <div class="custom-control custom-radio mb-3">
                                <input type="radio"
                                    id="payMidtrans"
                                    name="pembayaran"
                                    value="Midtrans"
                                    class="custom-control-input"
                                    <?= old('pembayaran', 'Midtrans') === 'Midtrans' ? 'checked' : '' ?>
                                    required>
                                <label class="custom-control-label" for="payMidtrans">
                                    <strong>
                                        <i class="fa fa-credit-card mr-1 text-primary"></i>
                                        Bayar Online (Midtrans)
                                        <span class="badge badge-success ml-1" style="font-size:10px;">RECOMMENDED</span>
                                    </strong>
                                    <small class="text-muted d-block ml-4 mt-1">
                                        <strong>Tersedia:</strong> Kartu Kredit/Debit, GoPay, OVO, Dana,
                                        ShopeePay, Virtual Account BCA/BNI/BRI/Mandiri, QRIS, dan lainnya.
                                        <br>Proses otomatis, tidak perlu upload bukti.
                                    </small>
                                </label>
                            </div>

                            <!-- Divider -->
                            <div class="d-flex align-items-center mb-3">
                                <hr class="flex-grow-1">
                                <span class="text-muted small px-2">atau bayar manual</span>
                                <hr class="flex-grow-1">
                            </div>

                            <!-- ========================================
                                OPSI 2: QRIS (Manual Upload)
                                ======================================== -->
                            <div class="custom-control custom-radio mb-3">
                                <input type="radio"
                                    id="payQRIS"
                                    name="pembayaran"
                                    value="QRIS"
                                    class="custom-control-input"
                                    <?= old('pembayaran') === 'QRIS' ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="payQRIS">
                                    <strong><i class="fa fa-qrcode mr-1 text-info"></i> QRIS (Upload Bukti Manual)</strong>
                                    <small class="text-muted d-block ml-4 mt-1">
                                        Scan QR code kami, lalu upload bukti pembayaran.
                                        Diverifikasi tim dalam 1-3 jam kerja.
                                    </small>
                                </label>
                            </div>

                            <!-- ========================================
                                OPSI 3: Transfer Bank (Manual Upload)
                                ======================================== -->
                            <div class="custom-control custom-radio">
                                <input type="radio"
                                    id="payTransfer"
                                    name="pembayaran"
                                    value="Transfer"
                                    class="custom-control-input"
                                    <?= old('pembayaran') === 'Transfer' ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="payTransfer">
                                    <strong><i class="fa fa-university mr-1 text-success"></i> Transfer Bank (Upload Bukti Manual)</strong>
                                    <small class="text-muted d-block ml-4 mt-1">
                                        BCA: <strong>1234567890</strong> a/n <strong>Annyeong Foodie</strong>
                                        <br>Transfer lalu upload bukti. Diverifikasi tim dalam 1-3 jam kerja.
                                    </small>
                                </label>
                            </div>

                            <?php if (session('errors.pembayaran')): ?>
                                <div class="text-danger small mt-2">
                                    <?= session('errors.pembayaran') ?>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>

                <!-- ============================
                     KOLOM KANAN: Ringkasan Order
                     ============================ -->
                <div class="col-lg-5">
                    <div class="card shadow-sm border-0 sticky-top" style="top:20px;">
                        <div class="card-header bg-dark text-white border-0">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="fa fa-list-alt mr-2"></i>Ringkasan Pesanan
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Produk</th>
                                            <th class="text-right text-nowrap">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($cart as $item): ?>
                                            <tr>
                                                <td>
                                                    <?= esc($item['name']) ?>
                                                    <span class="text-muted">&times;<?= $item['qty'] ?></span>
                                                </td>
                                                <td class="text-right text-nowrap">
                                                    Rp <?= number_format($item['price'] * $item['qty']) ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-primary">
                                            <th class="text-uppercase">Total</th>
                                            <th class="text-right h6 mb-0">
                                                Rp <?= number_format($total) ?>
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <button type="submit"
                                    class="btn btn-success btn-block btn-lg font-weight-bold"
                                    id="btnCheckout">
                                <i class="fa fa-check-circle mr-2"></i>Buat Pesanan
                            </button>
                            <a href="<?= site_url('cart') ?>"
                               class="btn btn-outline-secondary btn-block btn-sm mt-2">
                                <i class="fa fa-arrow-left mr-1"></i> Kembali ke Keranjang
                            </a>
                            <p class="text-muted text-center small mt-2 mb-0">
                                <i class="fa fa-shield mr-1"></i>Transaksi aman
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
// Disable tombol submit setelah klik untuk mencegah double submit
document.getElementById('checkoutForm').addEventListener('submit', function() {
    var btn = document.getElementById('btnCheckout');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin mr-2"></i>Memproses...';
});
</script>

<?= $this->endSection() ?>