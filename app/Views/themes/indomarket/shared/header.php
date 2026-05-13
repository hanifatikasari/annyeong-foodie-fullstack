<header class="header clearfix">
    <div class="top-bar d-none d-sm-block">
        <div class="container">
            <div class="row">
                <div class="col-6 text-left">
                    <ul class="top-links contact-info">
                        <li><i class="fa fa-envelope-o"></i> <a href="#">annyeongfoodie@gmail.com</a></li>
                        <li><i class="fa fa-whatsapp"></i> +62 812-3456-7890</li>
                    </ul>
                </div>
                <div class="col-6 text-right">
                    <ul class="top-links account-links">
                        <?php if ($auth->loggedIn()): ?>
                            <?php if ($auth->inGroup(['admin','gudang','produksi','penjualan'], $currentUser->id)): ?>
                                <li><i class="fa fa-tachometer"></i> <a href="<?= site_url('admin/dashboard') ?>">Dashboard Admin</a></li>
                            <?php endif; ?>
                            <li><i class="fa fa-user-circle-o"></i> <a href="<?= site_url('account') ?>"><?= esc($currentUser->first_name) ?></a></li>
                            <li><i class="fa fa-power-off"></i> <a href="<?= site_url('auth/logout') ?>">Logout</a></li>
                        <?php else: ?>
                            <li><i class="fa fa-user-circle-o"></i> <a href="<?= site_url('auth/create_user') ?>">Daftar</a></li>
                            <li><i class="fa fa-power-off"></i> <a href="<?= site_url('auth/login') ?>">Login</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="header-main border-top">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-3 col-6">
                    <a class="navbar-brand" href="<?= site_url() ?>">
                        <i class="fa fa-shopping-bag fa-2x text-primary"></i>
                        <span class="logo font-weight-bold">Annyeong Foodie</span>
                    </a>
                </div>
                <div class="col-lg-6 col-12 col-sm-12 order-lg-0 order-2 mt-2 mt-lg-0">
                    <form action="<?= site_url('search') ?>" method="get" class="search">
                        <div class="input-group w-100">
                            <input type="text"
                             name="q" 
                             class="form-control" 
                             placeholder="Cari produk..." 
                             value="<?= esc(service('request')->getGet('q') ?? '') ?>">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-lg-3 col-6 text-right">
                    <div class="right-icons d-inline-flex align-items-center">
                        <div class="single-icon mr-3">
                            <a href="<?= site_url('account') ?>" title="Akun Saya"><i class="fa fa-user fa-lg"></i></a>
                        </div>
                        <div class="single-icon cart-badge">
                            <a href="<?= site_url('cart') ?>">
                                <i class="fa fa-shopping-cart fa-lg"></i>
                                <span class="badge-count cart-count"><?= cart_count() ?></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-main navbar-expand-lg navbar-light border-top border-bottom">
        <div class="container">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main_nav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="main_nav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="<?= site_url() ?>">Home</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#">Produk</a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="<?= site_url('products') ?>">Semua Produk</a>
                            <div class="dropdown-divider"></div>
                            <?php foreach ($categories ?? [] as $cat): ?>
                                <a class="dropdown-item" href="<?= site_url('products?category=' . $cat->slug) ?>"><?= esc($cat->name) ?></a>
                            <?php endforeach; ?>
                        </div>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('track') ?>">Lacak Pesanan</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('about') ?>">Tentang Kami</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
            <div class="container"><?= session()->getFlashdata('success') ?></div>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-0">
            <div class="container"><?= session()->getFlashdata('error') ?></div>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>
</header>