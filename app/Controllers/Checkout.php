<?php

namespace App\Controllers;

use App\Models\PenjualanModel;
use App\Models\PenjualanDetailModel;

class Checkout extends BaseController
{
    public function __construct()
    {
        helper('cart_helper');
    }

    // ---------------------------------------------------------------
    // CHECKOUT FORM
    // ---------------------------------------------------------------
    public function index()
    {
        if (!$this->auth->loggedIn()) {
            session()->set('redirect_url', current_url());
            return redirect()->to('auth/login');
        }

        $buyNow = session()->get('buy_now');
        if ($buyNow) {
             $product = model('ProductModel')->find($buyNow['product_id']);
            if (!$product) {
                session()->remove('buy_now');
                return redirect()->to('products');
            }
           $cart = [
                $buyNow['product_id'] => [
                    'product_id' => $buyNow['product_id'],
                    'name'       => $buyNow['name'],
                    'price'      => $buyNow['price'],
                    'qty'        => $buyNow['qty'],
                    'sku'        => $buyNow['sku'],
                    'image'      => $buyNow['image'],
                ]
            ];
        } else {
            $cart = get_cart();
        }
        if (empty($cart)) return redirect()->to('cart')->with('error','Keranjang Anda kosong.');


        // Ambil data user terbaru dari DB (bukan session)
        $user = \Config\Database::connect()
            ->table('users')
            ->where('id', $this->currentUser->id)
            ->get()
            ->getRow();

        $this->data['cart'] = $cart;
        $total = 0;
        foreach($cart as $item){
            $total += $item['price'] * $item['qty'];
        }
        $this->data['total'] = $total;
        $this->data['user']  = $user ?? $this->currentUser;
        $this->data['title'] = 'Checkout';

        $settingModel = model('SettingModel');
        $this->data['handlingFee'] = (int) $settingModel->getValue(
            'handling_fee',
            0
        );

        $this->data['shippingAreas'] = model('ShippingAreaModel')
            ->where('is_active', 1)
            ->orderBy('area_name', 'ASC')
            ->findAll();
        //settingan disable/enable metode pembayaran
        $this->data['enableMidtrans'] =
            (bool) $settingModel->getValue(
                'payment_midtrans_enabled',
                0
            );

        $this->data['enableTransfer'] =
            (bool) $settingModel->getValue(
                'payment_transfer_enabled',
                0
            );

        $this->data['enableQris'] =
            (bool) $settingModel->getValue(
                'payment_qris_enabled',
                0
            );

        if (
            !$this->data['enableMidtrans']
            && !$this->data['enableTransfer']
            && !$this->data['enableQris']
        ) {
            return redirect()->to('/')
                ->with(
                    'error',
                    'Saat ini pembayaran sedang tidak tersedia.'
                );
        }

        return view('themes/indomarket/pages/checkout', $this->data);
    }

