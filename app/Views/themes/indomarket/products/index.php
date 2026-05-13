<?= $this->extend('themes/indomarket/layout') ?>
<?= $this->section('content') ?>

<section class="products-grid pb-5 pt-4">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 col-md-4 col-12 mb-4">
                <?= $this->include('themes/indomarket/shared/sidebar') ?>
            </div>

            <!-- Product Grid -->
            <div class="col-lg-9 col-md-8 col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Semua Produk</h5>
                    <select class="form-control form-control-sm w-auto" onchange="location.href=this.value">
                        <?php foreach ($ordering as $url => $label): ?>
                            <option value="<?= $url ?>" <?= $selectedOrder == $url ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="row">
                    <?php if ($products): ?>
                        <?php foreach ($products as $product): ?>
                            <div class="col-lg-4 col-md-6 col-6 mb-4">
                                <div class="single-product product-card h-100 border rounded p-2">
                                    <?php if (!empty($product->featured_image)): ?>
                                        <div class="product-img">
                                            <a href="<?= site_url('products/' . $product->sku . '/' . $product->slug) ?>">
                                                <img src="<?= base_url($product->featured_image->medium) ?>" class="img-fluid rounded" alt="<?= esc($product->name) ?>">
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <div class="bg-light d-flex align-items-center justify-content-center rounded" style="height:180px;">
                                            <i class="fa fa-image fa-2x text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="product-content mt-2">
                                        <h6><a href="<?= site_url('products/' . $product->sku . '/' . $product->slug) ?>" class="text-dark"><?= esc($product->name) ?></a></h6>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <span class="text-primary font-weight-bold">Rp <?= number_format($product->lowest_price ?? $product->price ?? 0) ?></span>
                                        </div>
                                        <form class="form-add-cart mt-2">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="product_id" value="<?= $product->id ?>">
                                            <input type="hidden" name="qty" value="1">
                                            <button type="submit" class="btn btn-primary btn-block btn-sm">
                                                <i class="fa fa-cart-plus mr-1"></i> Tambah ke Keranjang
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-5">
                            <i class="fa fa-box-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada produk tersedia.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <?= $pager->links('bootstrap', 'bootstrap') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>