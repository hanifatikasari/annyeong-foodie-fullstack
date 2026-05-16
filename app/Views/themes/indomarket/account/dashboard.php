<?= $this->extend('themes/indomarket/layout') ?>
<?= $this->section('content') ?>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <!-- Sidebar Account -->
            <div class="col-lg-3 mb-4">
                <?= $this->include('themes/indomarket/account/_sidebar') ?>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="font-weight-bold">
                            Selamat Datang, <?= esc($currentUser->first_name) ?>! 👋
                        </h5>
                        <p class="text-muted mb-0">Kelola pesanan dan profil Anda dari sini.</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm text-center h-100">
                            <div class="card-body">
                                <i class="fa fa-shopping-bag fa-3x text-primary mb-2"></i>
                                <h4 class="font-weight-bold"><?= count($orders) ?></h4>
                                <p class="text-muted mb-2">Total Pesanan</p>
                                <a href="<?= site_url('account/orders') ?>" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm text-center h-100">
                            <div class="card-body">
                                <i class="fa fa-clock-o fa-3x text-warning mb-2"></i>
                                <h4 class="font-weight-bold">
                                    <?= count(array_filter($orders, fn($o) => in_array($o->order_status, ['pending_payment','pending_verification']))) ?>
                                </h4>
                                <p class="text-muted mb-2">Menunggu Verifikasi</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm text-center h-100">
                            <div class="card-body">
                                <i class="fa fa-check-circle fa-3x text-success mb-2"></i>
                                <h4 class="font-weight-bold">
                                    <?= count(array_filter($orders, fn($o) => $o->order_status === 'completed')) ?>
                                </h4>
                                <p class="text-muted mb-2">Pesanan Selesai</p>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($recentOrder): ?>
                    <div class="card shadow-sm mt-3">
                        <div class="card-header"><h6 class="mb-0">Pesanan Terbaru</h6></div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?= esc($recentOrder->invoice_no) ?></strong><br>
                                    <small class="text-muted"><?= date('d M Y', strtotime($recentOrder->created_at)) ?></small>
                                </div>
                                <div>
                                    <span class="badge status-badge status-<?= $recentOrder->order_status ?>">
                                        <?= ucwords(str_replace('_', ' ', $recentOrder->order_status)) ?>
                                    </span>
                                </div>
                                <div>
                                    <strong>Rp <?= number_format($recentOrder->total_bayar) ?></strong>
                                </div>
                             <a href="<?= site_url('account/orders/detail/' . $recentOrder->id) ?>" class="btn btn-sm btn-outline-primary">
                                Detail
                            </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>