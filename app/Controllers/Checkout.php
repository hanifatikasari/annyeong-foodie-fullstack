<?php

namespace App\Controllers;

use App\Models\PenjualanModel;
use App\Models\PenjualanDetailModel;
use App\Models\ProductInventoryModel;

class Checkout extends BaseController
{
    public function __construct()
    {
        helper('cart_helper');
    }

    public function index()
    {
        $cart = get_cart();
        if (empty($cart)) {
            return redirect()->to('cart')->with('error', 'Keranjang Anda kosong.');
        }

        $this->data['cart']     = $cart;
        $this->data['total']    = cart_total();
        $this->data['user']     = $this->currentUser;
        $this->data['title']    = 'Checkout';

        return view('themes/indomarket/pages/checkout', $this->data);
    }

    public function process()
    {
        $cart = get_cart();
        if (empty($cart)) {
            return redirect()->to('cart');
        }

        $rules = [
            'phone'    => 'required',
            'shipping_address' => 'required|min_length[10]',
            'pembayaran'        => 'required|in_list[QRIS,Transfer]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $penjualanModel = new PenjualanModel();
            $detailModel    = new PenjualanDetailModel();
            $invModel       = new ProductInventoryModel();

            $totalHarga = cart_total();
            $invoiceNo  = $penjualanModel->generateInvoiceNo();

            $orderId = $penjualanModel->insert([
                'invoice_no'        => $invoiceNo,
                'total_harga'       => $totalHarga,
                'diskon'            => 0,
                'total_bayar'       => $totalHarga,
                'pembayaran'        => $this->request->getPost('pembayaran'),
                'uang_diterima'     => 0,
                'kasir_id'          => $this->currentUser->id,
                'customer_id'       => $this->currentUser->id,
                'order_status'      => 'Pending',
                'payment_status'    => 'menunggu_pembayaran',
                'phone'             => $this->request->getPost('phone'),
                'shipping_address'  => $this->request->getPost('shipping_address'),
                'catatan_customer'  => $this->request->getPost('catatan_customer'),
            ], true);

            foreach ($cart as $item) {
                $product = model('ProductModel')->find($item['product_id']);
                $hpp     = $product->hpp_total ?? 0;

                $detailModel->insert([
                    'penjualan_id' => $orderId,
                    'product_id'   => $item['product_id'],
                    'qty'          => $item['qty'],
                    'hpp_price'    => $hpp,
                    'selling_price'=> $item['price'],
                    'subtotal'     => $item['price'] * $item['qty'],
                ]);

                // Kurangi stok
                $inv = $invModel->where('product_id', $item['product_id'])->first();
                if ($inv) {
                    $newQty = max(0, $inv->qty - $item['qty']);
                    $invModel->update($inv->id, ['qty' => $newQty]);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi gagal.');
            }

            clear_cart();
            return redirect()->to('checkout/success/' . $invoiceNo);

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }
    }

    public function success(string $invoice)
    {
        $order = model('PenjualanModel')->getOrderByInvoice($invoice);
        if (!$order || $order['customer_id'] != $this->currentUser->id) {
            return redirect()->to('/');
        }

        $this->data['order'] = $order;
        $this->data['title'] = 'Pesanan Berhasil!';
        return view('themes/indomarket/pages/checkout_success', $this->data);
    }

    public function uploadForm(string $invoice)
    {
        $order = model('PenjualanModel')->getOrderByInvoice($invoice);
        if (!$order || $order['customer_id'] != $this->currentUser->id) {
            return redirect()->to('/');
        }

        $this->data['order'] = $order;
        $this->data['title'] = 'Upload Bukti Pembayaran';
        return view('themes/indomarket/pages/upload_bukti', $this->data);
    }

    public function uploadBukti(string $invoice)
    {
        $order = model('PenjualanModel')->getOrderByInvoice($invoice);
        if (!$order || $order['customer_id'] != $this->currentUser->id) {
            return redirect()->to('/');
        }

        $file = $this->request->getFile('payment_proof');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid.');
        }

        $allowed = ['jpg','jpeg','png','pdf'];
        if (!in_array(strtolower($file->getExtension()), $allowed)) {
            return redirect()->back()->with('error', 'Format file tidak didukung. Gunakan JPG, PNG, atau PDF.');
        }

        $fileName = $file->getRandomName();
        $file->move(FCPATH . 'uploads/payment_proof', $fileName);

        model('PenjualanModel')->update($order['id'], [
            'payment_proof'  => 'uploads/payment_proof/' . $fileName,
            'order_status' => 'Pending',
        ]);

        return redirect()->to('account/orders/' . $invoice)->with('success', 'Bukti pembayaran berhasil diupload! Menunggu verifikasi admin.');
    }
}