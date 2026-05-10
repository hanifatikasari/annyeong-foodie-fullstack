<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1>Proses Produksi</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Produksi</li>
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
                <h3 class="card-title">Riwayat Produksi</h3>
                <div class="card-tools d-flex">
                    <form action="<?= site_url('admin/produksi') ?>" method="get" class="form-inline mr-2">
                        <div class="input-group input-group-sm">
                            <input type="text" name="table_search" class="form-control"
                                   placeholder="Cari kode/produk..." value="<?= esc($keyword ?? '') ?>">
                            <div class="input-group-append">
                                <button class="btn btn-default"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                    <a href="<?= site_url('admin/produksi/tambah') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Catat Produksi
                    </a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-bordered">
                    <thead class="bg-light">
                        <tr>
                            <th>Kode</th>
                            <th>Produk</th>
                            <th class="text-center">Qty</th>
                            <th>Tanggal</th>
                            <th>Status QC</th>
                            <th>Operator</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($produksi)): ?>
                            <tr><td colspan="7" class="text-center">Belum ada data produksi.</td></tr>
                        <?php else: ?>
                        <?php foreach ($produksi as $p): ?>
                        <tr>
                            <td><strong><?= esc($p->kode_produksi) ?></strong></td>
                            <td>
                                <span class="badge badge-secondary"><?= esc($p->sku) ?></span>
                                <?= esc($p->nama_produk) ?>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-info"><?= number_format($p->qty_hasil) ?> porsi</span>
                            </td>
                            <td><?= date('d/m/Y', strtotime($p->tanggal_produksi)) ?></td>
                            <td>
                                <span class="badge badge-<?= $p->status_qc === 'Lolos' ? 'success' : 'danger' ?>">
                                    <?= $p->status_qc ?>
                                </span>
                            </td>
                            <td><?= esc(($p->first_name ?? '') . ' ' . ($p->last_name ?? '')) ?></td>
                            <td>
                                <a href="<?= site_url('admin/produksi/show/' . $p->id) ?>"
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