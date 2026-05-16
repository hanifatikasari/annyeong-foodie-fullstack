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
            ->getRow(); // object

        $this->data['cart']  = get_cart();
        $this->data['total'] = cart_total();
        $this->data['user']  = $user ?? $this->currentUser;
        $this->data['title'] = 'Checkout';

        return view('themes/indomarket/pages/checkout', $this->data);
    }

    // ---------------------------------------------------------------
    // PROCESS ORDER
    // FIX ISSUE #2 & #3:
    //   - Gunakan 'customer_id' (bukan user_id)
    //   - Gunakan 'invoice_no'    (bukan invoice)
    //   - Gunakan 'shipping_address' (bukan alamat_pengiriman)
    //   - Gunakan 'payment_proof'   (bukan bukti_bayar)
    //   - Gunakan 'order_status'    (bukan status_order)
    //   - Gunakan 'selling_price'   di detail (bukan price)
    //   - Validasi min_length lebih longgar agar tidak false-fail
    // ---------------------------------------------------------------
    public function process()
    {
        $cart = get_cart();
        if (empty($cart)) {
            return redirect()->to('cart');
        }

        // *** Validasi — nama field harus sama persis dengan name= di form ***
        $rules = [
            'nama_penerima'     => ['rules' => 'required|min_length[3]',  'label' => 'Nama Penerima'],
            'no_hp_penerima'    => ['rules' => 'required|min_length[6]',  'label' => 'Nomor HP'],
            'alamat_pengiriman' => ['rules' => 'required|min_length[5]',  'label' => 'Alamat Pengiriman'],
            'pembayaran'        => ['rules' => 'required|in_list[QRIS,Transfer]', 'label' => 'Metode Pembayaran'],
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

            // FIX #3: customer_id, invoice, shipping_address, order_status
           $orderId = $penjualanModel->insert([
            'invoice_no'       => $invoiceNo,
            'customer_id'      => $this->currentUser->id,
            'kasir_id'         => $this->currentUser->id,
            'total_harga'      => $totalHarga,
            'diskon'           => 0,
            'total_bayar'      => $totalHarga,
            'pembayaran'       => $this->request->getPost('pembayaran'),
            'uang_diterima'    => 0,
            'order_status'     => 'pending_payment',
            'payment_status'   => 'menunggu_pembayaran',
            'shipping_address' => $this->request->getPost('alamat_pengiriman'),
            'catatan_customer' => $this->request->getPost('catatan_customer') ?? '',
        ], true); // true = return insert ID

            if (!$orderId) {
                throw new \Exception('Gagal membuat order. Coba lagi.');
            }

            // Insert detail + kurangi stok
            $invModel = model('ProductInventoryModel');

            foreach ($cart as $item) {
                $product = model('ProductModel')->find($item['product_id']);
                $hpp     = $product ? ($product->hpp_total ?? 0) : 0;
                $price   = $item['price'];
                $qty     = $item['qty'];

                // FIX #2: kolom 'selling_price' bukan 'price'
                $detailModel->insert([
                    'penjualan_id' => $orderId,
                    'product_id'   => $item['product_id'],
                    'qty'          => $qty,
                    'selling_price' => $price,   // DB: selling_price
                    'subtotal'     => $price * $qty,
                    'hpp_price'    => $hpp,
                ]);

                // Kurangi stok inventori
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

            clear_cart();

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
    // SUCCESS PAGE
    // ---------------------------------------------------------------
    public function success(string $invoice)
    {
        $order = model('PenjualanModel')->getOrderByInvoice($invoice);

        if (!$order) {
            return redirect()->to('/');
        }

        // FIX #3: gunakan customer_id
        if ((int) ($order->customer_id ?? 0) !== (int) $this->currentUser->id) {
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

        if (!$order || (int) ($order->customer_id ?? 0) !== (int) $this->currentUser->id) {
            return redirect()->to('/');
        }

        $this->data['order'] = $order;
        $this->data['title'] = 'Upload Bukti Pembayaran';

        return view('themes/indomarket/pages/upload_bukti', $this->data);
    }

    // ---------------------------------------------------------------
    // UPLOAD BUKTI BAYAR
    // FIX #3: Gunakan 'payment_proof' (bukan bukti_bayar)
    //         Gunakan 'order_status'  (bukan status_order)
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

        // FIX #3: kolom payment_proof dan order_status
        model('PenjualanModel')->update($order->id, [
            'payment_proof'  => 'uploads/bukti_bayar/' . $fileName,
            'order_status'   => 'pending_verification',
            'payment_status' => 'paid',
        ]);

        return redirect()->to('account/orders/' . $invoice)
            ->with('success', 'Bukti pembayaran berhasil diupload! Menunggu verifikasi dari tim kami.');
    }
}