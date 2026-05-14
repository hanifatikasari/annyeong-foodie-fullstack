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
    // FIX ISSUE #4: Preload user data dengan null-safe operator
    // FIX ISSUE #5: Sudah diproteksi oleh FrontendAuthFilter,
    //               tapi juga cek di sini sebagai safety net
    // ---------------------------------------------------------------
    public function index()
    {
        // Safety net: jika user belum login, redirect ke login
        if (!$this->auth->loggedIn() || !$this->currentUser) {
            session()->set('redirect_url', current_url());

            return redirect()->to('auth/login')
                ->with('message', 'Silakan login terlebih dahulu untuk melanjutkan checkout.');
        }
        $cart = get_cart();
        if (empty($cart)) {
            return redirect()->to('cart')
                ->with('error', 'Keranjang Anda kosong. Silakan pilih produk terlebih dahulu.');
        }

        // Ambil data user terbaru dari DB (bukan dari session)
        $user = \Config\Database::connect()
            ->table('users')
            ->where('id', $this->currentUser->id)
            ->get()
            ->getRow();

        $this->data['cart']  = $cart;
        $this->data['total'] = cart_total();
        $this->data['user']  = $user ?? $this->currentUser;
        $this->data['title'] = 'Checkout';

        return view('themes/indomarket/pages/checkout', $this->data);
    }

    // ---------------------------------------------------------------
    // PROCESS ORDER
    // FIX ISSUE #4:
    //   - Nama field validasi harus 100% cocok dengan name= di form HTML
    //   - 'no_hp_penerima' dan 'alamat_pengiriman' adalah nama field yang benar
    //   - Hapus min_length[10] untuk alamat — terlalu strict
    //   - Tambahkan penanganan error yang jelas
    // ---------------------------------------------------------------
    public function process()
    {
        $cart = get_cart();
        if (empty($cart)) {
            return redirect()->to('cart');
        }

        // Validasi — nama field HARUS sama persis dengan name= di <input>/<textarea>
        $rules = [
            'nama_penerima'     => [
                'rules' => 'required|min_length[3]|max_length[100]',
                'label' => 'Nama Penerima',
            ],
            'no_hp_penerima'    => [
                'rules' => 'required|min_length[6]|max_length[20]',
                'label' => 'Nomor HP',
            ],
            'alamat_pengiriman' => [
                'rules' => 'required|min_length[5]',
                'label' => 'Alamat Pengiriman',
            ],
            'pembayaran' => [
                'rules' => 'required|in_list[QRIS,Transfer]',
                'label' => 'Metode Pembayaran',
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $penjualanModel = new PenjualanModel();
            $detailModel    = new PenjualanDetailModel();

            $totalHarga = cart_total();
            $invoiceNo  = $penjualanModel->generateInvoiceNo();

            // Insert order
            $orderId = $penjualanModel->insert([
                'invoice_no'        => $invoiceNo,
                'total_harga'       => $totalHarga,
                'diskon'            => 0,
                'total_bayar'       => $totalHarga,
                'pembayaran'        => $this->request->getPost('pembayaran'),
                'uang_diterima'     => 0,
                'kasir_id'          => $this->currentUser->id,
                'customer_id'       => $this->currentUser->id,
                'tipe_order'        => 'online',
                'status_order'      => 'pending_payment',
                'nama_penerima'     => $this->request->getPost('nama_penerima'),
                'no_hp_penerima'    => $this->request->getPost('no_hp_penerima'),
                'alamat_pengiriman' => $this->request->getPost('alamat_pengiriman'),
                'catatan_order'     => $this->request->getPost('catatan_order') ?? '',
            ], true);

            if (!$orderId) {
                throw new \Exception('Gagal membuat order.');
            }

            // Insert detail + kurangi stok
            $invModel = model('ProductInventoryModel');

            foreach ($cart as $item) {
                $product = model('ProductModel')->find($item['product_id']);
                $hpp     = $product ? ($product->hpp_total ?? 0) : 0;

                $detailModel->insert([
                    'penjualan_id'  => $orderId,
                    'product_id'    => $item['product_id'],
                    'qty'           => $item['qty'],
                    'hpp_price'     => $hpp,
                    'selling_price' => $item['price'],
                    'subtotal'      => $item['price'] * $item['qty'],
                ]);

                // Kurangi stok dari product_inventories
                $inv = $invModel->where('product_id', $item['product_id'])->first();
                if ($inv) {
                    $newQty = max(0, (int) $inv->qty - (int) $item['qty']);
                    $invModel->update($inv->id, ['qty' => $newQty]);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi database gagal.');
            }

            clear_cart();

            return redirect()->to('checkout/success/' . $invoiceNo);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Checkout error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memproses pesanan. Silakan coba lagi. (' . $e->getMessage() . ')');
        }
    }

    // ---------------------------------------------------------------
    // SUCCESS PAGE
    // ---------------------------------------------------------------
    public function success(string $invoice)
    {
        $order = model('PenjualanModel')->getOrderByInvoice($invoice);

        if (!$order) {
            return redirect()->to('/');
        }

        // Support both array and object return type
        $orderUserId = is_object($order) ? ($order->user_id ?? 0) : ($order['user_id'] ?? 0);

        if ((int) $orderUserId !== (int) $this->currentUser->id) {
            return redirect()->to('/');
        }

        $this->data['order'] = $order;
        $this->data['title'] = 'Pesanan Berhasil Dibuat!';

        return view('themes/indomarket/pages/checkout_success', $this->data);
    }

    // ---------------------------------------------------------------
    // UPLOAD FORM
    // ---------------------------------------------------------------
    public function uploadForm(string $invoice)
    {
        $order = model('PenjualanModel')->getOrderByInvoice($invoice);

        if (!$order) {
            return redirect()->to('/');
        }

        $orderUserId = is_object($order) ? ($order->user_id ?? 0) : ($order['user_id'] ?? 0);

        if ((int) $orderUserId !== (int) $this->currentUser->id) {
            return redirect()->to('/');
        }

        $this->data['order'] = $order;
        $this->data['title'] = 'Upload Bukti Pembayaran';

        return view('themes/indomarket/pages/upload_bukti', $this->data);
    }

    // ---------------------------------------------------------------
    // UPLOAD BUKTI BAYAR
    // ---------------------------------------------------------------
    public function uploadBukti(string $invoice)
    {
        $order = model('PenjualanModel')->getOrderByInvoice($invoice);

        if (!$order) {
            return redirect()->to('/');
        }

        $orderUserId = is_object($order) ? ($order->user_id ?? 0) : ($order['user_id'] ?? 0);
        $orderId     = is_object($order) ? $order->id : $order['id'];

        if ((int) $orderUserId !== (int) $this->currentUser->id) {
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

        // Pastikan folder ada
        $uploadDir = FCPATH . 'uploads/bukti_bayar';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = $file->getRandomName();
        $file->move($uploadDir, $fileName);

        model('PenjualanModel')->update($orderId, [
            'bukti_bayar'  => 'uploads/bukti_bayar/' . $fileName,
            'status_order' => 'pending_verification',
        ]);

        return redirect()->to('account/orders/' . $invoice)
            ->with('success', 'Bukti pembayaran berhasil diupload! Menunggu verifikasi dari tim kami.');
    }
}