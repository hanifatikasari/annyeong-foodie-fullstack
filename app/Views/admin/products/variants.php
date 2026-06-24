<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3><?= $product->name ?> Variants</h3>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="<?= site_url('admin/dashboard') ?>">Dashboard</a>
                        </li>

                        <li class="breadcrumb-item">
                            <a href="<?= site_url('admin/products') ?>">Products</a>
                        </li>

                        <li class="breadcrumb-item">
                            <a href="<?= site_url('admin/products/edit/'.$product->id) ?>">
                                <?= esc($product->name) ?>
                            </a>
                        </li>

                        <li class="breadcrumb-item active">
                            Variants
                        </li>
                    </ol>
                </div>
                <a href="<?= site_url('admin/products/'.$product->id.'/variants/create') ?>"
                class="btn btn-primary mb-3">
                    Add Variant
                </a>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Stok</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach($variants as $variant): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= esc($variant->name) ?></td>
                            <td><?= number_format($variant->price) ?></td>
                            <td><?= number_format($variant->qty ?? 0) ?></td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>