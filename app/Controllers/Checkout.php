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

        $cart = get_cart();
        if (empty($cart)) {
            return redirect()->to('cart')
                ->with('error', 'Keranjang Anda kosong. Silakan pilih produk terlebih dahulu.');
        }

        // Ambil data user terbaru dari DB (bukan session)
        $user = \Config\Database::connect()
            ->table('users')
            ->where('id', $this->currentUser->id)
            ->get()
            ->getRow();

        $this->data['cart']  = get_cart();
        $this->data['total'] = cart_total();
        $this->data['user']  = $user ?? $this->currentUser;
        $this->data['title'] = 'Checkout';

        return view('themes/indomarket/pages/checkout', $this->data);
    }

    // ---------------------------------------------------------------
    // PROCESS ORDER
    // Menangani semua metode pembayaran: QRIS, Transfer, Midtrans
    // ---------------------------------------------------------------
    public function process()
    {
        $cart = get_cart();
        if (empty($cart)) {
            return redirect()->to('cart');
        }

        // *** Validasi input form ***
        // Tambah 'Midtrans' ke in_list
        $rules = [
            'nama_penerima'     => ['rules' => 'required|min_length[3]',  'label' => 'Nama Penerima'],
            'no_hp_penerima'    => ['rules' => 'required|min_length[6]',  'label' => 'Nomor HP'],
            'alamat_pengiriman' => ['rules' => 'required|min_length[5]',  'label' => 'Alamat Pengiriman'],
            'pembayaran'        => ['rules' => 'required|in_list[QRIS,Transfer,Midtrans]', 'label' => 'Metode Pembayaran'],
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $pembayaran = $this->request->getPost('pembayaran');
        $totalHarga = cart_total();

        // ============================================================
        // MIDTRANS PRE-CHECK:
        // Generate snap token SEBELUM membuat order.
        // Jika Midtrans tidak available, tolak sebelum order dibuat.
        // ============================================================
        $snapToken = null;
        if ($pembayaran === 'Midtrans') {
            // Generate invoice_no dulu (tanpa menyimpan ke DB)
            $penjualanModelTemp = new PenjualanModel();
            $invoiceNoTemp      = $penjualanModelTemp->generateInvoiceNo();

            try {
                $this->_initMidtrans();
                $snapToken = $this->_generateSnapToken($invoiceNoTemp, $cart, $totalHarga);
            } catch (\Exception $e) {
                log_message('error', '[Checkout Midtrans] Gagal generate snap token: ' . $e->getMessage());
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Layanan pembayaran Midtrans sedang tidak tersedia. Silakan pilih Transfer atau QRIS, atau coba beberapa saat lagi.');
            }
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $penjualanModel = new PenjualanModel();
            $detailModel    = new PenjualanDetailModel();

            // Gunakan invoice yang sudah digenerate (jika Midtrans)
            // atau generate baru (jika manual)
            $invoiceNo = isset($invoiceNoTemp) ? $invoiceNoTemp : $penjualanModel->generateInvoiceNo();

            // *** Simpan header order ***
            $orderId = $penjualanModel->insert([
                'invoice_no'       => $invoiceNo,
                'customer_id'      => $this->currentUser->id,
                'kasir_id'         => $this->currentUser->id,
                'total_harga'      => $totalHarga,
                'diskon'           => 0,
                'total_bayar'      => $totalHarga,
                'pembayaran'       => $pembayaran,
                'uang_diterima'    => 0,
                'order_status'     => 'pending_payment',
                'payment_status'   => 'menunggu_pembayaran',
                'shipping_address' => $this->request->getPost('alamat_pengiriman'),
                'catatan_customer' => $this->request->getPost('catatan_customer') ?? '',
                'snap_token'       => $snapToken, // null untuk manual payment
            ], true);

            if (!$orderId) {
                throw new \Exception('Gagal membuat order. Coba lagi.');
            }

            // *** Simpan detail item + kurangi stok ***
            $invModel = model('ProductInventoryModel');

            foreach ($cart as $item) {
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

                // Kurangi stok
                $inv = $invModel->where('product_id', $item['product_id'])->first();
                if ($inv) {
                    $newQty = max(0, (int) $inv->qty - $qty);
                    $invModel->update($inv->id, ['qty' => $newQty]);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi database gagal. Silakan coba lagi.');
            }

            // Kosongkan keranjang
            clear_cart();

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
        if (in_array($order->order_status, ['processing', 'verified', 'completed'])) {
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

                $snapToken = $this->_generateSnapToken($invoice, $cartForToken, $order->total_bayar);
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
        if (in_array($order->order_status, ['processing', 'verified', 'completed'])) {
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
            'order_status'   => 'pending_verification',
            'payment_status' => 'menunggu_verifikasi',
        ]);

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
     * @param string $invoiceNo  Nomor invoice kita (jadi order_id di Midtrans)
     * @param array  $cart       Data keranjang belanja
     * @param int    $totalAmount Total pembayaran dalam Rupiah
     * @return string Snap token
     * @throws \Exception Jika Midtrans API gagal
     */
    private function _generateSnapToken(string $invoiceNo, array $cart, int $totalAmount): string
    {
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
                'order_id'     => $invoiceNo,     // Harus unik & match dengan invoice kita
                'gross_amount' => $totalAmount,   // Total dalam Rupiah (integer)
            ],
            'customer_details' => $customerDetails,
            'item_details'     => $itemDetails,

            // URL callbacks setelah pembayaran
            'callbacks' => [
                'finish' => site_url('checkout/return/' . $invoiceNo),
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