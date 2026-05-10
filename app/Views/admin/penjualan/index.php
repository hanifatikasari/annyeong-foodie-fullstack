<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1>Data Penjualan</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Penjualan</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <?= view('admin/shared/flash_message') ?>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Riwayat Transaksi</h3>
                <div class="card-tools d-flex">
                    <form action="<?= site_url('admin/penjualan') ?>" method="get" class="form-inline mr-2">
                        <div class="input-group input-group-sm">
                            <input type="text" name="table_search" class="form-control"
                                   placeholder="Cari invoice..." value="<?= esc($keyword ?? '') ?>">
                            <div class="input-group-append">
                                <button class="btn btn-default"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                    <a href="<?= site_url('admin/penjualan/create') ?>" class="btn btn-success btn-sm">
                        <i class="fas fa-plus"></i> Transaksi Baru
                    </a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-bordered">
                    <thead class="bg-light">
                        <tr>
                            <th>Invoice</th>
                            <th class="text-right">Total</th>
                            <th class="text-right">Diskon</th>
                            <th class="text-right">Bayar</th>
                            <th>Metode</th>
                            <th>Kasir</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($penjualan)): ?>
                            <tr><td colspan="8" class="text-center">Belum ada data penjualan.</td></tr>
                        <?php else: ?>
                        <?php foreach ($penjualan as $p): ?>
                        <tr>
                            <td><strong><?= esc($p->invoice_no) ?></strong></td>
                            <td class="text-right">Rp <?= number_format($p->total_harga) ?></td>
                            <td class="text-right text-danger">
                                <?= $p->diskon > 0 ? 'Rp ' . number_format($p->diskon) : '-' ?>
                            </td>
                            <td class="text-right"><strong>Rp <?= number_format($p->total_bayar) ?></strong></td>
                            <td>
                                <span class="badge badge-<?= $p->pembayaran === 'Cash' ? 'success' : ($p->pembayaran === 'QRIS' ? 'info' : 'primary') ?>">
                                    <?= $p->pembayaran ?>
                                </span>
                            </td>
                            <td><?= esc(($p->first_name ?? '') . ' ' . ($p->last_name ?? '')) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($p->created_at)) ?></td>
                            <td>
                                <a href="<?= site_url('admin/penjualan/show/' . $p->id) ?>"
                                   class="btn btn-info btn-xs">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        <?php endforeach ?>
                        <?php endif ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">
                <div class="float-right">
                    <?= $pager->links('bootstrap', 'bootstrap') ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>