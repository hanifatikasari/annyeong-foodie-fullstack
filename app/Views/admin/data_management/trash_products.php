<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            Trash Bin Products
        </h3>
    </div>
    <div class="card-body table-responsive">

        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <strong>Automatic Cleanup Policy</strong>
            <ul class="mb-0 mt-2">
                <li>Products in Trash Bin can be restored within <strong>30 days</strong>.</li>
                <li>Products without any transaction history will be permanently removed after <strong>30 days</strong>.</li>
                <li>Products that already have transaction history will be retained for reporting purposes, but their images will be automatically removed after <strong>30 days</strong> to save storage space.</li>
            </ul>
        </div>

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th width="70">ID</th>
                    <th>Nama Produk</th>
                    <th>SKU</th>
                    <th>Type</th>
                    <th>Deleted</th>
                    <th>Auto Cleanup</th>
                    <th>Status</th>
                    <th width="170">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($products)): ?>
                    <tr>
                        <td colspan="8" class="text-center">
                            Tidak ada data di Trash Bin
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach($products as $product): ?>
                <?php
                    $deletedAt = strtotime($product->deleted_at);
                    $cleanupAt = strtotime('+30 days', $deletedAt);
                    $remaining = max(
                        0,
                    ceil(($cleanupAt - time()) / 86400)
                    );
                ?>
                <tr>
                    <td><?= $product->id ?></td>
                    <td><?= esc($product->name) ?></td>
                    <td><?= esc($product->sku) ?></td>
                    <td><?= esc($product->type) ?></td>
                    <td>
                        <?= date('d M Y', $deletedAt) ?><br>
                        <small class="text-muted">
                            <?= date('H:i', $deletedAt) ?>
                        </small>
                    </td>
                    <td>
                        <?= date('d M Y', $cleanupAt) ?><br>
                        <small class="text-muted">
                            <?= date('H:i', $cleanupAt) ?>
                        </small>
                    </td>
                    <td>
                        <?php if($remaining > 14): ?>
                            <span class="badge badge-success">
                                <?= $remaining ?> days left
                            </span>
                        <?php elseif($remaining > 7): ?>
                            <span class="badge badge-warning">
                                <?= $remaining ?> days left
                            </span>
                        <?php elseif($remaining > 1): ?>
                            <span class="badge badge-danger">
                                <?= $remaining ?> days left
                            </span>
                        <?php elseif($remaining == 1): ?>
                            <span class="badge badge-danger">
                                Tomorrow
                            </span>
                        <?php else: ?>
                            <span class="badge badge-dark">
                                Scheduled
                            </span>
                        <?php endif; ?>
                        </td>
                    <td>
                        <a href="<?= site_url('admin/products/restore/'.$product->id) ?>"
                           class="btn btn-success btn-sm"
                           onclick="return confirm('Restore produk ini?')">
                            <i class="fas fa-undo"></i>
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