    // ---------------------------------------------------------------
    // PROCESS ORDER
    // Menangani semua metode pembayaran: QRIS, Transfer, Midtrans
    // ---------------------------------------------------------------
    public function process()
    {
        $buyNow = session()->get('buy_now');

        if ($buyNow) {
            $product = model('ProductModel')->find($buyNow['product_id']);
            if (!$product) {
                session()->remove('buy_now');
                return redirect()->to('products');
            }
            $cart = [
                $product->id => [
                    'product_id' => $product->id,
                    'name'       => $product->name,
                    'price'      => $product->price,
                    'qty'        => $buyNow['qty']
                ]
            ];
        } else {
            $cart = get_cart();
        }

        if (empty($cart)) return redirect()->to('cart');


        // ========================================
        // Ambil input terlebih dahulu
        // ========================================
        $deliveryType = $this->request->getPost('delivery_type');
        $pembayaran   = $this->request->getPost('pembayaran');

        // ========================================
        // VALIDASI
        // ========================================
        $rules = [
            'delivery_type' => ['rules' => 'required|in_list[pickup,delivery]','label' => 'Metode Pengiriman'],
            'nama_penerima' => ['rules' => 'required|min_length[3]','label' => 'Nama Penerima'],
            'no_hp_penerima' => ['rules' => 'required|min_length[6]','label' => 'Nomor HP'],
            'pembayaran' => ['rules' => 'required|in_list[QRIS,Transfer,Midtrans]','label' => 'Metode Pembayaran']
        ];

        // Tambahkan validasi khusus DELIVERY
        if ($deliveryType === 'delivery') {
            $rules['alamat_pengiriman'] = [
                'rules' => 'required|min_length[5]',
                'label' => 'Alamat Pengiriman'
            ];
            $rules['shipping_area_id'] = [
                'rules' => 'required|integer',
                'label' => 'Area Pengiriman'
            ];
        }

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // ========================================
        // Setting
        // ========================================
        $settingModel = model('SettingModel');

        $handlingFee = (int) $settingModel->getValue(
            'handling_fee',
            0
        );
        $shippingAreaId = null;
        $shippingCost = 0;
        if ($deliveryType === 'delivery') {
            $shippingAreaId = $this->request->getPost('shipping_area_id');
            $shippingArea = model('ShippingAreaModel')->find($shippingAreaId);

            if (!$shippingArea) {
                return redirect()->back()
                    ->withInput()
                    ->with(
                        'error',
                        'Area pengiriman tidak valid.'
                    );
            }

            $shippingCost = (int)$shippingArea->ongkir;
        }

        $enableMidtrans =
            (bool) $settingModel->getValue(
                'payment_midtrans_enabled',
                0
            );

        $enableTransfer =
            (bool) $settingModel->getValue(
                'payment_transfer_enabled',
                0
            );

        $enableQris =
            (bool) $settingModel->getValue(
                'payment_qris_enabled',
                0
            );

            if (
                $pembayaran === 'Midtrans'
                && !$enableMidtrans
            ) {
                return redirect()->back()
                    ->withInput()
                    ->with('error',
                        'Pembayaran Midtrans sedang dinonaktifkan.'
                    );
            }

            if (
                $pembayaran === 'Transfer'
                && !$enableTransfer
            ) {
                return redirect()->back()
                    ->withInput()
                    ->with('error',
                        'Transfer Bank sedang dinonaktifkan.'
                    );
            }

            if (
                $pembayaran === 'QRIS'
                && !$enableQris
            ) {
                return redirect()->back()
                    ->withInput()
                    ->with('error',
                        'Pembayaran QRIS sedang dinonaktifkan.'
                    );
            }

            $db = \Config\Database::connect();
            $user = $db->table('users')
                ->where('id', $this->currentUser->id)
                ->get()
                ->getRow();
       
        if ($deliveryType === 'pickup') {
            $shippingAddress = trim($user->address .', ' . $user->city .', ' . $user->province);
        } else {
            $shippingAddress = $this->request->getPost('alamat_pengiriman');
        }

        $subtotal = 0;
        foreach($cart as $item){
            $subtotal += $item['price'] * $item['qty'];
        }
        $diskon = 0;

        $totalHarga =
            $subtotal
            + $handlingFee
            + $shippingCost
            - $diskon;

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $penjualanModel = new PenjualanModel();
            $detailModel    = new PenjualanDetailModel();

            // Gunakan invoice yang sudah digenerate (jika Midtrans)
            // atau generate baru (jika manual)
            $invoiceNo = $penjualanModel->generateOnlineInvoice();
            $midtransOrderId = $invoiceNo . '-' . strtoupper(bin2hex(random_bytes(3)));

            // *** Simpan header order ***
            $orderId = $penjualanModel->insert([
                'invoice_no'       => $invoiceNo,
                'midtrans_order_id' => $midtransOrderId,
                'customer_id'      => $this->currentUser->id,
                'kasir_id'         => $this->currentUser->id,
                'shipping_area_id' => $shippingAreaId,
                'subtotal_produk'  => $subtotal,
                'handling_fee'     => $handlingFee,
                'shipping_cost'    => $shippingCost,
                'diskon'           => $diskon,
                'grand_total'      => $totalHarga,
                'pembayaran'       => $pembayaran,
                'uang_diterima'    => 0,
                'order_status'     => 'pending',
                'payment_status'   => 'unpaid',
                'shipping_address' => $shippingAddress,
                'catatan_customer' => $this->request->getPost('catatan_customer') ?? '',
                'snap_token'       => null, 
                'delivery_type'    => $deliveryType,
            ], true);


            if (!$orderId) {
                throw new \Exception('Gagal membuat order. Coba lagi.');
            }
            // =====================================================
            // Generate Snap Token SETELAH order berhasil dibuat
            // =====================================================
            if ($pembayaran === 'Midtrans') {
                try {
                    $this->_initMidtrans();
                    $snapToken = $this->_generateSnapToken(
                        $midtransOrderId,
                         $invoiceNo,
                         $cart,
                         //$totalHarga,
                         $subtotal,
                         $shippingCost,
                         $handlingFee,
                         $diskon
                    );
                    //dd($snapToken);

                    $penjualanModel->update($orderId, [
                        'snap_token' => $snapToken
                    ]);

                } catch (\Exception $e) {
                    throw new \Exception(
                        'Gagal membuat pembayaran Midtrans: '
                        . $e->getMessage()
                    );
                }
            }

            // *** Simpan detail item + kurangi stok ***
            $invModel = model('ProductInventoryModel');

            foreach ($cart as $item) {
                //dd($cart);
                $product = model('ProductModel')->find($item['product_id']);
                $hpp     = $product ? ($product->hpp_total ?? 0) : 0;
                $price   = $item['price'];
                $qty     = $item['qty'];

                $detailModel->insert([
                    'penjualan_id'  => $orderId,
                    'product_id'    => $item['product_id'],
                    'qty'           => $qty,
                    'selling_price' => $price,
                    'subtotal'      => $price * $qty,
                    'hpp_price'     => $hpp,
                ]);

                //-----------------------------------------------
                // Kurangi stok di inventory (jika ada)
                //-----------------------------------------------
                $inv = $invModel
                    ->where('product_id', $item['product_id'])
                    ->first();
                
                if ($inv) {
                    $newQty = max(0, (int) $inv->qty - $qty);
                    $invModel->update(
                        $inv->id, 
                        ['qty' => $newQty]
                    );
                }
            }

            // =====================================================
            // CREATE NOTIFICATIONS FOR ADMIN/OWNER
            // =====================================================
            $adminUsers = $db->table('users_groups')
                ->join('groups', 'groups.id = users_groups.group_id')
                ->whereIn('groups.name', ['admin', 'owner', 'penjualan'])
                ->select('users_groups.user_id')
                ->get()
                ->getResult();

            $notifData = [];
            foreach ($adminUsers as $admin) {
                $notifData[] = [
                    'user_id'      => $admin->user_id,
                    'type'         => 'new_order',
                    'reference_id' => $orderId,
                    'title'        => 'Pesanan Baru: ' . $invoiceNo,
                    'message'      => 'Pesanan baru dari ' . ($this->currentUser->first_name ?? 'Pelanggan') . ' senilai Rp ' . number_format($totalHarga, 0, ',', '.'),
                    'is_read'      => 0,
                    'created_at'   => date('Y-m-d H:i:s'),
                    'updated_at'   => date('Y-m-d H:i:s'),
                ];
            }
            if (!empty($notifData)) {
                $db->table('notifications')->insertBatch($notifData);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi database gagal. Silakan coba lagi.');
            }

            // Kosongkan keranjang
            if(session()->has('buy_now')){
                session()->remove('buy_now');
            }else{
                clear_cart();
            }

            // ============================================================
            // ROUTING BERDASARKAN METODE PEMBAYARAN:
            // - Midtrans → ke halaman Snap payment
            // - QRIS/Transfer → ke halaman sukses (upload bukti)
            // ============================================================
            if ($pembayaran === 'Midtrans') {
                return redirect()->to('checkout/pay/' . $invoiceNo);
            }
            // Manual payment (QRIS / Transfer)
            return redirect()->to('checkout/success/' . $invoiceNo);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', '[Checkout::process] ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }
    }

    // ---------------------------------------------------------------
    // MIDTRANS PAYMENT PAGE
    // Menampilkan halaman Snap modal untuk Midtrans
    // ---------------------------------------------------------------
    public function pay(string $invoice)
    {
        if (!$this->auth->loggedIn()) {
            return redirect()->to('auth/login');
        }

        $order = model('PenjualanModel')->getOrderByInvoice($invoice);

        // Validasi order: harus ada, milik user ini, dan Midtrans payment
        if (!$order
            || (int) ($order->customer_id ?? 0) !== (int) $this->currentUser->id
            || $order->pembayaran !== 'Midtrans'
        ) {
            return redirect()->to('account/orders')
                ->with('error', 'Halaman tidak ditemukan.');
        }

        // Jika sudah dibayar, langsung ke success
        if (in_array($order->order_status, ['processing', 'shipped', 'completed'])) {
            return redirect()->to('checkout/success/' . $invoice)
                ->with('success', 'Pembayaran Anda sudah berhasil diproses!');
        }

        // Jika snap_token kosong atau expired, generate ulang
        if (empty($order->snap_token)) {
            try {
                $this->_initMidtrans();

                // Rebuild cart dari detail order untuk regenerate token
                $details = model('PenjualanDetailModel')->getDetailByPenjualan($order->id);
                $cartForToken = [];
                foreach ($details as $d) {
                    $cartForToken[$d->product_id] = [
                        'product_id' => $d->product_id,
                        'name'       => $d->product_name,
                        'price'      => $d->selling_price,
                        'qty'        => $d->qty,
                    ];
                }

                $snapToken = $this->_generateSnapToken(
                    $order->midtrans_order_id,
                    $order->invoice_no,
                    $cartForToken,
                    (int)$order->subtotal_produk,
                    (int)$order->shipping_cost,
                    (int)$order->handling_fee,
                    (int)$order->diskon,
                    //(int)$order->grand_total,
                    );
                model('PenjualanModel')->update($order->id, ['snap_token' => $snapToken]);
                $order->snap_token = $snapToken;

            } catch (\Exception $e) {
                log_message('error', '[Checkout::pay] Gagal regenerate token: ' . $e->getMessage());
                return redirect()->to('account/orders')
                    ->with('error', 'Gagal memuat halaman pembayaran. Hubungi kami untuk bantuan. Invoice: ' . $invoice);
            }
        }

        $this->data['order']             = $order;
        $this->data['title']             = 'Selesaikan Pembayaran';
        $this->data['midtransClientKey'] = env('MIDTRANS_CLIENT_KEY');
        $this->data['isProduction']      = (bool) env('MIDTRANS_IS_PRODUCTION', false);

        return view('themes/indomarket/pages/checkout_pay', $this->data);
    }

    // ---------------------------------------------------------------
    // MIDTRANS RETURN — dipanggil setelah user selesai di Snap
    // (via JavaScript redirect dari snap.pay() callbacks)
    // ---------------------------------------------------------------
    public function midtransReturn(string $invoice)
    {
        if (!$this->auth->loggedIn()) {
            return redirect()->to('auth/login');
        }

        // Beri waktu webhook Midtrans untuk memperbarui status
        // (dalam kasus race condition antara redirect dan webhook)
        sleep(1);

        $order = model('PenjualanModel')->getOrderByInvoice($invoice);

        if (!$order || (int) ($order->customer_id ?? 0) !== (int) $this->currentUser->id) {
            return redirect()->to('account/orders');
        }

        // Arahkan berdasarkan status terkini
        if (in_array($order->order_status, ['processing', 'shipped', 'completed'])) {
            return redirect()->to('checkout/success/' . $invoice)
                ->with('success', 'Pembayaran berhasil! Pesanan Anda sedang diproses.');
        }

        if ($order->order_status === 'cancelled') {
            return redirect()->to('account/orders')
                ->with('error', 'Pembayaran gagal atau kadaluarsa. Silakan buat pesanan baru.');
        }

        // Masih pending (belum ada konfirmasi dari Midtrans)
        return redirect()->to('checkout/success/' . $invoice)
            ->with('info', 'Pembayaran Anda sedang diverifikasi. Kami akan memberitahu Anda melalui email setelah dikonfirmasi.');
    }

    // ---------------------------------------------------------------
    // SUCCESS PAGE
    // Handle kedua flow: manual payment dan Midtrans
    // ---------------------------------------------------------------
    public function success(string $invoice)
    {
        // Halaman success bisa diakses tanpa login untuk lacak order
        // tapi jika login, pastikan order milik user
        $order = model('PenjualanModel')->getOrderByInvoice($invoice);

        if (!$order) {
            return redirect()->to('/');
        }

        // Jika user login, pastikan order miliknya
        if ($this->auth->loggedIn()
            && !empty($order->customer_id)
            && (int) $order->customer_id !== (int) $this->currentUser->id
        ) {
            return redirect()->to('account/orders');
        }

        $this->data['order'] = $order;
        $this->data['title'] = 'Pesanan Berhasil Dibuat!';

        return view('themes/indomarket/pages/checkout_success', $this->data);
    }

    // ---------------------------------------------------------------
    // UPLOAD FORM (Manual payment: QRIS/Transfer)
    // ---------------------------------------------------------------
    public function uploadForm(string $invoice)
    {
        $order = model('PenjualanModel')->getOrderByInvoice($invoice);

        if (!$order || (int) ($order->customer_id ?? 0) !== (int) $this->currentUser->id) {
            return redirect()->to('/');
        }

        $this->data['order'] = $order;
        $this->data['title'] = 'Upload Bukti Pembayaran';
        
        return view('themes/indomarket/pages/upload_bukti', $this->data);
    }

    // ---------------------------------------------------------------
    // UPLOAD BUKTI BAYAR (Manual payment: QRIS/Transfer)
    // ---------------------------------------------------------------
    public function uploadBukti(string $invoice)
    {
        $order = model('PenjualanModel')->getOrderByInvoice($invoice);

        if (!$order || (int) ($order->customer_id ?? 0) !== (int) $this->currentUser->id) {
            return redirect()->to('/');
        }

        $file = $this->request->getFile('bukti_bayar');

        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid. Silakan upload ulang.');
        }

        $allowedExt = ['jpg', 'jpeg', 'png', 'pdf'];
        if (!in_array(strtolower($file->getExtension()), $allowedExt)) {
            return redirect()->back()
                ->with('error', 'Format file tidak didukung. Gunakan JPG, PNG, atau PDF.');
        }

        if ($file->getSize() > 5 * 1024 * 1024) {
            return redirect()->back()->with('error', 'Ukuran file maksimal 5MB.');
        }

        $uploadDir = FCPATH . 'uploads/bukti_bayar';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = $file->getRandomName();
        $file->move($uploadDir, $fileName);

        model('PenjualanModel')->update($order->id, [
            'payment_proof'  => 'uploads/bukti_bayar/' . $fileName,
            'order_status'   => 'pending',
            'payment_status' => 'pending_verification',
        ]);

        //agar user tidak bisa akses checkout/upload/nomor invoice
       
        return redirect()->to('account/orders/detail/' . $order->id)
            ->with('success', 'Bukti pembayaran berhasil diupload! Menunggu verifikasi dari tim kami.');
    }

    // ================================================================
    // PRIVATE HELPER METHODS
    // ================================================================

    /**
     * Inisialisasi konfigurasi Midtrans dari .env
     */
    private function _initMidtrans(): void
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');

        if (empty($serverKey)) {
            throw new \Exception('MIDTRANS_SERVER_KEY belum dikonfigurasi di .env');
        }

        \Midtrans\Config::$serverKey    = $serverKey;
        \Midtrans\Config::$isProduction = (bool) env('MIDTRANS_IS_PRODUCTION', false);
        \Midtrans\Config::$isSanitized  = true; // Bersihkan input sebelum dikirim
        \Midtrans\Config::$is3ds        = true; // Wajibkan 3DS untuk kartu kredit
    }

    /**
     * Generate Snap Token dari Midtrans API
     *
     * @param string $orderId  Nomor invoice kita (jadi order_id di Midtrans)
     * @param array  $cart       Data keranjang belanja
     * @param int    $totalAmount Total pembayaran dalam Rupiah
     * @return string Snap token
     * @throws \Exception Jika Midtrans API gagal
     */
    private function _generateSnapToken(
        string $midtransOrderId,
        string $invoiceNo,
        array $cart,
        int $subtotal,
        int $shippingCost,
        int $handlingFee,
        int $diskon
        ): string
    {
        $totalAmount = $subtotal + $shippingCost + $handlingFee - $diskon;
        // Build item details (Midtrans requires this match gross_amount)
        $itemDetails = [];
        foreach ($cart as $item) {
            $itemDetails[] = [
                'id'       => 'PROD-' . ($item['product_id'] ?? 0),
                'price'    => (int) $item['price'],
                'quantity' => (int) $item['qty'],
                // Midtrans max 50 karakter untuk nama item
                'name'     => mb_substr($item['name'] ?? 'Produk', 0, 50),
            ];
        }

        if ($handlingFee > 0) {
            $itemDetails[] = [
                'id'       => 'HANDLING',
                'price'    => $handlingFee,
                'quantity' => 1,
                'name'     => 'Biaya Penanganan'
            ];
        }

        if ($shippingCost > 0) {
            $itemDetails[] = [
                'id'       => 'SHIPPING',
                'price'    => $shippingCost,
                'quantity' => 1,
                'name'     => 'Biaya Pengiriman'
            ];
        }

        if ($diskon > 0) {
            $itemDetails[] = [
                'id'       => 'DISCOUNT',
                'price'    => -$diskon,
                'quantity' => 1,
                'name'     => 'Diskon'
            ];
        }

        // Data customer dari user yang sedang login
        $customerDetails = [
            'first_name' => $this->currentUser->first_name ?? 'Pelanggan',
            'last_name'  => $this->currentUser->last_name  ?? '',
            'email'      => $this->currentUser->email      ?? '',
            'phone'      => $this->request->getPost('no_hp_penerima')
                            ?? $this->currentUser->phone
                            ?? '',
        ];

        $params = [
            'transaction_details' => [
                'order_id'     => $midtransOrderId,     // Harus unik & match dengan invoice kita
                'gross_amount' => $totalAmount,   // Total dalam Rupiah (integer)
            ],
            'customer_details' => $customerDetails,
            'item_details'     => $itemDetails,

            // URL callbacks setelah pembayaran
            'callbacks' => [
                'finish' => site_url('checkout/midtransReturn/' . $invoiceNo),
            ],

            // Expiry: order expired setelah 24 jam jika belum dibayar
            'expiry' => [
                'start_time' => date('Y-m-d H:i:s O'),
                'unit'       => 'hours',
                'duration'   => 24,
            ],
        ];

        // Panggil Midtrans API
        return \Midtrans\Snap::getSnapToken($params);
    }
}