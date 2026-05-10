<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1><?= esc($penjualan->invoice_no) ?></h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/penjualan') ?>">Penjualan</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h3 class="card-title">Info Transaksi</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr><td>Invoice</td><td><strong><?= esc($penjualan->invoice_no) ?></strong></td></tr>
                            <tr><td>Tanggal</td><td><?= date('d/m/Y H:i', strtotime($penjualan->created_at)) ?></td></tr>
                            <tr><td>Kasir</td><td><?= esc(($penjualan->first_name ?? '') . ' ' . ($penjualan->last_name ?? '')) ?></td></tr>
                            <tr><td>Metode</td><td>
                                <span class="badge badge-primary"><?= $penjualan->pembayaran ?></span>
                            </td></tr>
                            <tr class="border-top"><td>Subtotal</td><td>Rp <?= number_format($penjualan->total_harga) ?></td></tr>
                            <tr><td>Diskon</td><td class="text-danger">- Rp <?= number_format($penjualan->diskon) ?></td></tr>
                            <tr><td><strong>Total Bayar</strong></td>
                                <td><strong>Rp <?= number_format($penjualan->total_bayar) ?></strong></td></tr>
                            <tr><td>Uang Diterima</td><td>Rp <?= number_format($penjualan->uang_diterima) ?></td></tr>
                            <tr><td>Kembalian</td>
                                <td class="text-success"><strong>
                                    Rp <?= number_format(max(0, $penjualan->uang_diterima - $penjualan->total_bayar)) ?>
                                </strong></td></tr>
                        </table>
                    </div>
                </div>
                <a href="<?= site_url('admin/penjualan') ?>" class="btn btn-secondary btn-block">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Detail Item</h3></div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-right">Harga Jual</th>
                                    <th class="text-right">HPP</th>
                                    <th class="text-right">Subtotal</th>
                                    <th class="text-right">Laba</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $totalLaba = 0;
                                foreach ($details as $d):
                                    $laba = ($d->selling_price - $d->hpp_price) * $d->qty;
                                    $totalLaba += $laba;
                                ?>
                                <tr>
                                    <td>
                                        <strong><?= esc($d->nama_produk) ?></strong><br>
                                        <small class="badge badge-secondary"><?= esc($d->sku) ?></small>
                                    </td>
                                    <td class="text-center"><?= $d->qty ?></td>
                                    <td class="text-right">Rp <?= number_format($d->selling_price) ?></td>
                                    <td class="text-right text-muted">Rp <?= number_format($d->hpp_price) ?></td>
                                    <td class="text-right"><strong>Rp <?= number_format($d->subtotal) ?></strong></td>
                                    <td class="text-right text-<?= $laba >= 0 ? 'success' : 'danger' ?>">
                                        Rp <?= number_format($laba) ?>
                                    </td>
                                </tr>
                                <?php endforeach ?>
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <th colspan="4" class="text-right">Total</th>
                                    <th class="text-right">Rp <?= number_format($penjualan->total_harga) ?></th>
                                    <th class="text-right text-success"><strong>Rp <?= number_format($totalLaba) ?></strong></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>