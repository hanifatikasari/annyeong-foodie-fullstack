<?php namespace Config;

$routes = Services::routes();

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

// 1. FRONTEND (E-BISNIS) - TANPA FILTER
$routes->get('/', 'Home::index');
$routes->get('products', 'Products::index');
$routes->get('products/(:segment)/(:segment)', 'Products::show/$1/$2');
$routes->get('test-midtrans', 'TestMidtrans::index');

// 2. AUTHENTICATION
$routes->group('auth', function($routes) {
    $routes->get('login', 'Auth::login');
    $routes->post('login', 'Auth::login');
    $routes->get('logout', 'Auth::logout');
    $routes->get('forgot_password', 'Auth::forgot_password');
});

// 3. ADMIN AREA (ERP/Dashboard) - PAKAI FILTER
$routes->group('admin', ['filter' => 'authAdmin:admin,pemilik,gudang,produksi,penjualan'], function($routes) {
    
    $routes->get('dashboard', 'Admin\Dashboard::index');

     // --- CATEGORIES ---
    // Produksi & Penjualan butuh ini untuk manajemen katalog
    $routes->group('categories', ['filter' => 'authAdmin:admin,penjualan,gudang,pemilik,produksi'], function($routes) {
        $routes->get('/', 'Admin\Categories::index');
        
        $routes->group('', ['filter' => 'authAdmin:admin,penjualan,gudang'], function($routes) {
            $routes->post('simpan', 'Admin\Categories::simpan');
            $routes->get('edit/(:num)', 'Admin\Categories::edit/$1');
            $routes->get('hapus/(:num)', 'Admin\Categories::hapus/$1');
        });
    });

    // --- PRODUCTS ---
    $routes->group('products', ['filter' => 'authAdmin:admin,produksi,pemilik'], function($routes) {
    // Tampilan List & Sampah
    $routes->get('/', 'Admin\Products::index');
    $routes->get('trashed', 'Admin\Products::trashed');

        // Fitur Khusus Admin/Produksi
        $routes->group('', ['filter' => 'authAdmin:admin,produksi'], function($routes) {
            $routes->get('create', 'Admin\Products::create');
            $routes->post('/', 'Admin\Products::store'); // Simpan baru
            $routes->get('edit/(:num)', 'Admin\Products::edit/$1');
            $routes->put('(:num)', 'Admin\Products::update/$1'); // Update data (Method PUT)
            $routes->delete('(:num)', 'Admin\Products::destroy/$1'); // Hapus
            
            
            // Fitur Gambar 
            $routes->get('(:num)/images', 'Admin\Products::images/$1');
            $routes->get('(:num)/upload-image', 'Admin\Products::uploadImage/$1');
            $routes->post('(:num)/upload-image', 'Admin\Products::doUploadImage/$1');
            $routes->delete('images/(:num)', 'Admin\Products::destroyImage/$1');
            
            $routes->get('getCategoriesAjax', 'Admin\Products::getCategoriesAjax');
            $routes->post('getAttributesByCategory', 'Admin\Products::getAttributesByCategory');
        });
    });


    // --- PRODUKSI ---
    $routes->group('produksi', ['filter' => 'authAdmin:admin,produksi'], function($routes) {
        $routes->get('/', 'Admin\Produksi::index');
        $routes->get('tambah', 'Admin\Produksi::tambah');
        $routes->post('simulasi', 'Admin\Produksi::simulasi');
        $routes->post('simpan', 'Admin\Produksi::simpan');
        $routes->get('show/(:num)', 'Admin\Produksi::show/$1');
    });

    // --- PENJUALAN ---
    $routes->group('penjualan', ['filter' => 'authAdmin:admin,penjualan'], function($routes) {
        $routes->get('/', 'Admin\Penjualan::index');
        $routes->get('create', 'Admin\Penjualan::create');
        $routes->post('simpan', 'Admin\Penjualan::simpan');
        $routes->get('show/(:num)', 'Admin\Penjualan::show/$1');
        });

        // Orders
    $routes->get('orders', 'Admin\Orders::index');
    $routes->get('orders/detail/(:num)', 'Admin\Orders::detail/$1');
    $routes->post('orders/verify/(:num)', 'Admin\Orders::verify/$1');
    $routes->post('orders/update-status/(:num)', 'Admin\Orders::updateStatus/$1');
    $routes->post('orders/updateStatus/(:num)', 'Admin\Orders::updateStatus/$1');
    

    // --- REPORTS ---
    $routes->group('reports', ['filter' => 'authAdmin:admin,pemilik'], function($routes) {
        $routes->get('/', 'Admin\Reports::index');
        $routes->get('penjualan', 'Admin\Reports::penjualan');
        $routes->get('produksi', 'Admin\Reports::produksi');
        $routes->get('stok-bahan', 'Admin\Reports::stokBahan');
    });
    
    // --- BAHAN BAKU (Inventory) ---
    // Pemilik bisa LIHAT, tapi cuma Admin & Gudang yang bisa eksekusi CRUD
    $routes->group('bahanbaku', ['filter' => 'authAdmin:admin,gudang,pemilik'], function($routes) {
        $routes->get('/', 'Admin\BahanBaku::index'); // Read-only untuk pemilik
        
        $routes->group('', ['filter' => 'authAdmin:admin,gudang'], function($routes) {
            $routes->get('tambah', 'Admin\BahanBaku::tambah');
            $routes->post('simpan', 'Admin\BahanBaku::simpan');
            $routes->get('edit/(:num)', 'Admin\BahanBaku::edit/$1');
            $routes->post('update/(:num)', 'Admin\BahanBaku::update/$1');
            $routes->get('hapus/(:num)', 'Admin\BahanBaku::hapus/$1');
            $routes->get('getCategoriesAjax', 'Admin\BahanBaku::getCategoriesAjax');
        });
    });

        // --- ATTRIBUTES & OPTIONS ---
    $routes->group('attributes', ['filter' => 'authAdmin:admin,produksi'], function($routes) {
        // Utama: Management Atribut
        $routes->get('/', 'Admin\Attributes::index');
        $routes->get('create', 'Admin\Attributes::create'); // Opsional jika form tambah di halaman beda
        $routes->get('edit/(:num)', 'Admin\Attributes::edit/$1'); 
        $routes->post('store', 'Admin\Attributes::store');
        $routes->post('update/(:num)', 'Admin\Attributes::update/$1');
        $routes->get('delete/(:num)', 'Admin\Attributes::destroy/$1');
        $routes->get('getCategoriesAjax', 'Admin\Attributes::getCategoriesAjax');

        $routes->group('(:num)/options', function($routes) {
            $routes->get('/', 'Admin\AttributeOptions::index/$1');
            $routes->post('store', 'Admin\AttributeOptions::store/$1');
            
            // Edit butuh 2 parameter: ID Attribute dan ID Option
            $routes->get('edit/(:num)', 'Admin\AttributeOptions::index/$1/$2');
            $routes->post('update/(:num)', 'Admin\AttributeOptions::update/$1/$2');
            
            // Delete butuh 2 parameter: ID Attribute dan ID Option
           $routes->get('delete/(:num)', 'Admin\AttributeOptions::destroy/$1/$2');
        });
    });

    // --- RECIPES ---
    $routes->group('recipes', ['filter' => 'authAdmin:admin,produksi'], function($routes) {
        $routes->get('/', 'Admin\Recipes::index');
        $routes->get('detail/(:num)', 'Admin\Recipes::detail/$1');
        $routes->post('simpan/(:num)', 'Admin\Recipes::simpan/$1');
        $routes->post('update/(:num)/(:num)', 'Admin\Recipes::update/$1/$2');
        $routes->get('hapus/(:num)/(:num)', 'Admin\Recipes::hapus/$1/$2');
        $routes->get('getBahanAjax', 'Admin\Recipes::getBahanAjax');
    });

    // --- STOK MASUK (Inventory) ---
    $routes->group('stokmasuk', ['filter' => 'authAdmin:admin,gudang,pemilik'], function($routes) {
        $routes->get('/', 'Admin\StokMasuk::index');

        $routes->group('', ['filter' => 'authAdmin:admin,gudang'], function($routes) {
             $routes->get('tambah', 'Admin\StokMasuk::tambah');
             $routes->post('simpan', 'Admin\StokMasuk::simpan');
             $routes->get('getBahanbakuAjax', 'Admin\StokMasuk::getBahanbakuAjax');
        });  
    });
    
    
});

