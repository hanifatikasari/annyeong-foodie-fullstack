<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            Trash Bin Products
        </h3>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover">

            <thead>
                <tr>
                    <th width="70">ID</th>
                    <th>Nama Produk</th>
                    <th>SKU</th>
                    <th>Type</th>
                    <th>Deleted At</th>
                    <th width="250">Action</th>
                </tr>
            </thead>

            <tbody>
                <?php if(empty($products)): ?>
                    <tr>
                        <td colspan="6" class="text-center">
                            Tidak ada data di Trash Bin
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach($products as $product): ?>
                    <tr>
                        <td><?= $product->id ?></td>
                        <td><?= esc($product->name) ?></td>
                        <td><?= esc($product->sku) ?></td>
                        <td><?= esc($product->type) ?></td>
                        <td><?= $product->deleted_at ?></td>
                        <td>
                            <a href="<?= site_url('admin/products/restore/'.$product->id) ?>"
                               class="btn btn-success btn-sm"
                               onclick="return confirm('Restore produk ini?')"><i class="fas fa-undo"></i>
                                Restore
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>