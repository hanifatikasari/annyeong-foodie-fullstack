<?= $this->extend('themes/indomarket/layout') ?>
<?= $this->section('content') ?>

<section class="product-detail py-5">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb bg-transparent p-0">
                <li class="breadcrumb-item"><a href="<?= site_url() ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url('products') ?>">Produk</a></li>
                <li class="breadcrumb-item active"><?= esc($product->name) ?></li>
            </ol>
        </nav>

        <div class="row">
            <!-- Gambar Produk -->
            <div class="col-md-5 mb-4">
                <?php if (!empty($product->images)): ?>
                    <img id="mainImg" src="<?= base_url($product->images[0]->large) ?>" class="img-fluid rounded w-100" style="max-height:450px;object-fit:cover;" alt="<?= esc($product->name) ?>">
                    <?php if (count($product->images) > 1): ?>
                        <div class="d-flex mt-2 gap-2">
                            <?php foreach ($product->images as $img): ?>
                                <img src="<?= base_url($img->small) ?>"
                                     class="img-thumbnail cursor-pointer mr-2"
                                     style="width:70px;height:70px;object-fit:cover;cursor:pointer;"
                                     onclick="document.getElementById('mainImg').src='<?= base_url($img->large) ?>'"
                                     alt="">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height:350px;">
                        <i class="fa fa-image fa-4x text-muted"></i>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Detail Produk -->
            <div class="col-md-7">
                <h2 class="font-weight-bold"><?= esc($product->name) ?></h2>

                <!-- Rating -->
                <div class="d-flex align-items-center mb-2">
                    <div class="star-rating mr-2">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fa fa-star<?= $i <= round($avg_rating) ? '' : '-o' ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <small class="text-muted"><?= $avg_rating ?>/5 (<?= count($reviews) ?> ulasan)</small>
                </div>

                <div class="mb-3">
                    <span class="h3 text-primary font-weight-bold">Rp <?= number_format($product->price) ?></span>
                </div>

                <p class="text-muted mb-3"><?= esc($product->short_description) ?></p>

                <!-- Attributes EAV -->
                <?php if (!empty($product->attributes)): ?>
                    <div class="mb-3">
                        <table class="table table-sm table-bordered">
                            <?php foreach ($product->attributes as $attr): ?>
                                <tr>
                                    <td class="font-weight-bold text-nowrap" style="width:140px;"><?= esc($attr['attr_name']) ?></td>
                                    <td><?= esc($attr['attr_value'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                <?php endif; ?>

                <!-- Stok -->
                <div class="mb-3">
                    <?php if ($product->stok > 0): ?>
                        <span class="badge badge-success"><i class="fa fa-check mr-1"></i>Stok tersedia (<?= $product->stok ?>)</span>
                    <?php else: ?>
                        <span class="badge badge-danger"><i class="fa fa-times mr-1"></i>Stok habis</span>
                    <?php endif; ?>
                    <small class="text-muted ml-2">SKU: <?= esc($product->sku) ?></small>
                </div>

                <!-- Add to Cart -->
                <?php if ($product->stok > 0): ?>
                    <form class="form-add-cart d-flex align-items-center mb-3">
                        <?= csrf_field() ?>
                        <input type="hidden" name="product_id" value="<?= $product->id ?>">
                        <div class="input-group mr-3" style="max-width:130px;">
                            <div class="input-group-prepend">
                                <button type="button" class="btn btn-outline-secondary btn-qty" data-action="minus">-</button>
                            </div>
                            <input type="number" name="qty" class="form-control text-center" value="1" min="1" max="<?= $product->stok ?>">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary btn-qty" data-action="plus">+</button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg mr-2">
                            <i class="fa fa-cart-plus mr-1"></i> Tambah ke Keranjang
                        </button>
                    </form>
                    <a href="<?= site_url('cart') ?>" class="btn btn-outline-primary">
                        <i class="fa fa-shopping-cart mr-1"></i> Lihat Keranjang
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Deskripsi & Ulasan -->
        <div class="row mt-5">
            <div class="col-12">
                <ul class="nav nav-tabs" id="productTab">
                    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#desc">Deskripsi</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#review">Ulasan (<?= count($reviews) ?>)</a></li>
                </ul>
                <div class="tab-content border border-top-0 p-4 rounded-bottom">
                    <div class="tab-pane active" id="desc">
                        <?= $product->description ?: '<p class="text-muted">Belum ada deskripsi.</p>' ?>
                    </div>
                    <div class="tab-pane" id="review">
                        <!-- Form Ulasan -->
                        <?php if ($auth->loggedIn()): ?>
                            <form action="<?= site_url('products/review') ?>" method="post" class="mb-4 p-3 bg-light rounded">
                                <?= csrf_field() ?>
                                <input type="hidden" name="product_id" value="<?= $product->id ?>">
                                <h6 class="font-weight-bold">Tulis Ulasan</h6>
                                <div class="form-group">
                                    <label>Rating</label>
                                    <select name="rating" class="form-control form-control-sm" style="max-width:120px;">
                                        <?php for ($i=5;$i>=1;$i--): ?>
                                            <option value="<?= $i ?>"><?= str_repeat('⭐', $i) ?> (<?= $i ?>)</option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Ulasan</label>
                                    <textarea name="ulasan" class="form-control" rows="3" placeholder="Bagikan pengalaman Anda..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">Kirim Ulasan</button>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <a href="<?= site_url('auth/login') ?>">Login</a> untuk menulis ulasan.
                            </div>
                        <?php endif; ?>

                        <!-- Daftar Ulasan -->
                        <?php if (!empty($reviews)): ?>
                            <?php foreach ($reviews as $review): ?>
                                <div class="media mb-3 pb-3 border-bottom">
                                    <div class="mr-3">
                                        <?php if (!empty($review['avatar'])): ?>
                                            <img src="<?= base_url($review['avatar']) ?>" class="rounded-circle" width="45" height="45" style="object-fit:cover;">
                                        <?php else: ?>
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:45px;height:45px;">
                                                <?= strtoupper(substr($review['first_name'], 0, 1)) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="media-body">
                                        <h6 class="mb-0"><?= esc($review['first_name'] . ' ' . $review['last_name']) ?></h6>
                                        <div class="star-rating" style="font-size:13px;">
                                            <?php for ($i=1;$i<=5;$i++): ?>
                                                <i class="fa fa-star<?= $i <= $review['rating'] ? '' : '-o' ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <p class="mt-1 mb-0"><?= esc($review['ulasan']) ?></p>
                                        <small class="text-muted"><?= date('d M Y', strtotime($review['created_at'])) ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">Belum ada ulasan.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Produk Terkait -->
        <?php if (!empty($related)): ?>
            <div class="row mt-5">
                <div class="col-12"><h4 class="font-weight-bold mb-4">Produk Terkait</h4></div>
                <?php foreach ($related as $rel): ?>
                    <div class="col-lg-3 col-md-4 col-6 mb-3">
                        <div class="single-product product-card border rounded p-2">
                            <?php if (!empty($rel->featured_image)): ?>
                                <a href="<?= site_url('products/' . $rel->sku . '/' . $rel->slug) ?>">
                                    <img src="<?= base_url($rel->featured_image->medium) ?>" class="img-fluid rounded" alt="">
                                </a>
                            <?php endif; ?>
                            <div class="mt-2">
                                <h6><a href="<?= site_url('products/' . $rel->sku . '/' . $rel->slug) ?>" class="text-dark"><?= esc($rel->name) ?></a></h6>
                                <span class="text-primary font-weight-bold">Rp <?= number_format($rel->price) ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
// Qty control
document.querySelectorAll('.btn-qty').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var input = this.closest('.input-group').querySelector('input[name=qty]');
        var val   = parseInt(input.value) || 1;
        var max   = parseInt(input.max) || 99;
        if (this.dataset.action === 'plus' && val < max) input.value = val + 1;
        if (this.dataset.action === 'minus' && val > 1) input.value = val - 1;
    });
});
</script>

<?= $this->endSection() ?>