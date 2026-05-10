<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1>Manajemen Resep (Bill of Materials)</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Recipes</li>
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
                <h3 class="card-title">Daftar Produk — Klik untuk Kelola Resep</h3>
                <div class="card-tools">
                    <form action="<?= site_url('admin/recipes') ?>" method="get" class="form-inline">
                        <div class="input-group input-group-sm">
                            <input type="text" name="table_search" class="form-control"
                                   placeholder="Cari produk..." value="<?= esc($keyword ?? '') ?>">
                            <div class="input-group-append">
                                <button class="btn btn-default"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width:50px">No</th>
                            <th>SKU</th>
                            <th>Nama Produk</th>
                            <th>HPP Saat Ini</th>
                            <th>Harga Jual</th>
                            <th>Margin</th>
                            <th>Status Resep</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $currentPage = $pager->getCurrentPage('bootstrap');
                        $no = ($currentPage - 1) * $perPage + 1;
                        foreach ($products as $p):
                            $margin = ($p->price > 0 && $p->hpp_total > 0)
                                ? round((($p->price - $p->hpp_total) / $p->price) * 100, 1)
                                : 0;
                            $recipeCount = model('RecipeModel')->where('product_id', $p->id)->countAllResults();
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><span class="badge badge-secondary"><?= esc($p->sku) ?></span></td>
                            <td><strong><?= esc($p->name) ?></strong></td>
                            <td>Rp <?= number_format($p->hpp_total) ?></td>
                            <td>Rp <?= number_format($p->price) ?></td>
                            <td>
                                <?php if ($margin > 0): ?>
                                    <span class="badge badge-<?= $margin >= 30 ? 'success' : ($margin >= 15 ? 'warning' : 'danger') ?>">
                                        <?= $margin ?>%
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">-</span>
                                <?php endif ?>
                            </td>
                            <td>
                                <?php if ($recipeCount > 0): ?>
                                    <span class="badge badge-success"><i class="fas fa-check"></i> <?= $recipeCount ?> bahan</span>
                                <?php else: ?>
                                    <span class="badge badge-danger"><i class="fas fa-times"></i> Belum ada resep</span>
                                <?php endif ?>
                            </td>
                            <td>
                                <a href="<?= site_url('admin/recipes/detail/' . $p->id) ?>"
                                   class="btn btn-primary btn-sm">
                                    <i class="fas fa-book"></i> Kelola Resep
                                </a>
                            </td>
                        </tr>
                        <?php endforeach ?>
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