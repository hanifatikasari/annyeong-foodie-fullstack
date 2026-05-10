<?= $this->extend('themes/' . $currentTheme . '/layout') ?>

<?= $this->section('content') ?>
<section class="product-detail py-5">
    <div class="container">

        <div class="row">
            <div class="col-md-6">
                <?php if (!empty($product->featured_image)): ?>
                    <img src="<?= base_url($product->featured_image->medium) ?>"
                         class="img-fluid rounded"
                         alt="<?= esc($product->name) ?>">
                <?php else: ?>
                    <img src="https://via.placeholder.com/600x400?text=No+Image"
                         class="img-fluid rounded">
                <?php endif; ?>
            </div>

            <div class="col-md-6">
                <h2><?= esc($product->name) ?></h2>
                <h4 class="text-danger mb-3">
                    Rp <?= number_format($product->price, 0, ',', '.') ?>
                </h4>

                <p><?= $product->short_description ?></p>

                <hr>

                <strong>SKU:</strong> <?= esc($product->sku) ?><br>
                <strong>Berat:</strong> <?= $product->weight ?> gram
            </div>
        </div>

    </div>
</section>
<?= $this->endSection() ?>