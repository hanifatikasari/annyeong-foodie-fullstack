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
        $this->data['cart']       = get_cart();
        $this->data['total']      = cart_total();
        $this->data['cartCount']  = cart_count();
        $this->data['categories'] = model('CategoryModel')->getStorefrontCategories();
        $this->data['title']      = 'Keranjang Belanja';

        return view('themes/indomarket/pages/cart', $this->data);
    }

    public function add()
    {
        $productId = (int) $this->request->getPost('product_id');
        $qty       = max(1, (int) ($this->request->getPost('qty') ?? 1));

        $success = add_to_cart($productId, $qty);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => $success,
                'count'   => cart_count(),
                'message' => $success ? 'Produk ditambahkan ke keranjang!' : 'Stok tidak mencukupi.',
            ]);
        }

        if ($success) {
            return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang!');
        }

        return redirect()->back()->with('error', 'Stok tidak mencukupi.');
    }

    // ---------------------------------------------------------------
    // FIX ISSUE #7: Support action 'increment' dan 'decrement'
    // sehingga tombol [+] dan [-] bisa kirim form kecil
    // ---------------------------------------------------------------
    public function update()
    {
        $productId = (int) $this->request->getPost('product_id');
        $qty       = (int) $this->request->getPost('qty');
        $action    = $this->request->getPost('action'); // 'set', 'increment', 'decrement'

        $cart = get_cart();

        if ($action === 'increment' && isset($cart[$productId])) {
            $qty = $cart[$productId]['qty'] + 1;
        } elseif ($action === 'decrement' && isset($cart[$productId])) {
            $qty = $cart[$productId]['qty'] - 1;
        }

        update_cart($productId, $qty);

        if ($this->request->isAJAX()) {
            $cart  = get_cart();
            $item  = $cart[$productId] ?? null;
            return $this->response->setJSON([
                'success'   => true,
                'newQty'    => $item ? $item['qty'] : 0,
                'subtotal'  => $item ? number_format($item['price'] * $item['qty']) : '0',
                'cartTotal' => number_format(cart_total()),
                'count'     => cart_count(),
            ]);
        }

        return redirect()->to('cart')->with('success', 'Keranjang diperbarui.');
    }

    public function remove()
    {
        $productId = (int) $this->request->getPost('product_id');
        remove_from_cart($productId);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success'   => true,
                'count'     => cart_count(),
                'cartTotal' => number_format(cart_total()),
            ]);
        }

        return redirect()->to('cart')->with('success', 'Produk dihapus dari keranjang.');
    }

    public function clear()
    {
        clear_cart();
        return redirect()->to('cart');
    }
}