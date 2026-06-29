<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <a href="<?= site_url('admin/shipping_area/create') ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Tambah Area
                        </a>
                    </div>
                    <div class="col-md-6">
                        <form method="get">
                            <div class="input-group">
                                <input type="text" name="table_search" value="<?= esc($keyword) ?>" class="form-control" placeholder="Cari Area">
                                <input type="hidden" name="perPage" value="<?= $perPage ?>">
                                <div class="input-group-append">
                                    <button class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th width="60">No</th>
                            <th>Area</th>
                            <th>Ongkir</th>
                            <th>Estimasi</th>
                            <th>Status</th>
                            <th width="170">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1 + (($pager->getCurrentPage() - 1) * $perPage);
                        foreach ($shippingAreas as $row):
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= esc($row->area_name) ?></td>
                                <td>Rp <?= number_format($row->ongkir, 0, ',', '.') ?></td>
                                <td><?= esc($row->estimasi) ?></td>
                                <td>
                                    <?php if ($row->is_active): ?>
                                        <span class="badge badge-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= site_url('admin/shipping_area/edit/' . $row->id . '?page=' . $pager->getCurrentPage() . '&perPage=' . $perPage . '&table_search=' . urlencode($keyword)) ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?= site_url('admin/shipping_area/delete/' . $row->id . '?page=' . $pager->getCurrentPage() . '&perPage=' . $perPage . '&table_search=' . urlencode($keyword)) ?>" onclick="return confirm('Hapus data ini?')" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>

            <div class="card-footer clearfix">
                <div class="float-left">
                    <form method="get">
                        <input type="hidden" name="table_search" value="<?= esc($keyword) ?>">
                        <select class="form-control" name="perPage" onchange="this.form.submit()">
                            <option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>10</option>
                            <option value="25" <?= $perPage == 25 ? 'selected' : '' ?>>25</option>
                            <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50</option>
                        </select>
                    </form>
                </div>
                <div class="float-right">
                    <?= $pager->links('shippingarea', 'bootstrap') ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>