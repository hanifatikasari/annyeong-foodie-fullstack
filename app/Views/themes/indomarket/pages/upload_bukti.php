<?= $this->extend('themes/indomarket/layout') ?>
<?= $this->section('content') ?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fa fa-upload mr-2"></i>Upload Bukti Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-3">
                            <strong>Invoice:</strong> <?= esc($order->invoice_no) ?><br>
                            <strong>Total Bayar:</strong> Rp <?= number_format($order->total_bayar) ?><br>
                            <strong>Metode:</strong> <?= esc($order->pembayaran) ?>
                        </div>

                        <?php if ($order->pembayaran === 'QRIS'): ?>
                            <div class="text-center mb-3">
                                <img src="<?= base_url('themes/indomarket/assets/img/qris.png') ?>" alt="QRIS" class="img-fluid" style="max-width:250px;">
                                <p class="text-muted small mt-2">Scan QR ini dengan aplikasi dompet digital Anda</p>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-light border mb-3">
                                <strong>Info Transfer:</strong><br>
                                Bank BCA: <strong>1234567890</strong><br>
                                Atas Nama: <strong>Annyeong Foodie</strong><br>
                                Jumlah: <strong>Rp <?= number_format($order->total_bayar) ?></strong>
                            </div>
                        <?php endif; ?>

                        <form action="<?= site_url('checkout/upload/' . $order->invoice_no) ?>" method="post" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <div class="form-group">
                                <label><strong>Upload Bukti Transfer</strong> <span class="text-danger">*</span></label>
                                <input type="file" name="bukti_bayar" class="form-control-file" accept=".jpg,.jpeg,.png,.pdf" required>
                                <small class="text-muted">Format: JPG, PNG, atau PDF. Maksimal 5MB.</small>
                            </div>
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fa fa-upload mr-1"></i> Upload Sekarang
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>