<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1>Laporan</h1></div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <div class="card card-warning text-center">
                    <div class="card-body">
                        <i class="fas fa-chart-bar fa-3x mb-3"></i>
                        <h5>Laporan Penjualan</h5>
                        <p class="text-muted">Omzet, diskon, dan laba bersih</p>
                        <a href="<?= site_url('admin/reports/penjualan') ?>" class="btn btn-primary">Lihat Laporan</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-warning text-center">
                    <div class="card-body">
                        <i class="fas fa-industry fa-3x mb-3"></i>
                        <h5>Laporan Produksi</h5>
                        <p class="text-muted">Batch produksi</p>
                        <a href="<?= site_url('admin/reports/produksi') ?>" class="btn btn-info">Lihat Laporan</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-warning text-center">
                    <div class="card-body">
                        <i class="fas fa-boxes fa-3x mb-3"></i>
                        <h5>Laporan Stok Bahan</h5>
                        <p class="text-muted">Kondisi stok bahan baku saat ini</p>
                        <a href="<?= site_url('admin/reports/stok-bahan') ?>" class="btn btn-warning">Lihat Laporan</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-warning text-center">
                    <div class="card-body">
                        <i class="fas fa-credit-card fa-3x mb-3"></i>
                        <h5>Metode Pembayaran</h5>
                        <p class="text-muted">Cash, QRIS, Transfer, Midtrans</p>
                        <a href="<?= site_url('admin/reports/metode') ?>" class="btn btn-success">
                            Lihat Laporan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>