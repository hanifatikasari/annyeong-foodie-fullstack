<?php
/**
 * Header View — Indomarket Theme
 *
 * Variables yang diharapkan dari BaseController / $this->data:
 * - $categories  : array of storefront categories (child dari "Produk Jadi")
 * - $auth        : IonAuth instance
 * - $currentUser : object user yang sedang login, atau null
 */

// Guard: pastikan $categories tidak null
$categories  = $categories ?? [];
$currentUser = $currentUser ?? null;
?>
<header class="header clearfix">

    <!-- TOP BAR -->
    <div class="top-bar d-none d-sm-block">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-left">
                    <ul class="top-links contact-info list-unstyled d-flex mb-0">
                        <li class="mr-3">
                            <i class="fa fa-envelope-o mr-1"></i>
                            <a href="mailto:annyeongfoodie@gmail.com">annyeongfoodie@gmail.com</a>
                        </li>
                        <li>
                            <i class="fa fa-whatsapp mr-1"></i>
                            <a href="https://wa.me/6281234567890" target="_blank">+62 812-3456-7890</a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6 text-right">
                    <ul class="top-links account-links list-unstyled d-flex justify-content-end mb-0">
                        <?php if (isset($auth) && $auth->loggedIn()): ?>
                            <?php
                            // Cek apakah user adalah admin/staff
                            $isAdmin = $auth->inGroup(['admin', 'gudang', 'produksi', 'penjualan', 'pemilik']);
                            ?>
                            <?php if ($isAdmin): ?>
                                <li class="mr-3">
                                    <i class="fa fa-tachometer mr-1"></i>
                                    <a href="<?= site_url('admin/dashboard') ?>">Dashboard Admin</a>
                                </li>
                            <?php endif; ?>
                            <li class="mr-3">
                                <i class="fa fa-user-circle-o mr-1"></i>
                                <a href="<?= site_url('account') ?>">
                                    <?= esc($currentUser->first_name ?? 'Akun Saya') ?>
                                </a>
                            </li>
                            <li>
                                <i class="fa fa-power-off mr-1"></i>
                                <a href="<?= site_url('auth/logout') ?>">Logout</a>
                            </li>
                        <?php else: ?>
                            <li class="mr-3">
                                <i class="fa fa-user-plus mr-1"></i>
                                <a href="<?= site_url('auth/create_user') ?>">Daftar</a>
                            </li>
                            <li>
                                <i class="fa fa-power-off mr-1"></i>
                                <a href="<?= site_url('auth/login') ?>">Login</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN HEADER -->
    <div class="header-main border-top">
        <div class="container">
            <div class="row align-items-center py-3">
                <!-- Logo -->
                <div class="col-lg-3 col-6">
                    <a href="<?= site_url() ?>" class="navbar-brand text-decoration-none">
                        <span style="font-size:1.5rem;font-weight:800;color:#e83e8c;">
                            <i class="fa fa-shopping-bag mr-1"></i>Annyeong Foodie
                        </span>
                    </a>
                </div>

                <!-- Search Bar -->
                <div class="col-lg-6 col-12 col-sm-12 order-lg-0 order-2 mt-2 mt-lg-0">
                    <form action="<?= site_url('search') ?>" method="get">
                        <div class="input-group">
                            <input type="text"
                                   name="q"
                                   class="form-control"
                                   placeholder="Cari produk Korea favorit Anda..."
                                   value="<?= esc(service('request')->getGet('q') ?? '') ?>"
                                   autocomplete="off">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Cart & Account Icons -->
                <div class="col-lg-3 col-6 text-right">
                    <div class="d-inline-flex align-items-center">
                        <div class="mr-3">
                            <a href="<?= site_url('account') ?>" title="Akun Saya" class="text-dark">
                                <i class="fa fa-user-circle fa-lg"></i>
                            </a>
                        </div>
                        <div style="position:relative;">
                            <a href="<?= site_url('cart') ?>" title="Keranjang" class="text-dark">
                                <i class="fa fa-shopping-cart fa-lg"></i>
                                <span class="cart-count-badge"
                                      id="cartCountBadge"
                                      style="
                                          position:absolute;
                                          top:-8px;right:-10px;
                                          background:#e83e8c;color:#fff;
                                          border-radius:50%;
                                          width:19px;height:19px;
                                          font-size:11px;font-weight:700;
                                          display:flex;align-items:center;justify-content:center;
                                          line-height:1;
                                      ">
                                    <?= function_exists('cart_count') ? cart_count() : 0 ?>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- NAVIGATION -->
    <nav class="navbar navbar-expand-lg navbar-light border-top border-bottom bg-white py-0">
        <div class="container">
            <button class="navbar-toggler my-2" type="button"
                    data-toggle="collapse" data-target="#mainNav"
                    aria-controls="mainNav" aria-expanded="false">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav mr-auto">
                    <!-- Home -->
                    <li class="nav-item">
                        <a class="nav-link <?= current_url() === site_url() ? 'active font-weight-bold' : '' ?>"
                           href="<?= site_url() ?>">Home</a>
                    </li>

                    <!-- Produk Dropdown: FIX ISSUE #1 — hanya tampilkan storefront categories -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                            Produk
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="<?= site_url('products') ?>">
                                <i class="fa fa-th-large mr-1"></i> Semua Produk
                            </a>
                            <?php if (!empty($categories)): ?>
                                <div class="dropdown-divider"></div>
                                <?php foreach ($categories as $cat): ?>
                                    <a class="dropdown-item"
                                       href="<?= site_url('products?category=' . esc($cat->slug)) ?>">
                                        <?= esc($cat->name) ?>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </li>

                    <!-- Lacak Pesanan -->
                    <li class="nav-item">
                        <a class="nav-link" href="<?= site_url('track') ?>">
                            <i class="fa fa-map-marker mr-1"></i> Lacak Pesanan
                        </a>
                    </li>

                    <!-- Tentang Kami -->
                    <li class="nav-item">
                        <a class="nav-link" href="<?= site_url('about') ?>">Tentang Kami</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- FLASH MESSAGES -->
    <?php if ($flashSuccess = session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mb-0 rounded-0" role="alert">
            <div class="container"><?= $flashSuccess ?></div>
            <button type="button" class="close pr-4" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>

    <?php if ($flashError = session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-0 rounded-0" role="alert">
            <div class="container"><?= $flashError ?></div>
            <button type="button" class="close pr-4" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>

    <?php if ($flashMessage = session()->getFlashdata('message')): ?>
        <div class="alert alert-info alert-dismissible fade show mb-0 rounded-0">
            <div class="container"><?= $flashMessage ?></div>
            <button type="button" class="close pr-4" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>

</header>