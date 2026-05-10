<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1 class="m-0 text-dark">Dashboard</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item active"><?= date('d F Y') ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- KARTU STATISTIK -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>Rp <?= number_format($penjualanHariIni->total ?? 0) ?></h3>
                        <p>Omzet Hari Ini (<?= $penjualanHariIni->jumlah ?? 0 ?> transaksi)</p>
                    </div>
                    <div class="icon"><i class="fas fa-shopping-cart"></i></div>
                    <a href="<?= site_url('admin/penjualan') ?>" class="small-box-footer">
                        Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?= number_format($produksiHariIni->total ?? 0) ?> porsi</h3>
                        <p>Diproduksi Hari Ini (<?= $produksiHariIni->jumlah ?? 0 ?> batch)</p>
                    </div>
                    <div class="icon"><i class="fas fa-industry"></i></div>
                    <a href="<?= site_url('admin/produksi') ?>" class="small-box-footer">
                        Lihat Produksi <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-<?= $bahanMenipis > 0 ? 'danger' : 'success' ?>">
                    <div class="inner">
                        <h3><?= $bahanMenipis ?></h3>
                        <p>Bahan Baku Menipis</p>
                    </div>
                    <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                    <a href="<?= site_url('admin/bahanbaku') ?>" class="small-box-footer">
                        Cek Stok <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3><?= number_format($totalPelanggan) ?></h3>
                        <p>Total Pelanggan Terdaftar</p>
                    </div>
                    <div class="icon"><i class="fas fa-users"></i></div>
                    <a href="<?= site_url('admin/users') ?>" class="small-box-footer">
                        Kelola User <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- GRAFIK PENJUALAN 7 HARI -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-line mr-1"></i> Penjualan 7 Hari Terakhir</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="grafikPenjualan" height="120"></canvas>
                    </div>
                </div>
            </div>

            <!-- TOP 5 PRODUK -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-trophy mr-1"></i> Top 5 Produk (30 hari)</h3>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <?php if (empty($topProduk)): ?>
                                <li class="list-group-item text-muted text-center">Belum ada data</li>
                            <?php else: ?>
                            <?php foreach ($topProduk as $i => $tp): ?>
                            <li class="list-group-item">
                                <span class="badge badge-primary mr-2">#<?= $i + 1 ?></span>
                                <strong><?= esc($tp->name) ?></strong>
                                <span class="float-right">
                                    <span class="badge badge-info"><?= number_format($tp->total_qty) ?> porsi</span>
                                </span>
                            </li>
                            <?php endforeach ?>
                            <?php endif ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- STOK MENIPIS -->
        <?php if (!empty($stokMenipis)): ?>
        <div class="row">
            <div class="col-12">
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-exclamation-triangle"></i> Peringatan: Stok Produk Menipis
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead><tr><th>Produk</th><th>SKU</th><th>Stok</th><th>Batas</th><th>Aksi</th></tr></thead>
                            <tbody>
                                <?php foreach ($stokMenipis as $s): ?>
                                <tr>
                                    <td><?= esc($s->name) ?></td>
                                    <td><span class="badge badge-secondary"><?= esc($s->sku) ?></span></td>
                                    <td><span class="badge badge-danger"><?= $s->qty ?></span></td>
                                    <td><?= $s->low_stock_threshold ?></td>
                                    <td>
                                        <a href="<?= site_url('admin/produksi/tambah') ?>"
                                           class="btn btn-warning btn-xs">Produksi Sekarang</a>
                                    </td>
                                </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endif ?>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
const ctx = document.getElementById('grafikPenjualan').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [<?= implode(',', array_map(fn($g) => '"' . $g['tanggal'] . '"', $grafikPenjualan)) ?>],
        datasets: [{
            label: 'Omzet (Rp)',
            data: [<?= implode(',', array_column($grafikPenjualan, 'total')) ?>],
            backgroundColor: 'rgba(40, 167, 69, 0.6)',
            borderColor: 'rgba(40, 167, 69, 1)',
            borderWidth: 1,
        }]
    },
    options: {
        scales: {
            y: { beginAtZero: true, ticks: {
                callback: v => 'Rp ' + v.toLocaleString('id-ID')
            }}
        },
        plugins: { legend: { display: false } }
    }
});
</script>
<?= $this->endSection() ?>