<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Detail Produksi</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="<?= site_url('admin/dashboard') ?>">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="<?= site_url('admin/produksi') ?>">Produksi</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <?= esc($produksi->kode_produksi) ?>
                    </li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        <?= view('admin/shared/flash_message') ?>

        <!-- Informasi Header Produksi -->
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-industry"></i>
                    Informasi Produksi
                </h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="25%">Kode Produksi</th>
                        <td>
                            <span class="badge badge-primary">
                                <?= esc($produksi->kode_produksi) ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Produk</th>
                        <td>
                            <strong><?= esc($produksi->nama_produk) ?></strong>
                            <br>
                            <small class="text-muted">
                                SKU: <?= esc($produksi->sku) ?>
                            </small>
                        </td>
                    </tr>
                    <tr>
                        <th>Jumlah Produksi</th>
                        <td>
                            <span class="badge badge-info">
                                <?= number_format($produksi->qty_hasil) ?> porsi
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Tanggal Produksi</th>
                        <td>
                            <?= date('d F Y', strtotime($produksi->tanggal_produksi)) ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Status QC</th>
                        <td>
                            <span class="badge badge-<?= $produksi->status_qc === 'Lolos' ? 'success' : 'danger' ?>">
                                <?= esc($produksi->status_qc) ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Operator</th>
                        <td>
                            <?= esc(trim(($produksi->first_name ?? '') . ' ' . ($produksi->last_name ?? ''))) ?: '-' ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Catatan</th>
                        <td>
                            <?= nl2br(esc($produksi->catatan ?: '-')) ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Detail Bahan Baku -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i>
                    Detail Pemakaian Bahan Baku
                </h3>
            </div>

            <div class="card-body table-responsive p-0">
                <?php if (empty($details)): ?>
                    <div class="alert alert-warning m-3">
                        Tidak ada detail bahan baku.
                    </div>
                <?php else: ?>
                    <table class="table table-hover table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Bahan</th>
                                <th class="text-center">Qty Digunakan</th>
                                <th class="text-center">Satuan</th>
                                <th class="text-right">Harga/Satuan</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $totalBiaya = 0;
                            foreach ($details as $d):
                                $subtotal = $d->qty_digunakan * $d->harga_beli_satuan;
                                $totalBiaya += $subtotal;
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= esc($d->nama_bahan) ?></strong></td>
                                <td class="text-center">
                                    <?= rtrim(rtrim(number_format($d->qty_digunakan, 3, '.', ''), '0'), '.') ?>
                                </td>
                                <td class="text-center"><?= esc($d->satuan) ?></td>
                                <td class="text-right">
                                    Rp <?= number_format($d->harga_beli_satuan) ?>
                                </td>
                                <td class="text-right">
                                    <strong>Rp <?= number_format($subtotal) ?></strong>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <th colspan="5" class="text-right">
                                    Total Biaya Produksi
                                </th>
                                <th class="text-right text-danger">
                                    Rp <?= number_format($totalBiaya) ?>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                <?php endif; ?>
            </div>

            <div class="card-footer">
                <a href="<?= site_url('admin/produksi') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Riwayat Produksi
                </a>
            </div>
        </div>

    </div>
</section>

<?= $this->endSection() ?>