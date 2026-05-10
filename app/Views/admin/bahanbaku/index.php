<?= $this->extend('admin/layout') ?> 
<?= $this->section('content') ?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $title; ?></h1>
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="<?= site_url('admin/bahanbaku/tambah') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Bahan Baku
        </a>
        
        <div class="card-tools">
            <form action="<?= site_url('admin/bahanbaku') ?>" method="get" class="form-inline">
                <div class="input-group input-group-sm" style="width: 250px;">
                    <input type="text" name="table_search" class="form-control" placeholder="Cari kode atau nama..." value="<?= $keyword ?? '' ?>">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-default">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover table-bordered text-nowrap mb-0">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 50px">No</th>
                        <th>Kode</th>
                        <th>Nama Bahan</th>
                        <th>Satuan</th>
                        <th>Stok Sekarang</th>
                        <th>Tgl Terdaftar</th>
                        <th style="width: 150px" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($bahan)) : ?>
                        <?php 
                        $currentPage = $pager->getCurrentPage('bootstrap');
                        $no = ($currentPage - 1) * 10 + 1; // Angka 10 sesuaikan dengan perPage di Controller
                        foreach ($bahan as $b) : 
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><span class="badge badge-secondary"><?= $b->kode_bahan; ?></span></td>
                            <td><strong><?= $b->nama_bahan; ?></strong></td>
                            <td><?= $b->satuan; ?></td>
                            <td>
                                <span class="badge <?= ($b->stok_sekarang <= $b->stok_minimal) ? 'badge-danger' : 'badge-success' ?>">
                                    <?= (float)$b->stok_sekarang; ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y', strtotime($b->created_at)); ?></td>
                            <td class="text-center">
                                <a href="<?= site_url('admin/bahanbaku/edit/'.$b->id) ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="<?= site_url('admin/bahanbaku/hapus/'.$b->id) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus bahan ini?')">
                                    <i class="fas fa-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="7" class="text-center">Data bahan baku kosong.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="card-footer clearfix bg-white">
            <div class="float-right">
                <?= $pager->links('bootstrap', 'bootstrap') ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>