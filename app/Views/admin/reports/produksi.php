<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1>Laporan Produksi</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/reports') ?>">Reports</a></li>
                    <li class="breadcrumb-item active">Produksi</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <form method="get" action="<?= site_url('admin/reports/produksi') ?>" class="form-inline">
                    <label class="mr-2">Dari:</label>
                    <input type="date" name="dari" class="form-control form-control-sm mr-3"
                           value="<?= esc($dari) ?>">
                    <label class="mr-2">Sampai:</label>
                    <input type="date" name="sampai" class="form-control form-control-sm mr-3"
                           value="<?= esc($sampai) ?>">
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-box"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Produksi</span>
                        <span class="info-box-number"><?= number_format($totalQty) ?> porsi</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-coins"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Biaya Produksi</span>
                        <span class="info-box-number">Rp <?= number_format($totalHpp) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3 class="card-title">Detail Produksi</h3></div>
            <div class="card-body table-responsive p-0">
                <table class="table table-bordered table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>Kode</th>
                            <th>Produk</th>
                            <th class="text-center">Qty</th>
                            <th>Tanggal</th>
                            <th>Status QC</th>
                            <th class="text-right">Biaya Batch</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rows)): ?>
                            <tr><td colspan="6" class="text-center">Tidak ada data.</td></tr>
                        <?php else: ?>
                        <?php foreach ($rows as $r): ?>
                        <tr>
                            <td><?= esc($r->kode_produksi) ?></td>
                            <td><?= esc($r->nama_produk) ?></td>
                            <td class="text-center"><?= number_format($r->qty_hasil) ?></td>
                            <td><?= date('d/m/Y', strtotime($r->tanggal_produksi)) ?></td>
                            <td><span class="badge badge-<?= $r->status_qc === 'Lolos' ? 'success' : 'danger' ?>">
                                <?= $r->status_qc ?>
                            </span></td>
                            <td class="text-right">Rp <?= number_format($r->qty_hasil * $r->hpp_total) ?></td>
                        </tr>
                        <?php endforeach ?>
                        <?php endif ?>
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <th colspan="5" class="text-right">Total</th>
                            <th class="text-right">Rp <?= number_format($totalHpp) ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>