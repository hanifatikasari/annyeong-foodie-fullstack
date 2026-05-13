<?php 
// Panggil library Ion Auth agar dapat digunakan di View
$ionAuth = new \IonAuth\Libraries\IonAuth();

// Variabel penanda menu aktif (dikirim dari controller)
$currentAdminMenu    = $currentAdminMenu ?? '';
$currentAdminSubMenu = $currentAdminSubMenu ?? '';
?>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?= site_url('admin/dashboard') ?>" class="brand-link navbar-primary">
        <img src="<?= base_url('admin/img/AdminLTELogo.png') ?>"
             alt="AdminLTE Logo"
             class="brand-image img-circle elevation-3"
             style="opacity: .8">
        <span class="brand-text font-weight-light">Annyeong Foodie</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

        <!-- User Panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?= base_url('admin/img/user2-160x160.jpg') ?>"
                     class="img-circle elevation-2"
                     alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">
                    <?= $ionAuth->user()->row()->username ?? 'User'; ?>
                </a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column"
                data-widget="treeview"
                role="menu"
                data-accordion="false">

                <!-- DASHBOARD -->
                <li class="nav-item">
                    <a href="<?= site_url('admin/dashboard') ?>"
                       class="nav-link <?= ($currentAdminSubMenu == 'dashboard') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- ===================================================== -->
                <!-- CATALOGUE -->
                <!-- admin, produksi -->
                <!-- ===================================================== -->
                <?php if ($ionAuth->inGroup(['admin', 'produksi'])): ?>
                    <li class="nav-item has-treeview <?= ($currentAdminMenu == 'catalogue') ? 'menu-open' : '' ?>">
                        <a href="#"
                           class="nav-link <?= ($currentAdminMenu == 'catalogue') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-th"></i>
                            <p>
                                Catalogue
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= site_url('admin/products') ?>"
                                   class="nav-link <?= ($currentAdminSubMenu == 'product') ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Products</p>
                                </a>
                            </li>

                             <!-- Categories khusus gudang/admin -->
                            <?php if ($ionAuth->inGroup(['admin', 'gudang'])): ?>
                                <li class="nav-item">
                                    <a href="<?= site_url('admin/categories') ?>"
                                       class="nav-link <?= ($currentAdminSubMenu == 'category') ? 'active' : '' ?>">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Categories</p>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <li class="nav-item">
                                <a href="<?= site_url('admin/attributes') ?>"
                                   class="nav-link <?= ($currentAdminSubMenu == 'attribute') ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Attributes</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- ===================================================== -->
                <!-- PRODUCTION -->
                <!-- admin, produksi -->
                <!-- ===================================================== -->
                <?php if ($ionAuth->inGroup(['admin', 'produksi'])): ?>
                    <li class="nav-item has-treeview <?= ($currentAdminMenu == 'production') ? 'menu-open' : '' ?>">
                        <a href="#"
                           class="nav-link <?= ($currentAdminMenu == 'production') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-tools"></i>
                            <p>
                                Production
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">
                            <!-- Recipes -->
                            <li class="nav-item">
                                <a href="<?= site_url('admin/recipes') ?>"
                                   class="nav-link <?= ($currentAdminSubMenu == 'recipes') ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Recipes (BOM)</p>
                                </a>
                            </li>

                            <!-- Production Process -->
                            <li class="nav-item">
                                <a href="<?= site_url('admin/produksi') ?>"
                                   class="nav-link <?= ($currentAdminSubMenu == 'produksi') ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Production Process</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- ===================================================== -->
                <!-- INVENTORY -->
                <!-- admin, gudang, produksi, pemilik -->
                <!-- ===================================================== -->
                <?php if ($ionAuth->inGroup(['admin', 'gudang', 'produksi', 'pemilik'])): ?>
                    <li class="nav-item has-treeview <?= ($currentAdminMenu == 'inventory') ? 'menu-open' : '' ?>">
                        <a href="#"
                           class="nav-link <?= ($currentAdminMenu == 'inventory') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-warehouse"></i>
                            <p>
                                Inventory
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">

                            <!-- Raw Materials -->
                            <li class="nav-item">
                                <a href="<?= site_url('admin/bahanbaku') ?>"
                                   class="nav-link <?= ($currentAdminSubMenu == 'bahan_baku') ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Bahan Baku</p>
                                </a>
                            </li>

                            <!-- Stock In -->
                            <li class="nav-item">
                                <a href="<?= site_url('admin/stokmasuk') ?>"
                                   class="nav-link <?= ($currentAdminSubMenu == 'stok_masuk') ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Stok Masuk</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- ===================================================== -->
                <!-- SALES / TRANSACTIONS -->
                <!-- admin, penjualan -->
                <!-- ===================================================== -->
                <?php if ($ionAuth->inGroup(['admin', 'penjualan'])): ?>
                    <li class="nav-item has-treeview <?= ($currentAdminMenu == 'sales') ? 'menu-open' : '' ?>">
                        <a href="#"
                           class="nav-link <?= ($currentAdminMenu == 'sales') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-shopping-cart"></i>
                            <p>
                                Transactions
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= site_url('admin/penjualan') ?>"
                                   class="nav-link <?= ($currentAdminSubMenu == 'order') ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Orders / Sales</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Tambahkan menu ini di dalam grup penjualan -->
                    <li class="nav-item">
                        <a href="<?= site_url('admin/orders') ?>" class="nav-link <?= ($currentAdminSubMenu == 'online_order') ? 'active' : '' ?>">
                            <i class="far fa-circle nav-icon"></i><p>Pesanan Online</p>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- ===================================================== -->
                <!-- REPORTS -->
                <!-- admin, pemilik -->
                <!-- ===================================================== -->
                <?php if ($ionAuth->inGroup(['admin', 'pemilik'])): ?>
                    <li class="nav-item">
                        <a href="<?= site_url('admin/reports') ?>"
                           class="nav-link <?= ($currentAdminMenu == 'report') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>Reports</p>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- ===================================================== -->
                <!-- SETTINGS -->
                <!-- admin only -->
                <!-- ===================================================== -->
                <?php if ($ionAuth->isAdmin()): ?>
                    <li class="nav-header">SETTINGS</li>

                    <li class="nav-item">
                        <a href="<?= site_url('admin/users') ?>"
                           class="nav-link <?= ($currentAdminSubMenu == 'user') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-users-cog"></i>
                            <p>User Management</p>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- ===================================================== -->
                <!-- ACCOUNT -->
                <!-- ===================================================== -->
                <li class="nav-header">ACCOUNT</li>

                <li class="nav-item">
                    <a href="<?= site_url('auth/logout') ?>"
                       class="nav-link text-danger"
                       onclick="return confirm('Apakah anda yakin ingin keluar?')">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>