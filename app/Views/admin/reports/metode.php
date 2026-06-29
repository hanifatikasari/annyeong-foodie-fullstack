<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>

<section class="content-header">
    <div class="container-fluid">
        <h1>Laporan Metode Pembayaran</h1>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Metode Pembayaran</h3>
            </div>

            <div class="card-body table-responsive">
                <a href="<?= site_url('admin/reports/metode/pdf?dari='.$dari.'&sampai='.$sampai) ?>" class="btn btn-danger btn-sm mb-3">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr class="text-center">
                            <th>Metode</th>
                            <th>Total Transaksi</th>
                            <th>Total Omzet</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $r): ?>
                            <tr>
                                <td><?= esc($r->pembayaran) ?></td>
                                <td class="text-center"><?= number_format($r->jumlah_transaksi) ?></td>
                                <td class="text-right">Rp <?= number_format($r->total_omzet) ?></td>
                                <td class="text-center">
                                    <?php $persen = $totalOmzet > 0 ? ($r->total_omzet / $totalOmzet) * 100 : 0; ?>
                                    <?= number_format($persen, 1) ?>%
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold">
                            <td>TOTAL</td>
                            <td class="text-center"><?= number_format($totalTransaksi) ?></td>
                            <td class="text-right">Rp <?= number_format($totalOmzet) ?></td>
                            <td class="text-center">100%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>