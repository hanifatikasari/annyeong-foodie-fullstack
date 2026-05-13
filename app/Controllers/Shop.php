<?php

namespace App\Controllers;

class Shop extends BaseController
{
    public function search()
    {
        helper('cart_helper');
        $keyword  = $this->request->getGet('q');
        $perPage  = 12;

        $productModel = model('ProductModel');

        $products = $productModel
            ->select('products.*, product_inventories.qty as stok')
            ->join('product_inventories', 'products.id = product_inventories.product_id', 'left')
            ->where('products.parent_id IS NULL')
            ->where('products.published_at IS NOT NULL');

        if ($keyword) {
            $products->groupStart()
                     ->like('products.name', $keyword)
                     ->orLike('products.sku', $keyword)
                     ->orLike('products.short_description', $keyword)
                     ->groupEnd();
        }

        $results = $products->paginate($perPage, 'bootstrap');

        foreach ($results as &$p) {
            $img = model('ProductImageModel')->where('product_id', $p->id)->orderBy('id','ASC')->first();
            $p->featured_image = $img;
        }

        $this->data['products'] = $results;
        $this->data['pager']    = $productModel->pager;
        $this->data['keyword']  = $keyword;
        $this->data['title']    = 'Hasil Pencarian: ' . $keyword;

        return view('themes/indomarket/pages/search', $this->data);
    }

    public function track()
    {
        $this->data['title'] = 'Lacak Pesanan';
        return view('themes/indomarket/pages/track', $this->data);
    }

    public function trackOrder()
    {
        $invoice = $this->request->getPost('invoice_no');
        $order   = model('PenjualanModel')->getOrderByInvoice($invoice);

        if (!$order) {
            return redirect()->back()->with('error', 'Nomor invoice tidak ditemukan.');
        }

        $details = model('PenjualanDetailModel')->getDetailByPenjualan($order->id);
        $this->data['order']   = $order;
        $this->data['details'] = $details;
        $this->data['title']   = 'Status Pesanan ' . $invoice;

        return view('themes/indomarket/pages/track_result', $this->data);
    }

    public function about()
    {
        $this->data['title'] = 'Tentang Kami';
        return view('themes/indomarket/pages/about', $this->data);
    }

    public function submitReview()
    {
        helper('cart_helper');
        if (!$this->auth->loggedIn()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Login diperlukan']);
        }

        $productId = (int) $this->request->getPost('product_id');
        $rating    = (int) $this->request->getPost('rating');
        $ulasan    = $this->request->getPost('ulasan');
        $userId    = $this->currentUser->id;

        $reviewModel = model('ReviewModel');
        $existing    = $reviewModel->where('product_id', $productId)->where('user_id', $userId)->first();

        if ($existing) {
            $reviewModel->update($existing->id, ['rating' => $rating, 'ulasan' => $ulasan]);
        } else {
            $reviewModel->save(['product_id' => $productId, 'user_id' => $userId, 'rating' => $rating, 'ulasan' => $ulasan]);
        }

        return redirect()->back()->with('success', 'Ulasan berhasil disimpan!');
    }
}