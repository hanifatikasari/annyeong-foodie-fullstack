<?php

namespace App\Controllers;

class Cart extends BaseController
{
    public function __construct()
    {
        helper('cart_helper');
    }

    public function index()
    {
        $this->data['cart']  = get_cart();
        $this->data['total'] = cart_total();
        $this->data['title'] = 'Keranjang Belanja';
        return view('themes/indomarket/pages/cart', $this->data);
    }

    public function add()
    {
        $productId = (int) $this->request->getPost('product_id');
        $qty       = (int) $this->request->getPost('qty') ?: 1;

        if (add_to_cart($productId, $qty)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'count'   => cart_count(),
                    'message' => 'Produk ditambahkan ke keranjang!',
                ]);
            }
            return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang!');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Stok tidak mencukupi.']);
        }
        return redirect()->back()->with('error', 'Stok tidak mencukupi.');
    }

    public function update()
    {
        $productId = (int) $this->request->getPost('product_id');
        $qty       = (int) $this->request->getPost('qty');

        update_cart($productId, $qty);
        return redirect()->to('cart')->with('success', 'Keranjang diperbarui.');
    }

    public function remove()
    {
        $productId = (int) $this->request->getPost('product_id');
        remove_from_cart($productId);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'count' => cart_count()]);
        }
        return redirect()->to('cart')->with('success', 'Produk dihapus dari keranjang.');
    }

    public function clear()
    {
        clear_cart();
        return redirect()->to('cart');
    }
}