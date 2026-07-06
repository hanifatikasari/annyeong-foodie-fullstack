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
                            <strong>Invoice:</strong>
                            <?= esc($order->invoice_no) ?>
                            <br>

                            <strong>Subtotal Produk:</strong>
                            Rp <?= number_format($order->subtotal_produk) ?>
                            <br>

                            <?php if (($order->shipping_cost ?? 0) > 0): ?>
                                <strong>Ongkir:</strong>
                                Rp <?= number_format($order->shipping_cost) ?>
                                <br>
                            <?php endif; ?>

                            <?php if (($order->handling_fee ?? 0) > 0): ?>
                                <strong>Biaya Penanganan:</strong>
                                Rp <?= number_format($order->handling_fee) ?>
                                <br>
                            <?php endif; ?>

                            <?php if (($order->diskon ?? 0) > 0): ?>
                                <strong>Diskon:</strong>
                                - Rp <?= number_format($order->diskon) ?>
                                <br>
                            <?php endif; ?>

                            <strong>Total Bayar:</strong>
                            Rp <?= number_format($order->grand_total) ?>
                            <br>

                            <strong>Metode Pembayaran:</strong>
                            <?= esc($order->pembayaran) ?>
                        </div>

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