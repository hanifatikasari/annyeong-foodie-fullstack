<?php 
// Panggil library Ion Auth agar dapat digunakan di View
$ionAuth = new \IonAuth\Libraries\IonAuth();

// Variabel penanda menu aktif (dikirim dari controller)
$currentAdminMenu    = $currentAdminMenu ?? '';
$currentAdminSubMenu = $currentAdminSubMenu ?? '';
?>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
<a href="<?= site_url('admin/dashboard') ?>" class="brand-link navbar-primary d-flex align-items-center text-decoration-none">
    <img src="<?= base_url('uploads/settings/logo.png') ?>" 
         alt="Annyeong Foodie" 
         class="logo-img">
    <span class="logo-text">Annyeong Foodie</span>
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
                <!-- SINGLE MENU -- SETTINGS  -->
                <!-- ===================================================== -->
                <?php if ($ionAuth->inGroup(['admin','pemilik', 'penjualan', 'produksi','gudang'])): ?>
                    <li class="nav-item">
                        <a href="<?= site_url('admin/absensi') ?>"
                        class="nav-link <?= $currentAdminMenu == 'absensi' ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-user-check"></i>
                            <p>Absensi</p>
                        </a>
                    </li> 
                <?php endif; ?>

                <?php if ($ionAuth->inGroup(['admin','pemilik', 'penjualan'])): ?>
                    <li class="nav-item">
                        <a href="<?= site_url('admin/settings/payment') ?>"
                        class="nav-link <?= ($currentAdminSubMenu == 'payment_setting') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-credit-card"></i>
                            <p>Payment Settings</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= site_url('admin/sliders') ?>"
                           class="nav-link <?= ($currentAdminSubMenu == 'carousel') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-images"></i>
                            <p>Carousel</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= site_url('admin/settings/handling-fee') ?>"
                        class="nav-link <?= ($currentAdminSubMenu == 'handling_fee') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-box-open"></i>
                            <p>Handling Fee</p>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($ionAuth->inGroup(['admin','penjualan'])): ?>
                    <li class="nav-item">
                        <a href="<?= site_url('admin/shipping_area') ?>"
                           class="nav-link <?= ($currentAdminSubMenu == 'shipping_area') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-map-marked-alt"></i>
                            <p>Shipping Area</p>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($ionAuth->inGroup(['admin', 'pemilik', 'penjualan', 'gudang', 'produksi'])): ?>
                    <li class="nav-item">
                        <a href="<?= site_url('admin/reports') ?>"
                           class="nav-link <?= ($currentAdminMenu == 'report') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>Reports</p>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- MENU WITH SUBMENU -->
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
                                    <p>POS Sales</p>
                                </a>
                            </li>

                             <!-- Tambahkan menu ini di dalam grup penjualan -->
                            <li class="nav-item">
                                <a href="<?= site_url('admin/orders') ?>" class="nav-link <?= ($currentAdminSubMenu == 'online_order') ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i><p>Online Orders</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- ===================================================== -->
                <!-- CATALOGUE -->
                <!-- admin, produksi, gudang -->
                <!-- ===================================================== -->
                <?php if ($ionAuth->inGroup(['admin', 'produksi', 'gudang'])): ?>
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
                            <?php if ($ionAuth->inGroup(['admin', 'produksi'])): ?>
                                <li class="nav-item">
                                    <a href="<?= site_url('admin/products') ?>"
                                       class="nav-link <?= ($currentAdminSubMenu == 'product') ? 'active' : '' ?>">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Products</p>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if ($ionAuth->inGroup(['admin', 'produksi', 'gudang'])): ?>
                                <li class="nav-item">
                                    <a href="<?= site_url('admin/categories') ?>"
                                       class="nav-link <?= ($currentAdminSubMenu == 'category') ? 'active' : '' ?>">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Categories</p>
                                    </a>
                                </li>
                            <?php endif; ?>
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
                            <i class="nav-icon fas fa-utensils"></i>
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
                                   class="nav-link <?= ($currentAdminSubMenu == 'process') ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Production Process</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- ===================================================== -->
                <!-- INVENTORY -->
                <!-- admin, gudang, produksi -->
                <!-- ===================================================== -->
                <?php if ($ionAuth->inGroup(['admin', 'gudang', 'produksi'])): ?>
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
                <!-- data manajemen -->
                <!--==================================================== -->
                <?php if ($ionAuth->inGroup(['admin','gudang','penjualan','kurir','produksi','pemilik'])): ?>
                <li class="nav-item has-treeview <?= ($currentAdminMenu == 'data_management') ? 'menu-open' : '' ?>">
                    <a href="#"
                    class="nav-link <?= ($currentAdminMenu == 'data_management') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-database"></i>
                        <p>
                            Data Management
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                         <li class="nav-item">
                            <a href="<?= site_url('admin/users') ?>"
                            class="nav-link <?= ($currentAdminSubMenu == 'user_management') ? 'active' : '' ?>">
                                <i class="fas fa-users nav-icon"></i>
                                <p>Kelola User</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="<?= site_url('admin/products/trashed') ?>"
                            class="nav-link <?= ($currentAdminSubMenu == 'trash_bin') ? 'active' : '' ?>">
                                <i class="far fa-trash-alt nav-icon"></i>
                                <p>Trash Bin</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="<?= site_url('admin/backup') ?>"
                            class="nav-link <?= ($currentAdminSubMenu == 'backup') ? 'active' : '' ?>">
                                <i class="fas fa-save nav-icon"></i>
                                <p>Backup and Restore</p>
                            </a>
                        </li>

                    </ul>
                </li>
                <?php endif; ?>
                <!-- ===================================================== -->
                <!-- WEBSITE -->
                <!-- ===================================================== -->
                <li class="nav-header">WEBSITE</li>

                <li class="nav-item">
                    <a href="<?= site_url('/') ?>"
                    class="nav-link">
                        <i class="nav-icon fas fa-store"></i>
                        <p>Back to Online Store</p>
                    </a>
                </li>

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