<?php

namespace App\Controllers;

class Track extends BaseController
{
    public function index()
    {
        $invoice = trim($this->request->getGet('invoice'));

        if (empty($invoice)) {
            return redirect()->back()
                ->with('error', 'Silakan masukkan nomor invoice.');
        }

        $penjualanModel = model('PenjualanModel');
        $detailModel    = model('PenjualanDetailModel');

       $order = $penjualanModel->getOrderWithCustomerByInvoice($invoice);

        if (!$order) {
            return redirect()->back()
                ->with('error', 'Nomor invoice tidak ditemukan.');
        }

        $details = $detailModel->getDetailByPenjualan($order->id);

        $this->data['title']   = 'Lacak Pesanan';
        $this->data['order']   = $order;
        $this->data['details'] = $details;

        return view('themes/indomarket/pages/track_result', $this->data);
    }
}