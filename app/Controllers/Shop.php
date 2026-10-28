<?php

namespace App\Controllers;

class Shop extends BaseController
{
    public function __construct()
    {
        helper('cart_helper');
    }

    // ---------------------------------------------------------------
    // Helper: Inject storefront categories ke $this->data
    // FIX ISSUE #1: Gunakan getStorefrontCategories()
    // ---------------------------------------------------------------
    protected function injectCategories(): void
    {
        if (!isset($this->data['categories'])) {
            $this->data['categories'] = model('CategoryModel')->getStorefrontCategories();
        }
    }

    // ---------------------------------------------------------------
    // SEARCH
    // ---------------------------------------------------------------
    public function search()
    {
        $this->injectCategories();

        $keyword = trim($this->request->getGet('q') ?? '');
        $perPage = 12;

        $builder = model('ProductModel')
            ->select('products.*, COALESCE(product_inventories.qty, 0) as stok')
            ->join('product_inventories', 'product_inventories.product_id = products.id', 'left')
            ->where('products.parent_id IS NULL')
            ->where('products.published_at IS NOT NULL');

        if ($keyword !== '') {
            $builder->groupStart()
                    ->like('products.name', $keyword)
                    ->orLike('products.sku', $keyword)
                    ->orLike('products.short_description', $keyword)
                    ->groupEnd();
        }

        $results = $builder->paginate($perPage, 'shop');

        // Attach images
        foreach ($results as &$p) {
            $img = model('ProductImageModel')
                ->where('product_id', $p->id)
                ->orderBy('id', 'ASC')
                ->first();
            $p->featured_image = $img;
        }

        $this->data['products'] = $results;
        $this->data['pager']    = model('ProductModel')->pager;
        $this->data['keyword']  = $keyword;
        $this->data['title']    = $keyword !== '' ? 'Hasil Pencarian: "' . esc($keyword) . '"' : 'Semua Produk';

        return view('themes/indomarket/pages/search', $this->data);
    }

    // ---------------------------------------------------------------
    // TRACK ORDER (publik)
    // ---------------------------------------------------------------
    public function track()
    {
        $this->injectCategories();

        // Jika ada invoice dari GET (dari home page form)
        $invoiceFromGet = $this->request->getGet('invoice_no');

        $this->data['invoiceFromGet'] = $invoiceFromGet ?? '';
        $this->data['title']          = 'Lacak Pesanan';

        return view('themes/indomarket/pages/track', $this->data);
    }

    // ---------------------------------------------------------------
    // TRACK RESULT (POST)
    // ---------------------------------------------------------------
    public function trackOrder()
    {
        $this->injectCategories();

        $invoice = trim($this->request->getPost('invoice_no') ?? '');

        if (!$invoice) {
            return redirect()->to('track')->with('error', 'Masukkan nomor invoice terlebih dahulu.');
        }

        $order = model('PenjualanModel')->getOrderByInvoice($invoice);

        if (!$order) {
            $this->data['title']         = 'Pesanan Tidak Ditemukan';
            $this->data['order']         = null;
            $this->data['invoice_input'] = $invoice;
            return redirect()->to('track')->with('error', 'Nomor invoice <strong>' . esc($invoice) . '</strong> tidak ditemukan.');
        }

        $orderId = is_object($order) ? $order->id : $order['id'];
        $details = model('PenjualanDetailModel')->getDetailByPenjualan($orderId);

        $this->data['order']   = $order;
        $this->data['details'] = $details;
        $this->data['title']   = 'Status Pesanan ' . esc($invoice);

        return view('themes/indomarket/pages/track_result', $this->data);
    }

    // ---------------------------------------------------------------
    // ABOUT
    // ---------------------------------------------------------------
    public function about()
    {
        $this->injectCategories();
        $this->data['title'] = 'Tentang Kami';
        return view('themes/indomarket/pages/about', $this->data);
    }

}