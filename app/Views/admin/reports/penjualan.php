<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Laporan Penjualan</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/reports') ?>">Reports</a></li>
                    <li class="breadcrumb-item active">Penjualan</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <!-- Filter -->
        <div class="card">
            <div class="card-body">
                <form method="get" action="<?= site_url('admin/reports/penjualan') ?>" class="form-inline">
                    <label class="mr-2">Dari:</label>
                    <input type="date" name="dari" class="form-control form-control-sm mr-3" value="<?= esc($dari) ?>">
                    <label class="mr-2">Sampai:</label>
                    <input type="date" name="sampai" class="form-control form-control-sm mr-3" value="<?= esc($sampai) ?>">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="<?= site_url('admin/reports/penjualan/pdf?dari='.$dari.'&sampai='.$sampai) ?>" class="btn btn-danger btn-sm ml-2">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>
                </form>
            </div>
        </div>

        <!-- Summary -->
        <div class="row">
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-money-bill"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Omzet</span>
                        <span class="info-box-number">Rp <?= number_format($totalNett) ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-chart-pie"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Laba Kotor</span>
                        <span class="info-box-number">Rp <?= number_format($totalLaba) ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-tag"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Diskon</span>
                        <span class="info-box-number">Rp <?= number_format($totalDiskon) ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-primary"><i class="fas fa-percentage"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Margin Rata-rata</span>
                        <span class="info-box-number">
                            <?= $totalNett > 0 ? round(($totalLaba / $totalNett) * 100, 1) : 0 ?>%
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Data Penjualan per Hari</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-bordered table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>Tanggal</th>
                            <th class="text-center">Transaksi</th>
                            <th class="text-center">Gross</th>
                            <th class="text-center">Ongkir</th>
                            <th class="text-center">Handling</th>
                            <th class="text-center">Diskon</th>
                            <th class="text-center">Nett</th>
                            <th class="text-center">Laba Kotor</th>
                            <th class="text-center">Margin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rows)): ?>
                            <tr>
                                <td colspan="9" class="text-center">Tidak ada data pada periode ini.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($rows as $r): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($r->tanggal)) ?></td>
                                    <td class="text-right"><?= $r->jumlah_transaksi ?></td>
                                    <td class="text-right">Rp <?= number_format($r->subtotal_produk) ?></td>
                                    <td class="text-right text-info">Rp <?= number_format($r->shipping_cost) ?></td>
                                    <td class="text-right text-info">Rp <?= number_format($r->handling_fee) ?></td>
                                    <td class="text-right text-danger">Rp <?= number_format($r->diskon) ?></td>
                                    <td class="text-right">
                                        <strong>Rp <?= number_format($r->grand_total) ?></strong>
                                    </td>
                                    <td class="text-right text-success">
                                        <strong>Rp <?= number_format($r->laba) ?></strong>
                                    </td>
                                    <td class="text-right">
                                        <?php
                                            $margin = $r->grand_total > 0
                                                ? round(($r->laba / $r->grand_total) * 100, 1)
                                                : 0;
                                        ?>
                                        <span class="badge badge-<?= $margin >= 30 ? 'success' : ($margin >= 15 ? 'warning' : 'danger') ?>">
                                            <?= $margin ?>%
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        <?php endif ?>
                    </tbody>
                    <tfoot class="bg-light font-weight-bold text-right">
                        <tr>
                            <th>Total</th>
                            <th><?= number_format($totalTransaksi) ?></th>
                            <th><?= number_format($totalGross) ?></th>
                            <th><?= number_format($totalShipping) ?></th>
                            <th><?= number_format($totalHandling) ?></th>
                            <th>Rp <?= number_format($totalDiskon) ?></th>
                            <th>Rp <?= number_format($totalNett) ?></th>
                            <th>Rp <?= number_format($totalLaba) ?></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>