<?= $this->extend('themes/indomarket/layout') ?>
<?= $this->section('content') ?>

<!-- SLIDER -->
<section class="slider-section pt-4 pb-4">
    <div class="container">
        <div class="slider-inner">
            <div class="row">
                <div class="col-md-3">
                    <nav class="nav-category">
                        <h2>Kategori</h2>
                        <ul class="menu-category">
                            <?php foreach ($categories ?? [] as $cat): ?>
                                <li><a href="<?= site_url('products?category=' . $cat->slug) ?>"><?= esc($cat->name) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </nav>
                </div>
                <div class="col-md-9">
                    <div id="mainCarousel" class="carousel slide carousel-fade" data-ride="carousel">
                        <ol class="carousel-indicators">
                            <li data-target="#mainCarousel" data-slide-to="0" class="active"></li>
                            <li data-target="#mainCarousel" data-slide-to="1"></li>
                            <li data-target="#mainCarousel" data-slide-to="2"></li>
                        </ol>
                        <div class="carousel-inner shadow-sm rounded">
                            <div class="carousel-item active">
                                <img class="d-block w-100" src="<?= base_url('/themes/indomarket/assets/img/slides/slide1.jpg') ?>" alt="Kimbab">
                                <div class="carousel-caption d-none d-md-block">
                                    <h5 style="text-shadow:2px 2px 4px rgba(0,0,0,.6);">Kimbab Terlaris!</h5>
                                    <a href="<?= site_url('products') ?>" class="btn btn-primary btn-sm">Lihat Semua</a>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <img class="d-block w-100" src="<?= base_url('/themes/indomarket/assets/img/slides/slide2.jpg') ?>" alt="Dessert">
                                <div class="carousel-caption d-none d-md-block">
                                    <h5 style="text-shadow:2px 2px 4px rgba(0,0,0,.6);">Mochi Dessert</h5>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <img class="d-block w-100" src="<?= base_url('/themes/indomarket/assets/img/slides/slide3.jpg') ?>" alt="Dimsum">
                                <div class="carousel-caption d-none d-md-block">
                                    <h5 style="text-shadow:2px 2px 4px rgba(0,0,0,.6);">Dimsum Mentai Sauce</h5>
                                </div>
                            </div>
                        </div>
                        <a class="carousel-control-prev" href="#mainCarousel" data-slide="prev"><span class="carousel-control-prev-icon"></span></a>
                        <a class="carousel-control-next" href="#mainCarousel" data-slide="next"><span class="carousel-control-next-icon"></span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SERVICES -->
<section class="pt-4 pb-4 bg-light">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="media align-items-center justify-content-center">
                    <div class="iconbox rounded-circle text-white bg-primary mr-3 p-3"><i class="fa fa-truck fa-lg"></i></div>
                    <div class="text-left"><h6 class="mb-0">Pengiriman Cepat</h6><small class="text-muted">Area Pemalang & sekitarnya</small></div>
                </div>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="media align-items-center justify-content-center">
                    <div class="iconbox rounded-circle text-white bg-success mr-3 p-3"><i class="fa fa-credit-card fa-lg"></i></div>
                    <div class="text-left"><h6 class="mb-0">Bayar Online</h6><small class="text-muted">QRIS & Transfer Bank</small></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="media align-items-center justify-content-center">
                    <div class="iconbox rounded-circle text-white bg-warning mr-3 p-3"><i class="fa fa-shield fa-lg"></i></div>
                    <div class="text-left"><h6 class="mb-0">100% Halal</h6><small class="text-muted">Bahan pilihan berkualitas</small></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- TRENDING MENU -->
<section class="products-grids trending pb-5 pt-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-4">
                <h2 class="font-weight-bold">Menu Terlaris 🔥</h2>
                <p class="text-muted">Produk favorit pelanggan kami</p>
            </div>
        </div>
        <div class="row">
            <?php foreach ($trendingProducts as $product): ?>
                <div class="col-xl-3 col-lg-4 col-md-6 col-6 mb-4">
                    <div class="single-product product-card">
                        <?php if (!empty($product->featured_image)): ?>
                            <div class="product-img">
                                <a href="<?= site_url('products/' . $product->sku . '/' . $product->slug) ?>">
                                    <img src="<?= base_url($product->featured_image->medium) ?>" class="img-fluid rounded" alt="<?= esc($product->name) ?>">
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="product-img bg-light d-flex align-items-center justify-content-center rounded" style="height:200px;">
                                <i class="fa fa-image fa-3x text-muted"></i>
                            </div>
                        <?php endif; ?>
                        <div class="product-content mt-2">
                            <h3 class="h6"><a href="<?= site_url('products/' . $product->sku . '/' . $product->slug) ?>"><?= esc($product->name) ?></a></h3>
                            <div class="product-price d-flex justify-content-between align-items-center">
                                <span class="text-primary font-weight-bold">Rp <?= number_format($product->price ?? 0) ?></span>
                                <form class="form-add-cart">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="product_id" value="<?= $product->id ?>">
                                    <input type="hidden" name="qty" value="1">
                                    <button type="submit" class="btn btn-primary btn-sm" <?= ($product->stok ?? 0) <= 0 ? 'disabled' : '' ?>>
                                        <i class="fa fa-cart-plus"></i>
                                    </button>
                                </form>
                            </div>
                            <?php if (($product->stok ?? 0) <= 0): ?>
                                <span class="badge badge-danger">Habis</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-3">
            <a href="<?= site_url('products') ?>" class="btn btn-outline-primary">Lihat Semua Produk <i class="fa fa-arrow-right ml-1"></i></a>
        </div>
    </div>
</section>

<!-- ORDER CTA -->
<section id="track-order" class="py-5 bg-primary text-white text-center">
    <div class="container">
        <h3 class="font-weight-bold mb-2">Sudah tahu mau pesan apa?</h3>
        <p class="mb-4">Lacak pesanan Anda dengan mudah menggunakan nomor invoice.</p>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form action="<?= site_url('track') ?>" method="get" class="input-group">
                    <input type="text" name="invoice" class="form-control" placeholder="Masukkan nomor invoice...">
                    <div class="input-group-append">
                        <button class="btn btn-light" type="submit"><i class="fa fa-search"></i> Lacak</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>