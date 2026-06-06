<?= $this->extend('themes/indomarket/layout') ?>
<?= $this->section('content') ?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7 text-center mb-4">
                <i class="fa fa-map-marker fa-3x text-primary mb-3"></i>
                <h3 class="font-weight-bold">Lacak Pesanan</h3>
                <p class="text-muted">Masukkan nomor invoice untuk melihat status pesanan Anda</p>
            </div>
            <div class="col-md-7">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form action="<?= site_url('track/search') ?>" method="get">
                            <div class="form-group">
                                <label class="font-weight-bold">Nomor Invoice</label>
                                <input type="text" name="invoice_no" class="form-control form-control-lg"
                                       placeholder="contoh: AES-20260512-001" required value="<?= esc($invoice_no ?? '') ?>">
                            </div>
                            <button type="submit" class="btn btn-primary btn-block btn-lg">
                                <i class="fa fa-search mr-1"></i> Lacak Pesanan
                            </button>
                        </form>
                    </div>
                </div>

                <div class="mt-3 text-center">
                    <p class="text-muted small">Nomor invoice dikirim ke email Anda saat membuat pesanan.</p>
                    <?php if ($auth->loggedIn()): ?>
                        <a href="<?= site_url('account/orders') ?>">Lihat riwayat pesanan saya</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>