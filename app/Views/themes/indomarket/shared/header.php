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
// Identifikasi akun admin/staf
$isAdmin = isset($auth) && $auth->loggedIn()
    && $auth->inGroup([
        'admin',
        'gudang',
        'produksi',
        'penjualan',
        'pemilik'
    ]);
?>
<header class="header clearfix">

    <!-- TOP BAR (Desktop & Tablet >= 576px) -->
    <div class="top-bar d-none d-sm-block">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-left">
                    <ul class="top-links contact-info list-unstyled d-flex mb-0">
                        <li class="mr-3">
                            <i class="fas fa-envelope mr-1"></i>
                            <a href="mailto:batterbit@gmail.com">batterbit@gmail.com</a>
                        </li>
                        <li>
                            <i class="fab fa-whatsapp mr-1"></i>
                            <a href="https://wa.me/6285879505277" target="_blank">+62 858-7950-5277</a>
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
                                    <i class="fas fa-tachometer-alt mr-1"></i>
                                    <a href="<?= site_url('admin/dashboard') ?>">Dashboard Admin</a>
                                </li>
                            <?php endif; ?>
                            <li class="mr-3">
                                <i class="far fa-user-circle mr-1"></i>
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

    <!-- MAIN HEADER (All Dimensions Friendly) -->
    <div class="header-main border-top">
        <div class="container">
            <div class="row align-items-center py-2 py-md-3">
                <!-- Logo Brand -->
                <div class="col-7 col-sm-8 col-md-4 col-lg-3 order-1">
                    <a href="<?= site_url() ?>" class="navbar-brand d-flex align-items-center text-decoration-none py-0">
                        <img src="<?= base_url('uploads/settings/logo.png') ?>" 
                            alt="Batterbit" 
                            class="logo-img">
                        <span class="logo-text">Batterbit</span>
                    </a>
                </div>

                <!-- Cart & Account Icons (Mobile: right of logo on row 1; Desktop: right of search on row 1) -->
                <div class="col-5 col-sm-4 col-md-3 col-lg-1 order-2 order-md-3 text-right">
                    <div class="d-inline-flex align-items-center justify-content-end header-actions">
                        <div class="mr-3 account-icon-wrap" style="position:relative;">
                            <a href="<?= site_url('account') ?>" title="Akun Saya" class="text-dark">
                                <i class="fa fa-user-circle fa-lg"></i>
                            </a>
                        </div>
                        <?php if (isset($auth) && $auth->loggedIn() && !$isAdmin): ?>
                            <div class="notification-icon-wrap mr-3"
                                style="position:relative;">

                                <a href="<?= site_url('account/notifications') ?>"
                                title="Notifikasi"
                                class="text-dark">

                                    <i class="far fa-bell fa-lg"></i>

                                    <span id="notificationCountBadge"
                                        style="
                                            position:absolute;
                                            top:-8px;
                                            right:-10px;
                                            background:#e83e8c;
                                            color:#fff;
                                            border-radius:50%;
                                            width:19px;
                                            height:19px;
                                            font-size:11px;
                                            font-weight:700;
                                            display:none;
                                            align-items:center;
                                            justify-content:center;
                                        ">
                                    </span>
                                </a>
                            </div>
                        <?php endif; ?>
                        <div class="cart-icon-wrap" style="position:relative;">
                            <a href="<?= site_url('cart') ?>" title="Keranjang" class="text-dark">
                                <i class="fa fa-shopping-cart fa-lg"></i>
                                <span class="cart-count-badge"
                                      id="cartCountBadge"
                                      style="
                                          position:absolute;
                                          top:-8px;right:-10px;
                                          background:#5e72e4;color:#fff;
                                          border-radius:50%;
                                          width:19px;height:19px;
                                          font-size:11px;font-weight:700;
                                          display:flex;align-items:center;justify-content:center;
                                          line-height:1;
                                      ">
                                    <?= function_exists('cart_item_count') ? cart_item_count() : 0 ?>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>

               <!-- Search Bar (Mobile: row 2; Desktop: row 1 center) -->
                <div class="col-12 col-md-5 col-lg-8 order-3 order-md-2 mt-2 mt-md-0 d-flex justify-content-center">
                    <form action="<?= site_url('search') ?>" method="get" class="w-100" style="max-width:800px;">
                        <div class="input-group">
                            <input type="text"
                                name="q"
                                class="form-control"
                                placeholder="Cari menu favorit Anda..."
                                value="<?= esc(service('request')->getGet('q') ?? '') ?>"
                                autocomplete="off">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit" aria-label="Cari">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- NAVIGATION (Navbar & Collapse Menu) -->
    <nav class="navbar navbar-expand-lg navbar-light border-top border-bottom bg-white py-1 py-lg-0">
        <div class="container d-flex justify-content-between align-items-center">
            <!-- Toggler Button Mobile -->
            <button class="navbar-toggler" type="button"
                    data-toggle="collapse" data-target="#mainNav"
                    aria-controls="mainNav" aria-expanded="false" aria-label="Menu Navigasi">
                <span class="navbar-toggler-icon"></span>
                <span class="navbar-toggler-text ml-2">Menu</span>
            </button>

            <!-- Quick Link di samping Toggler (khusus mobile < lg) -->
            <div class="d-lg-none py-1 header-quick-links">
                <a href="<?= site_url('track') ?>" class="btn btn-sm btn-outline-secondary py-1 px-2 text-xs" style="font-size:11.5px; border-radius:var(--bb-radius-pill);">
                    <i class="fa fa-map-marker text-primary mr-1"></i>Lacak
                </a>
                <a href="<?= site_url('products') ?>" class="btn btn-sm btn-outline-primary py-1 px-2 text-xs ml-1" style="font-size:11.5px; border-radius:var(--bb-radius-pill);">
                    <i class="fa fa-th-large mr-1"></i>Katalog
                </a>
            </div>

            <!-- Collapse Menu -->
            <div class="collapse navbar-collapse" id="mainNav">
                <!-- Mobile Header di dalam modal collapse (Argon pattern) -->
                <div class="navbar-collapse-header d-lg-none">
                    <div class="row align-items-center">
                        <div class="col-8 collapse-brand">
                            <a href="<?= site_url() ?>" class="d-flex align-items-center text-decoration-none">
                                <img src="<?= base_url('uploads/settings/logo.png') ?>" alt="Batterbit" style="height:34px;width:auto;" class="mr-2">
                                <span class="logo-text" style="font-size:1.35rem;">Batterbit</span>
                            </a>
                        </div>
                        <div class="col-4 collapse-close text-right">
                            <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#mainNav" aria-controls="mainNav" aria-expanded="true" aria-label="Tutup navigasi">
                                <span></span>
                                <span></span>
                            </button>
                        </div>
                    </div>
                </div>

                <ul class="navbar-nav mr-auto">
                    <!-- Home -->
                    <li class="nav-item">
                        <a class="nav-link <?= current_url() === site_url() ? 'active font-weight-bold' : '' ?>"
                           href="<?= site_url() ?>">
                            <i class="fas fa-home mr-2 text-primary d-lg-none"></i>Home
                        </a>
                    </li>

                    <!-- Produk Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="navbarDropdownProduk" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-bread-slice mr-2 text-primary d-lg-none"></i>Produk
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownProduk">
                            <a class="dropdown-item" href="<?= site_url('products') ?>">
                                <i class="fa fa-th-large mr-2 text-primary"></i>Semua Produk
                            </a>
                            <?php if (!empty($categories)): ?>
                                <div class="dropdown-divider"></div>
                                <?php foreach ($categories as $cat): ?>
                                    <a class="dropdown-item"
                                       href="<?= site_url('products?category=' . esc($cat->slug)) ?>">
                                        <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:10px;"></i><?= esc($cat->name) ?>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </li>

                    <!-- Lacak Pesanan -->
                    <li class="nav-item">
                        <a class="nav-link <?= current_url() === site_url('track') ? 'active font-weight-bold' : '' ?>" href="<?= site_url('track') ?>">
                            <i class="fa fa-map-marker mr-2 text-primary d-lg-none"></i>Lacak Pesanan
                        </a>
                    </li>

                    <!-- Tentang Kami -->
                    <li class="nav-item">
                        <a class="nav-link <?= current_url() === site_url('about') ? 'active font-weight-bold' : '' ?>" href="<?= site_url('about') ?>">
                            <i class="fas fa-info-circle mr-2 text-primary d-lg-none"></i>Tentang Kami
                        </a>
                    </li>
                </ul>

                <!-- Informasi Akun & Bantuan Khusus Mobile (d-lg-none) -->
                <div class="d-lg-none border-top mt-3 pt-3">
                    <span class="text-xs text-uppercase font-weight-bold text-muted d-block mb-2" style="font-size:11px; letter-spacing:0.05em;">Akun Pelanggan</span>
                    <?php if (isset($auth) && $auth->loggedIn()): ?>
                        <?php $isAdmin = $auth->inGroup(['admin', 'gudang', 'produksi', 'penjualan', 'pemilik']); ?>
                        <div class="d-flex align-items-center mb-2">
                            <i class="far fa-user-circle fa-2x mr-2 text-primary"></i>
                            <div>
                                <div class="font-weight-bold text-dark text-sm"><?= esc($currentUser->first_name ?? 'Pelanggan') ?></div>
                                <small class="text-muted"><?= esc($currentUser->email ?? '') ?></small>
                            </div>
                        </div>
                        <?php if ($isAdmin): ?>
                            <a href="<?= site_url('admin/dashboard') ?>" class="btn btn-sm btn-outline-warning btn-block text-left mb-2">
                                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard Admin
                            </a>
                        <?php endif; ?>
                        <div class="row no-gutters mb-2">
                            <div class="col-6 pr-1">
                                <a href="<?= site_url('account/orders') ?>" class="btn btn-sm btn-outline-primary btn-block text-left">
                                    <i class="fa fa-shopping-bag mr-1"></i>Pesanan
                                </a>
                            </div>
                            <div class="col-6 pl-1">
                                <a href="<?= site_url('account/profile') ?>" class="btn btn-sm btn-outline-secondary btn-block text-left">
                                    <i class="fa fa-user mr-1"></i>Profil
                                </a>
                            </div>
                        </div>
                        <a href="<?= site_url('auth/logout') ?>" class="btn btn-sm btn-light btn-block text-danger">
                            <i class="fa fa-power-off mr-2"></i>Logout
                        </a>
                    <?php else: ?>
                        <div class="row no-gutters">
                            <div class="col-6 pr-1">
                                <a href="<?= site_url('auth/login') ?>" class="btn btn-sm btn-primary btn-block">
                                    <i class="fa fa-sign-in-alt mr-1"></i>Login
                                </a>
                            </div>
                            <div class="col-6 pl-1">
                                <a href="<?= site_url('auth/create_user') ?>" class="btn btn-sm btn-outline-primary btn-block">
                                    <i class="fa fa-user-plus mr-1"></i>Daftar
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="border-top mt-3 pt-2">
                        <span class="text-xs text-uppercase font-weight-bold text-muted d-block mb-1" style="font-size:11px; letter-spacing:0.05em;">Kontak & Bantuan</span>
                        <a href="https://wa.me/6285879505277" target="_blank" class="d-flex align-items-center text-muted small py-1">
                            <i class="fab fa-whatsapp text-success mr-2"></i>+62 858-7950-5277
                        </a>
                        <a href="mailto:batterbit@gmail.com" class="d-flex align-items-center text-muted small py-1">
                            <i class="fas fa-envelope text-primary mr-2"></i>batterbit@gmail.com
                        </a>
                    </div>
                </div>

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