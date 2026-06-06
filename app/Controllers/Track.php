<?php

namespace App\Controllers;

class Track extends BaseController
{
    /**
     * Menampilkan form lacak pesanan
     */
    public function index()
    {
        $this->data['title'] = 'Lacak Pesanan';

        return view(
            'themes/indomarket/pages/track',
            $this->data
        );
    }

    /**
     * Cari invoice
     */
    public function search()
    {
        $invoice_no = trim(
            $this->request->getGet('invoice_no')
        );

        if (empty($invoice_no)) {
            return redirect()->to('track')
                ->with('error', 'Silakan masukkan nomor invoice.');
        }

        $penjualanModel = model('PenjualanModel');
        $detailModel    = model('PenjualanDetailModel');

        $order = $penjualanModel
            ->getOrderWithCustomerByInvoice($invoice_no);

        if (!$order) {
            return redirect()->to('track')
                ->with('error', 'Nomor invoice tidak ditemukan.');
        }

        $details = $detailModel
            ->getDetailByPenjualan($order->id);

        $this->data['title']      = 'Lacak Pesanan';
        $this->data['order']      = $order;
        $this->data['details']    = $details;
        $this->data['invoice_no'] = $invoice_no;

        return view(
            'themes/indomarket/pages/track_result',
            $this->data
        );
    }
}