// ============================================================
// MIDTRANS WEBHOOK — HARUS PUBLIK (tanpa auth filter apapun)
// URL ini yang didaftarkan di dashboard Midtrans
// ============================================================
$routes->get('midtrans-test', 'MidtransNotification::handle');
$routes->post('midtrans/notification', 'MidtransNotification::handle');
$routes->get('hello', function() {
    return 'hello world';
});

// ============================================================
    // E-COMMERCE FRONTEND ROUTES
    // ============================================================

    // Product search
    $routes->get('search', 'Shop::search');

    // Cart
    $routes->get('cart',                   'Cart::index');
    $routes->post('cart/add',              'Cart::add');
    $routes->post('cart/update',           'Cart::update');
    $routes->post('cart/remove',           'Cart::remove');
    $routes->get('cart/clear',             'Cart::clear');

    // Checkout & Orders (wajib login)
    $routes->group('checkout', ['filter' => 'authFrontend'], function($routes) {
        $routes->get('/',                  'Checkout::index');
        $routes->post('process',           'Checkout::process');
        $routes->get('success/(:segment)', 'Checkout::success/$1');
        $routes->get('upload/(:segment)',  'Checkout::uploadForm/$1');
        $routes->post('upload/(:segment)', 'Checkout::uploadBukti/$1');
            // === Baris baru untuk Midtrans ===
        $routes->get('pay/(:segment)',     'Checkout::pay/$1');
        $routes->get('return/(:segment)',  'Checkout::midtransReturn/$1');
    });

    // =========================
    // CUSTOMER ACCOUNT
    // =========================
    $routes->group('account', ['filter' => 'authFrontend'], function($routes) {
        $routes->get('/', 'Account::index');
        $routes->get('orders', 'Account::orders');

         // Route spesifik harus lebih dulu
        $routes->get('orders/detail/(:num)', 'Account::orderDetail/$1');

        // Route alternatif lama
        $routes->get('orders/(:segment)', 'Account::orderDetail/$1');

        $routes->get('profile', 'Account::profile');
        $routes->post('profile/update', 'Account::updateProfile');
        $routes->get('track', 'Track::index');
    });
    

    // Track order (publik)
    $routes->get('track', 'Track::index');
    $routes->get('track/search', 'Track::search');

    // About
    $routes->get('about',                  'Shop::about');

    // Reviews
    $routes->post('products/review',       'Shop::submitReview');

    // ============================================================
    // ADMIN EXTENSION - Payment & Order Management
    // ============================================================
    $routes->group('admin', ['filter' => 'authAdmin:admin,penjualan,pemilik'], function($routes) {
        $routes->get('orders',                          'Admin\Orders::index');
        $routes->get('orders/(:num)',                   'Admin\Orders::detail/$1');
        $routes->post('orders/verify/(:num)',           'Admin\Orders::verify/$1');
        $routes->post('orders/status/(:num)',           'Admin\Orders::updateStatus/$1');
        $routes->get('orders/bukti/(:segment)',         'Admin\Orders::viewBukti/$1');
    });
