<?= $this->extend('themes/indomarket/layout') ?>
<?= $this->section('content') ?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <div class="card shadow-sm p-5">
                    <div class="text-success mb-4">
                        <i class="fa fa-check-circle fa-5x"></i>
                    </div>

                    <h3 class="font-weight-bold">Pesanan Berhasil Dibuat!</h3>
                    <p class="text-muted mb-2">Nomor Invoice Anda:</p>

                    <h4 class="text-primary font-weight-bold mb-3">
                        <?= esc($order->invoice_no ?? '-') ?>
                    </h4>

                    <div class="alert alert-warning text-left">
                        <h6>
                            <i class="fa fa-info-circle mr-1"></i>
                            Langkah Selanjutnya:
                        </h6>

                        <ol class="mb-0 pl-4">
                            <li>
                                Lakukan pembayaran sebesar
                                <strong>
                                    Rp <?= number_format($order->total_bayar ?? 0, 0, ',', '.') ?>
                                </strong>
                            </li>

                            <?php if (($order->pembayaran ?? '') === 'QRIS'): ?>
                                <li>Scan QR Code di aplikasi dompet digital Anda.</li>
                            <?php else: ?>
                                <li>
                                    Transfer ke
                                    <strong>BCA: 1234567890 a/n Annyeong Foodie</strong>
                                </li>
                            <?php endif; ?>

                            <li>Upload bukti pembayaran.</li>
                            <li>Tunggu verifikasi dari tim kami.</li>
                        </ol>
                    </div>

                    <div class="d-flex justify-content-center flex-wrap mt-3">
                        <a href="<?= site_url('checkout/upload/' . ($order->invoice_no ?? '')) ?>"
                           class="btn btn-primary btn-lg mr-2">
                            <i class="fa fa-upload mr-1"></i>
                            Upload Bukti Bayar
                        </a>

                        <a href="<?= site_url('account/orders') ?>"
                           class="btn btn-outline-secondary btn-lg">
                            <i class="fa fa-list mr-1"></i>
                            Riwayat Pesanan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>