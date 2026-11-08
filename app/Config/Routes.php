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
//$routes->get('test-midtrans', 'TestMidtrans::index');

// 2. AUTHENTICATION
$routes->group('auth', function($routes) {
    $routes->get('/', 'Auth::index');
    $routes->get('login', 'Auth::login');
    $routes->post('login', 'Auth::login');
    $routes->get('register', 'Auth::create_user');
    $routes->post('register', 'Auth::create_user');
    $routes->get('logout', 'Auth::logout');
    $routes->get('forgot_password', 'Auth::forgot_password');
    $routes->post('forgot_password', 'Auth::forgot_password');
    $routes->get('create_user', 'Auth::create_user');
    $routes->post('create_user', 'Auth::create_user');
    $routes->get('change_password', 'Auth::change_password');
    $routes->post('change_password', 'Auth::change_password');

    // --- GOOGLE OAUTH ---
    $routes->get('google', 'GoogleAuth::start');
    $routes->get('google/callback', 'GoogleAuth::callback');
    $routes->get('google/link', 'GoogleAuth::linkForm');
    $routes->post('google/link', 'GoogleAuth::linkSubmit');
    $routes->get('google/cancel', 'GoogleAuth::cancel');
});

// 3. ADMIN AREA (ERP/Dashboard) - PAKAI FILTER
$routes->group('admin', ['filter' => 'authAdmin:admin,pemilik,gudang,produksi,penjualan'], function($routes) {
    
    $routes->get('dashboard', 'Admin\Dashboard::index');
    // Backup routes 
    $routes->get('backup', 'Admin\Backup::index');
    $routes->get('backup/download', 'Admin\Backup::download');
    $routes->post('backup/restore', 'Admin\Backup::restore');
    
    $routes->group('categories', ['filter' => 'authAdmin:admin,gudang'], function($routes) {
        $routes->get('/', 'Admin\Categories::index');
        
        $routes->group('', ['filter' => 'authAdmin:admin,gudang'], function($routes) {
            $routes->post('simpan', 'Admin\Categories::simpan');
            $routes->get('edit/(:num)', 'Admin\Categories::edit/$1');
            $routes->get('hapus/(:num)', 'Admin\Categories::hapus/$1');
        });
    });

    // --- PRODUCTS ---
    $routes->group('products', ['filter' => 'authAdmin:admin,produksi,pemilik,penjualan,gudang'], function($routes) {
    // Tampilan List & Sampah
    $routes->get('/', 'Admin\Products::index');
    $routes->get('trashed', 'Admin\Products::trashed');
    $routes->get('restore/(:num)','Admin\Products::restore/$1');

        // Fitur Khusus Admin/Produksi
        $routes->group('', ['filter' => 'authAdmin:admin,produksi'], function($routes) {
            $routes->get('create', 'Admin\Products::create');
            $routes->post('/', 'Admin\Products::store'); // Simpan baru
            $routes->get('edit/(:num)', 'Admin\Products::edit/$1');
            $routes->put('(:num)', 'Admin\Products::update/$1'); // Update data (Method PUT)
            $routes->delete('(:num)', 'Admin\Products::destroy/$1'); // Hapus
            $routes->get('(:num)/variants', 'Admin\Products::variants/$1');
            $routes->get('(:num)/variants/create','Admin\Products::createVariant/$1');
            $routes->post('(:num)/variants', 'Admin\Products::storeVariant/$1');
            
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
        $routes->get('getProdukAjax', 'Admin\Produksi::getProdukAjax');
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
        $routes->get('struk/(:num)','Admin\Penjualan::struk/$1');
        });

        // Orders
    $routes->group('orders', ['filter' => 'authAdmin:admin,penjualan'], function($routes) {
        $routes->get('/', 'Admin\Orders::index');
        $routes->get('detail/(:num)', 'Admin\Orders::detail/$1');
        $routes->post('verify/(:num)', 'Admin\Orders::verify/$1');
        $routes->post('update-status/(:num)', 'Admin\Orders::updateStatus/$1');
        $routes->post('updateStatus/(:num)', 'Admin\Orders::updateStatus/$1');
        $routes->post('save-courier/(:num)', 'Admin\Orders::saveCourier/$1');
    });

    // --- USERS ---
    $routes->group('users', ['filter' => 'authAdmin:admin,gudang,penjualan,kurir,produksi,pemilik'], function($routes) {
        $routes->get('/', 'Admin\Users::index');
        $routes->get('create', 'Admin\Users::create');
        $routes->post('store', 'Admin\Users::store');
        $routes->get('edit/(:num)', 'Admin\Users::edit/$1');
        $routes->post('update/(:num)', 'Admin\Users::update/$1');
        $routes->get('delete/(:num)', 'Admin\Users::delete/$1');
        $routes->get('activate/(:num)', 'Admin\Users::activate/$1');
        $routes->get('deactivate/(:num)', 'Admin\Users::deactivate/$1');
        $routes->get('reset-password/(:num)', 'Admin\Users::resetPassword/$1');
        $routes->post('reset-password-all', 'Admin\Users::resetAllPasswords');
        $routes->get('activity', 'Admin\Users::activity');
    });

    //ABSENSI
    $routes->group('absensi', ['filter' => 'authAdmin:admin,gudang,penjualan,produksi,pemilik'], function($routes){
        $routes->get('/', 'Admin\Absensi::index');
        $routes->post('masuk', 'Admin\Absensi::masuk');
        $routes->post('keluar', 'Admin\Absensi::keluar');
        $routes->get('riwayat', 'Admin\Absensi::riwayat');
        $routes->get('pdf', 'Admin\Absensi::pdf');
    });

    // --- REPORTS ---
    $routes->group('reports', ['filter' => 'authAdmin:admin,pemilik,penjualan,gudang,produksi'], function($routes) {
        $routes->get('/', 'Admin\Reports::index');
        $routes->get('penjualan', 'Admin\Reports::penjualan');
        $routes->get('penjualan/pdf', 'Admin\Reports::penjualanPdf');

        $routes->get('produksi', 'Admin\Reports::produksi');
        $routes->get('produksi/pdf', 'Admin\Reports::produksiPdf');
        $routes->get('stok-bahan', 'Admin\Reports::stokBahan');
        $routes->get('stok-bahan/pdf', 'Admin\Reports::stokBahanPdf');
        $routes->get('metode', 'Admin\Reports::metode');
        $routes->get('metode/pdf', 'Admin\Reports::metodePdf');
        $routes->get('pdf', 'Admin\Reports::penjualanPdf');
    });
    
    // --- BAHAN BAKU (Inventory) ---
    // Pemilik bisa LIHAT, tapi cuma Admin & Gudang yang bisa eksekusi CRUD
    $routes->group('bahanbaku', ['filter' => 'authAdmin:admin,gudang,produksi,pemilik'], function($routes) {
        $routes->get('/', 'Admin\BahanBaku::index'); // Read-only untuk produksi/pemilik
        
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
        $routes->get('create', 'Admin\Recipes::create');
        $routes->post('create', 'Admin\Recipes::create');
    });

    // --- STOK MASUK (Inventory) ---
    $routes->group('stokmasuk', ['filter' => 'authAdmin:admin,gudang,produksi'], function($routes) {
        $routes->get('/', 'Admin\StokMasuk::index');

        $routes->group('', ['filter' => 'authAdmin:admin,gudang'], function($routes) {
             $routes->get('tambah', 'Admin\StokMasuk::tambah');
             $routes->post('simpan', 'Admin\StokMasuk::simpan');
             $routes->get('getBahanbakuAjax', 'Admin\StokMasuk::getBahanbakuAjax');
        });  
    });

    // --- SHIPPING AREA ---
    $routes->group('shipping_area', ['filter' => 'authAdmin:admin,penjualan'], function ($routes) {
        $routes->get('/', 'Admin\ShippingArea::index');
        $routes->get('create', 'Admin\ShippingArea::create');
        $routes->post('store', 'Admin\ShippingArea::store');
        $routes->get('edit/(:num)', 'Admin\ShippingArea::edit/$1');
        $routes->post('update/(:num)', 'Admin\ShippingArea::update/$1');
        $routes->get('delete/(:num)', 'Admin\ShippingArea::delete/$1');

    });
    
    // --- CAROUSEL ---
    $routes->group('sliders', ['filter' => 'authAdmin:admin,pemilik, penjualan'], function($routes) {
        $routes->get('/', 'Admin\Sliders::index');
        $routes->post('store', 'Admin\Sliders::store');
        $routes->get('edit/(:num)', 'Admin\Sliders::edit/$1');
        $routes->put('update/(:num)', 'Admin\Sliders::update/$1');
        $routes->get('delete/(:num)', 'Admin\Sliders::delete/$1');
    });

    // --- SETTINGS ---
    $routes->group('settings', ['filter' => 'authAdmin:admin,pemilik,penjualan'], function($routes) {
        $routes->get('payment','Admin\SettingsController::payment');
        $routes->post('payment/save', 'Admin\SettingsController::savePayment');

        $routes->get('handling-fee', 'Admin\SettingsController::handlingFee');
        $routes->post('handling-fee/save', 'Admin\SettingsController::saveHandlingFee');
    });
    // --- NOTIFICATIONS ---
    $routes->group('notifications', function($routes) {
        $routes->get('getUnread', 'Admin\Notification::getUnread');
        $routes->post('markAsRead/(:num)', 'Admin\Notification::markAsRead/$1');
        $routes->post('markAllAsRead', 'Admin\Notification::markAllAsRead');
    });

    // --- REVIEWS (Moderasi Ulasan) ---
    $routes->group('reviews', ['filter' => 'authAdmin:admin,penjualan,pemilik'], function($routes) {
        $routes->get('/', 'Admin\Reviews::index');
        $routes->post('toggle/(:num)', 'Admin\Reviews::toggleVisibility/$1');
        $routes->post('show/(:num)', 'Admin\Reviews::show/$1');
        $routes->post('hide/(:num)', 'Admin\Reviews::hide/$1');
        $routes->post('delete/(:num)', 'Admin\Reviews::delete/$1');
    });
    
});

// ============================================================
// MIDTRANS WEBHOOK — HARUS PUBLIK (tanpa auth filter apapun)
// URL ini yang didaftarkan di dashboard Midtrans
// ============================================================
$routes->get('midtrans/notification', 'MidtransNotification::handle');
$routes->post('midtrans/notification', 'MidtransNotification::handle');


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
    $routes->post('cart/buyNow',           'Cart::buyNow');
    $routes->post('cart/toggleSelect',     'Cart::toggleSelect');
    $routes->post('cart/selectAll',        'Cart::selectAll');
    $routes->post('cart/checkout',         'Cart::checkout');
    $routes->post('cart/removeSelected',   'Cart::removeSelected');
    $routes->post('cart/syncPrices',       'Cart::syncPrices');


    // Checkout & Orders (wajib login)
    $routes->group('checkout', ['filter' => 'authFrontend'], function($routes) {
        $routes->get('/',                  'Checkout::index');
        $routes->post('process',           'Checkout::process');
        $routes->get('success/(:segment)', 'Checkout::success/$1');
        $routes->get('upload/(:segment)',  'Checkout::uploadForm/$1');
        $routes->post('upload/(:segment)', 'Checkout::uploadBukti/$1');
            // === Baris baru untuk Midtrans ===
        $routes->get('pay/(:segment)',     'Checkout::pay/$1');
        $routes->get('midtransReturn/(:segment)',  'Checkout::midtransReturn/$1');
         $routes->get('return/(:segment)', 'Checkout::midtransReturn/$1');
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
        $routes->post('reviews/submit', 'Account::submitReview');

        // CUSTOMER NOTIFICATIONS
        // Halaman riwayat notifikasi
        $routes->get('notifications','CustomerNotification::page');
        // Endpoint JSON untuk mengambil data notifikasi
        $routes->get('notifications/data','CustomerNotification::index');
        // Endpoint jumlah notifikasi belum dibaca
        $routes->get('notifications/unread-count','CustomerNotification::unreadCount');
        // Tandai sudah dibaca (AJAX)
        $routes->post('notifications/read/(:num)','CustomerNotification::markAsRead/$1');
        $routes->post('notifications/read-all','CustomerNotification::markAllAsRead');
        $routes->post('notifications/read-all','CustomerNotification::markAllAsReadPage');
        // Buka notifikasi, tandai dibaca, lalu menuju detail pesanan
        $routes->post('notifications/open/(:num)','CustomerNotification::open/$1');
    });
    
    // Track order (publik)
    $routes->get('track', 'Track::index');
    $routes->get('track/search', 'Track::search');

    // About
    $routes->get('about',                  'Shop::about');

    // Reviews
    $routes->post('reviews/submit', 'Account::submitReview');

    // ============================================================
    // ADMIN EXTENSION - Payment & Order Management
    // ============================================================
    $routes->group('admin', ['filter' => 'authAdmin:admin,penjualan'], function($routes) {
        $routes->get('orders',                          'Admin\Orders::index');
        $routes->get('orders/(:num)',                   'Admin\Orders::detail/$1');
        $routes->post('orders/verify/(:num)',           'Admin\Orders::verify/$1');
        $routes->post('orders/status/(:num)',           'Admin\Orders::updateStatus/$1');
        $routes->get('orders/bukti/(:segment)',         'Admin\Orders::viewBukti/$1');
    });
