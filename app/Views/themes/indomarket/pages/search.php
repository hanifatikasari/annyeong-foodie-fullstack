<?= $this->extend('themes/indomarket/layout') ?>
<?= $this->section('content') ?>

<section class="py-5">
    <div class="container">
        <h4 class="font-weight-bold mb-1">
            Hasil Pencarian: <span class="text-primary">"<?= esc($keyword) ?>"</span>
        </h4>
        <p class="text-muted mb-4"><?= count($products) ?> produk ditemukan</p>

        <div class="row">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="col-lg-3 col-md-4 col-6 mb-4">
                        <div class="single-product product-card border rounded p-2 h-100">
                            <?php if (!empty($product->featured_image)): ?>
                                <a href="<?= site_url('products/' . $product->sku . '/' . $product->slug) ?>">
                                    <img src="<?= base_url($product->featured_image->medium) ?>" class="img-fluid rounded" alt="">
                                </a>
                            <?php endif; ?>
                            <div class="mt-2">
                                <h6><a href="<?= site_url('products/' . $product->sku . '/' . $product->slug) ?>" class="text-dark"><?= esc($product->name) ?></a></h6>
                                <span class="text-primary font-weight-bold">Rp <?= number_format($product->price) ?></span>
                                <form class="form-add-cart mt-2">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="product_id" value="<?= $product->id ?>">
                                    <input type="hidden" name="qty" value="1">
                                    <button type="submit" class="btn btn-primary btn-sm btn-block">
                                        <i class="fa fa-cart-plus mr-1"></i> Tambah
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="fa fa-search fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Produk tidak ditemukan</h5>
                    <p>Coba kata kunci yang berbeda atau <a href="<?= site_url('products') ?>">lihat semua produk</a></p>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($pager): ?>
            <div class="d-flex justify-content-center mt-3">
                <?= $pager->links('bootstrap', 'bootstrap') ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>