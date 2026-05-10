<?= $this->extend('admin/layout') ?> 
<?= $this->section('content') ?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $title; ?></h1>
    
    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    
    <a href="#tambahStok" class="btn btn-primary mb-3">
        <i class="fas fa-plus"></i> Tambah
    </a>
    
    <div class="card mb-4" id="bagianRiwayat">
        <div class="card-header font-weight-bold d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-table mr-1"></i> Riwayat Belanja Bahan Baku
            </div>
            <div class="card-tools">
                <form action="<?= site_url('admin/stokmasuk') ?>" method="get" class="form-inline">
                    <select name="perPage" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                        <option value="5" <?= $perPage == 5 ? 'selected' : '' ?>>5</option>
                        <option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>10</option>
                        <option value="25" <?= $perPage == 25 ? 'selected' : '' ?>>25</option>
                    </select>
                    
                    <div class="input-group input-group-sm" style="width: 200px;">
                        <input type="text" name="table_search" class="form-control" placeholder="Cari bahan/supplier..." value="<?= $keyword ?? '' ?>">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-bordered text-nowrap mb-0">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 50px">No</th>
                        <th>Tanggal</th>
                        <th>Nama Bahan</th>
                        <th>Qty Beli</th>
                        <th>Isi per Satuan</th>
                        <th>Total Masuk</th>
                        <th>Total Harga</th>
                        <th>Supplier</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($stok) && count($stok) > 0) : ?>
                        <?php 
                        $currentPage = $pager->getCurrentPage('bootstrap');
                        $no = ($currentPage - 1) * $perPage + 1;
                        foreach ($stok as $s) : 
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= date('d/m/Y', strtotime($s->tanggal_masuk)); ?></td>
                            <td><strong><?= $s->nama_bahan; ?></strong></td>
                            <td><?= $s->qty; ?></td>
                            <td><?= number_format($s->isi_per_satuan); ?></td>
                            <td>
                                <span class="badge badge-info">
                                    <?= number_format($s->qty * $s->isi_per_satuan); ?> <?= $s->satuan; ?>
                                </span>
                            </td>
                            <td>Rp <?= number_format($s->total_harga, 0, ',', '.'); ?></td>
                            <td><?= $s->nama_supplier ?? '-'; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="8" class="text-center">Data tidak ditemukan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="card-footer clearfix bg-white">
            <div class="float-left">
                <?php 
                    $total = $pager->getTotal('bootstrap');
                    $start = $total > 0 ? ($currentPage - 1) * $perPage + 1 : 0;
                    $end = min($currentPage * $perPage, $total);
                ?>
                <p class="text-sm text-muted mb-0">
                    Showing <?= $start ?> to <?= $end ?> of <?= $total ?> entries
                </p>
            </div>
            <div class="float-right">
                <?= $pager->links('bootstrap', 'bootstrap') ?>
            </div>
        </div>
    </div>
</div>

<?= $this->include('admin/stokmasuk/tambah') ?>

<?= $this->endSection() ?>