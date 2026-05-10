<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1>Laporan Stok Bahan Baku</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/reports') ?>">Reports</a></li>
                    <li class="breadcrumb-item active">Stok Bahan</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <p class="text-muted">Per: <strong><?= date('d F Y H:i') ?></strong></p>

        <div class="card">
            <div class="card-body table-responsive p-0">
                <table class="table table-bordered table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>Kode</th>
                            <th>Nama Bahan</th>
                            <th>Kategori</th>
                            <th class="text-right">Stok Sekarang</th>
                            <th>Satuan</th>
                            <th class="text-right">Stok Minimal</th>
                            <th>Status</th>
                            <th class="text-right">Nilai Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $totalNilai = 0;
                        foreach ($bahan as $b):
                            $nilaiStok = $b->stok_sekarang * $b->harga_beli_satuan;
                            $totalNilai += $nilaiStok;
                            $status = ($b->stok_sekarang <= $b->stok_minimal)
                                ? ['label' => 'MENIPIS', 'class' => 'danger']
                                : ['label' => 'Aman', 'class' => 'success'];
                        ?>
                        <tr class="<?= $status['class'] === 'danger' ? 'table-danger' : '' ?>">
                            <td><span class="badge badge-secondary"><?= esc($b->kode_bahan) ?></span></td>
                            <td><strong><?= esc($b->nama_bahan) ?></strong></td>
                            <td><?= esc($b->kategori ?? '-') ?></td>
                            <td class="text-right"><?= (float) $b->stok_sekarang ?></td>
                            <td><?= esc($b->satuan) ?></td>
                            <td class="text-right"><?= (float) $b->stok_minimal ?></td>
                            <td><span class="badge badge-<?= $status['class'] ?>"><?= $status['label'] ?></span></td>
                            <td class="text-right">Rp <?= number_format($nilaiStok) ?></td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <th colspan="7" class="text-right">Total Nilai Stok</th>
                            <th class="text-right"><strong>Rp <?= number_format($totalNilai) ?></strong></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>