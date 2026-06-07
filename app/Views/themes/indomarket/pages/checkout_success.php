<?= $this->extend('themes/indomarket/layout') ?>
<?= $this->section('content') ?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <?php
                $isMidtrans   = ($order->pembayaran ?? '') === 'Midtrans';
                $isLunas      = ($order->payment_status ?? '') === 'lunas';
                $isPending    = in_array($order->order_status ?? '', ['pending_payment', 'pending_verification']);
                $isCancelled  = ($order->order_status ?? '') === 'cancelled';

                // Ambil flash messages
                $flashSuccess = session()->getFlashdata('success');
                $flashInfo    = session()->getFlashdata('info');
                $flashWarning = session()->getFlashdata('warning');
                $flashError   = session()->getFlashdata('error');
                ?>

                <div class="card shadow-sm">
                    <div class="card-body text-center p-5">

                        <?php if ($isCancelled): ?>
                            <!-- ===== Status: DIBATALKAN ===== -->
                            <div class="text-danger mb-3">
                                <i class="fa fa-times-circle fa-5x"></i>
                            </div>
                            <h3 class="font-weight-bold text-danger">Pembayaran Gagal</h3>
                            <p class="text-muted">Pembayaran untuk pesanan ini gagal, kadaluarsa, atau dibatalkan.</p>

                        <?php elseif ($isMidtrans && $isLunas): ?>
                            <!-- ===== Status: MIDTRANS LUNAS ===== -->
                            <div class="text-success mb-3">
                                <i class="fa fa-check-circle fa-5x"></i>
                            </div>
                            <h3 class="font-weight-bold">Pembayaran Berhasil!</h3>
                            <p class="text-muted">Pesanan Anda sudah dikonfirmasi dan sedang diproses.</p>

                        <?php elseif ($isMidtrans && $isPending): ?>
                            <!-- ===== Status: MIDTRANS PENDING ===== -->
                            <div class="text-warning mb-3">
                                <i class="fa fa-clock-o fa-5x"></i>
                            </div>
                            <h3 class="font-weight-bold">Pesanan Berhasil Dibuat!</h3>
                            <p class="text-muted">
                                Pembayaran Anda sedang diverifikasi oleh sistem.
                                Jika Anda sudah membayar, status akan otomatis diperbarui.
                            </p>

                        <?php else: ?>
                            <!-- ===== Status: MANUAL PAYMENT / DEFAULT ===== -->
                            <div class="text-success mb-3">
                                <i class="fa fa-check-circle fa-5x"></i>
                            </div>
                            <h3 class="font-weight-bold">Pesanan Berhasil Dibuat!</h3>

                        <?php endif; ?>

                        <!-- Flash messages -->
                        <?php if ($flashSuccess): ?>
                            <div class="alert alert-success mt-3"><?= $flashSuccess ?></div>
                        <?php endif; ?>
                        <?php if ($flashInfo): ?>
                            <div class="alert alert-info mt-3"><?= $flashInfo ?></div>
                        <?php endif; ?>
                        <?php if ($flashWarning): ?>
                            <div class="alert alert-warning mt-3"><?= $flashWarning ?></div>
                        <?php endif; ?>

                        <!-- Info order -->
                        <div class="alert alert-light border mt-4 text-left">
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted d-block">Nomor Invoice</small>
                                    <strong><?= esc($order->invoice_no ?? '-') ?></strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Total Pembayaran</small>
                                    <strong>Rp <?= number_format($order->total_bayar ?? 0) ?></strong>
                                </div>
                                <div class="col-6 mt-2">
                                    <small class="text-muted d-block">Metode Bayar</small>
                                    <strong><?= esc($order->pembayaran ?? '-') ?></strong>
                                </div>
                                <div class="col-6 mt-2">
                                    <small class="text-muted d-block">Status</small>
                                    <?php
                                    $statusLabel = [
                                        'pending_payment'      => '<span class="badge badge-warning">Menunggu Pembayaran</span>',
                                        'pending_verification' => '<span class="badge badge-info">Verifikasi Pembayaran</span>',
                                        'processing'           => '<span class="badge badge-primary">Diproses</span>',
                                        'verified'             => '<span class="badge badge-success">Terverifikasi</span>',
                                        'completed'            => '<span class="badge badge-success">Selesai</span>',
                                        'cancelled'            => '<span class="badge badge-danger">Dibatalkan</span>',
                                    ];
                                    echo $statusLabel[$order->order_status ?? ''] ?? '<span class="badge badge-secondary">' . esc($order->order_status ?? '-') . '</span>';
                                    ?>
                                </div>
                            </div>
                        </div>

                        <!-- Instruksi berdasarkan metode pembayaran -->
                        <?php if (!$isMidtrans && !$isCancelled): ?>
                            <!-- MANUAL PAYMENT: Tampilkan instruksi upload -->
                            <div class="alert alert-warning text-left mt-3">
                                <h6><i class="fa fa-info-circle mr-1"></i>Langkah Selanjutnya:</h6>
                                <ol class="mb-0 pl-4">
                                    <li>
                                        Lakukan pembayaran sebesar
                                        <strong>Rp <?= number_format($order->total_bayar ?? 0) ?></strong>
                                    </li>
                                    <?php if (($order->pembayaran ?? '') === 'QRIS'): ?>
                                        <li>Scan QR Code di aplikasi dompet digital Anda.</li>
                                    <?php else: ?>
                                        <li>Transfer ke <strong>BCA: 1234567890 a/n Annyeong Foodie</strong></li>
                                    <?php endif; ?>
                                    <li>Klik tombol <strong>"Upload Bukti Bayar"</strong> di bawah.</li>
                                    <li>Tunggu verifikasi dari tim kami (1-3 jam kerja).</li>
                                </ol>
                            </div>
                        <?php elseif ($isMidtrans && !$isLunas && !$isCancelled): ?>
                            <!-- MIDTRANS PENDING -->
                            <div class="alert alert-info text-left mt-3">
                                <h6><i class="fa fa-info-circle mr-1"></i>Informasi:</h6>
                                <ul class="mb-0 pl-4">
                                    <li>Jika Anda sudah membayar, status akan otomatis diperbarui.</li>
                                    <li>Pembayaran Virtual Account berlaku hingga <strong>24 jam</strong>.</li>
                                    <li>Anda akan mendapat email konfirmasi setelah pembayaran berhasil.</li>
                                    <li>Cek status pesanan di <strong>"Riwayat Pesanan"</strong>.</li>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <!-- Tombol aksi -->
                        <div class="d-flex justify-content-center flex-wrap mt-4" style="gap:10px;">

                            <?php if (!$isMidtrans && !$isCancelled && ($order->order_status ?? '') === 'pending_payment'): ?>
                                <!-- Tombol upload untuk pembayaran manual -->
                                <a href="<?= site_url('checkout/upload/' . ($order->invoice_no ?? '')) ?>"
                                   class="btn btn-primary btn-lg">
                                    <i class="fa fa-upload mr-1"></i>Upload Bukti Bayar
                                </a>
                            <?php endif; ?>

                            <?php if ($isMidtrans && ($order->order_status ?? '') === 'pending_payment' && !empty($order->snap_token)): ?>
                                <!-- Tombol bayar ulang jika Midtrans masih pending -->
                                <a href="<?= site_url('checkout/pay/' . ($order->invoice_no ?? '')) ?>"
                                   class="btn btn-warning btn-lg">
                                    <i class="fa fa-credit-card mr-1"></i>Selesaikan Pembayaran
                                </a>
                            <?php endif; ?>

                            <a href="<?= site_url('account/orders') ?>"
                               class="btn btn-outline-secondary btn-lg">
                                <i class="fa fa-list mr-1"></i>Riwayat Pesanan
                            </a>

                            <a href="<?= site_url('products') ?>"
                               class="btn btn-outline-primary btn-lg">
                                <i class="fa fa-shopping-bag mr-1"></i>Lanjut Belanja
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?> 