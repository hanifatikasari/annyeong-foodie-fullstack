<?= $this->extend('themes/indomarket/layout') ?>
<?= $this->section('content') ?>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <!-- Info Order -->
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-primary text-white border-0">
                        <h5 class="mb-0 font-weight-bold">
                            <i class="fa fa-credit-card mr-2"></i>Selesaikan Pembayaran
                        </h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted">Invoice</td>
                                <td class="font-weight-bold text-right">
                                    <?= esc($order->invoice_no) ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Total Pembayaran</td>
                                <td class="font-weight-bold text-right text-primary h5 mb-0">
                                    Rp <?= number_format($order->total_bayar) ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Info Midtrans -->
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body text-center py-4">

                        <!-- Loading state (ditampilkan saat Snap belum terbuka) -->
                        <div id="loadingState">
                            <div class="spinner-border text-primary mb-3" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <p class="text-muted mb-0">Memuat halaman pembayaran...</p>
                        </div>

                        <!-- Ready state (ditampilkan setelah Snap siap) -->
                        <div id="readyState" style="display:none;">
                            <i class="fa fa-shield fa-3x text-success mb-3"></i>
                            <h6 class="font-weight-bold">Halaman Pembayaran Siap</h6>
                            <p class="text-muted small">
                                Pilih metode pembayaran favorit Anda: Kartu Kredit,
                                GoPay, OVO, Dana, ShopeePay, Virtual Account Bank, dan lainnya.
                            </p>

                            <!-- Tombol manual jika popup tidak terbuka otomatis -->
                            <button id="pay-button" class="btn btn-primary btn-lg px-5 mt-2">
                                <i class="fa fa-credit-card mr-2"></i>Pilih Metode Pembayaran
                            </button>
                        </div>

                        <!-- Error state -->
                        <div id="errorState" style="display:none;" class="text-danger">
                            <i class="fa fa-times-circle fa-3x mb-3"></i>
                            <h6>Pembayaran Tidak Dapat Dimuat</h6>
                            <p class="small">Silakan refresh halaman atau hubungi kami.</p>
                            <a href="<?= site_url('checkout/pay/' . $order->invoice_no) ?>"
                               class="btn btn-outline-primary btn-sm">
                                <i class="fa fa-refresh mr-1"></i>Coba Lagi
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Info keamanan -->
                <div class="text-center text-muted small">
                    <i class="fa fa-lock mr-1"></i>
                    Pembayaran diamankan oleh
                    <strong>Midtrans</strong> — payment gateway terpercaya
                    <br>
                    <a href="<?= site_url('account/orders') ?>" class="text-muted mt-2 d-inline-block">
                        <i class="fa fa-arrow-left mr-1"></i>Batalkan dan kembali ke pesanan
                    </a>
                </div>

            </div>
        </div>
    </div>
</section>

<?php
// Tentukan URL Snap.js berdasarkan mode (sandbox/production)
$snapJsUrl = $isProduction
    ? 'https://app.midtrans.com/snap/snap.js'
    : 'https://app.sandbox.midtrans.com/snap/snap.js';
?>

<!-- Load Midtrans Snap.js -->
<script
    src="<?= $snapJsUrl ?>"
    data-client-key="<?= esc($midtransClientKey) ?>">
</script>

<script>
(function() {
    var snapToken    = '<?= esc($order->snap_token) ?>';
    var returnUrl    = '<?= site_url('checkout/return/' . $order->invoice_no) ?>';
    var ordersUrl    = '<?= site_url('account/orders') ?>';
    var isPopupOpen  = false;

    function openSnapPayment() {
        if (!snapToken) {
            document.getElementById('loadingState').style.display = 'none';
            document.getElementById('errorState').style.display   = 'block';
            return;
        }

        snap.pay(snapToken, {

            // Pembayaran berhasil (untuk metode yang langsung settled)
            onSuccess: function(result) {
                console.log('[Midtrans] Success:', result);
                window.location.href = returnUrl + '?status=success';
            },

            // Menunggu pembayaran (VA, dll yang belum settled)
            onPending: function(result) {
                console.log('[Midtrans] Pending:', result);
                window.location.href = returnUrl + '?status=pending';
            },

            // Error saat proses pembayaran
            onError: function(result) {
                console.error('[Midtrans] Error:', result);
                window.location.href = returnUrl + '?status=error';
            },

            // User menutup popup tanpa membayar
            onClose: function() {
                console.log('[Midtrans] Popup ditutup user');
                isPopupOpen = false;

                // Tampilkan tombol manual agar user bisa buka lagi
                document.getElementById('loadingState').style.display = 'none';
                document.getElementById('readyState').style.display   = 'block';
            },
        });
    }

    // Auto-trigger saat halaman selesai dimuat
    window.addEventListener('load', function() {
        setTimeout(openSnapPayment, 800);
    });

    // Tombol manual (jika popup tidak terbuka otomatis atau user tutup)
    document.addEventListener('DOMContentLoaded', function() {
        var payBtn = document.getElementById('pay-button');
        if (payBtn) {
            payBtn.addEventListener('click', function() {
                openSnapPayment();
            });
        }
    });
})();
</script>

<?= $this->endSection() ?>