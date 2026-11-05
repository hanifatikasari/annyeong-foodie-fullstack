<?php

namespace App\Controllers;

class Cart extends BaseController
{
    public function __construct()
    {
        helper('cart_helper');
    }

    // ---------------------------------------------------------------
    // CART INDEX (Shopee Style: checkbox selection & dynamic summary)
    // ---------------------------------------------------------------
    public function index()
    {
        // Sinkronkan harga terlebih dahulu sebelum menghitung ringkasan.
        sync_cart_prices();
        $cart              = get_cart();
        $selectedCart      = get_selected_cart();
        $selectedTotal     = cart_selected_total();
        $selectedCount     = cart_selected_count();
        $selectedItemCount = count($selectedCart);

        // Cek apakah semua produk yang tersedia (stok > 0) sedang tercentang
        $availableItems = array_filter($cart, function($item) {
            return ($item['stok'] ?? 0) > 0;
        });
        $isAllSelected = !empty($availableItems) && ($selectedItemCount >= count($availableItems));

        $this->data['cart']              = $cart;
        $this->data['selectedCart']      = $selectedCart;
        $this->data['total']             = $selectedTotal; // Total default yang dihitung adalah yang terpilih
        $this->data['selectedTotal']     = $selectedTotal;
        $this->data['selectedCount']     = $selectedCount;
        $this->data['selectedItemCount'] = $selectedItemCount;
        $this->data['isAllSelected']     = $isAllSelected;
        $this->data['cartTotal']         = cart_total();
        $this->data['cartCount']         = cart_count();
        $this->data['categories']        = model('CategoryModel')->getStorefrontCategories();
        $this->data['title']             = 'Keranjang Belanja';

        return view('themes/indomarket/pages/cart', $this->data);
    }

    // ---------------------------------------------------------------
    // ADD TO CART
    // Status default: UNCHECKED / tidak langsung terpilih
    // ---------------------------------------------------------------
    public function add()
    {
        $productId = (int) $this->request->getPost('product_id');
        $qty       = max(1, (int) ($this->request->getPost('qty') ?? 1));

        // Validasi: product_id wajib ada dan valid
        if ($productId <= 0) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'count'   => cart_item_count(),
                    'message' => 'Produk tidak valid.',
                ]);
            }
            return redirect()->back()->with('error', 'Produk tidak ditemukan.');
        }

        // Cek apakah produk adalah configurable (parent) — wajib pilih varian dulu
        $productCheck = model('ProductModel')->find($productId);
        if ($productCheck && $productCheck->type === 'configurable') {
            $detailUrl = site_url(
                'products/' . $productCheck->sku . '/' . $productCheck->slug
            );

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success'       => false,
                    'needs_variant' => true,
                    'count'         => cart_item_count(),
                    'message'       => 'Silakan pilih varian produk terlebih dahulu.',
                    'redirect'      => $detailUrl,
                ]);
            }
            return redirect()->to($detailUrl)
                ->with('info', 'Silakan pilih varian produk terlebih dahulu.');
        }

        $success = add_to_cart($productId, $qty);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => $success,
                'count'   => cart_item_count(),
                'message' => $success
                    ? 'masuk keranjang.'
                    : 'Stok tidak mencukupi.',
            ]);
        }

        if ($success) {
            return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang!');
        }

        return redirect()->back()->with('error', 'Stok tidak mencukupi.');
    }

    // ---------------------------------------------------------------
    // UPDATE CART — Support action 'increment' dan 'decrement'
    // ---------------------------------------------------------------
    public function update()
    {
        sync_cart_prices();
        $productId = (int) $this->request->getPost('product_id');
        $qty       = (int) $this->request->getPost('qty');
        $action    = $this->request->getPost('action'); // 'set', 'increment', 'decrement'

        $cart = get_cart();

        if ($action === 'increment' && isset($cart[$productId])) {
            $qty = $cart[$productId]['qty'] + 1;
        } elseif ($action === 'decrement' && isset($cart[$productId])) {
            $qty = $cart[$productId]['qty'] - 1;
        }

        if ($qty < 1) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Jumlah produk minimal 1.',
                'csrf_token' => csrf_hash(),
            ]);
        }

        $updated = update_cart($productId, $qty);

        if (!$updated) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Stok tidak mencukupi atau keranjang tidak valid.',
                'csrf_token' => csrf_hash(),
            ]);
        }

        if ($this->request->isAJAX()) {
            $cart = get_cart();
            $item = $cart[$productId] ?? null;
            return $this->response->setJSON([
                'success'           => true,
                'newQty'            => $item ? $item['qty'] : 0,
                'subtotal'          => $item ? number_format($item['price'] * $item['qty']) : '0',
                'rawSubtotal'       => $item ? ($item['price'] * $item['qty']) : 0,
                'selected'          => $item ? (!empty($item['selected'])) : false,
                'selectedTotal'     => number_format(cart_selected_total()),
                'rawSelectedTotal'  => cart_selected_total(),
                'selectedCount'     => cart_selected_count(),
                'selectedItemCount' => cart_selected_item_count(),
                'cartTotal'         => number_format(cart_total()),
                'count'             => cart_item_count(),
                'csrf_token'        => csrf_hash(),
            ]);
        }

        return redirect()->to('cart')->with('success', 'Keranjang diperbarui.');
    }

    // ---------------------------------------------------------------
    // TOGGLE CHECKBOX ITEM (AJAX)
    // ---------------------------------------------------------------
    public function toggleSelect()
    {
        $productId = (int) $this->request->getPost('product_id');
        $selected  = (bool) $this->request->getPost('selected');

        update_cart_selected($productId, $selected);

        $cart              = get_cart();
        $selectedCart      = get_selected_cart();
        $availableItems    = array_filter($cart, function($item) {
            return ($item['stok'] ?? 0) > 0;
        });
        $isAllSelected     = !empty($availableItems) && (count($selectedCart) >= count($availableItems));

        return $this->response->setJSON([
            'success'           => true,
            'selectedTotal'     => number_format(cart_selected_total()),
            'rawSelectedTotal'  => cart_selected_total(),
            'selectedCount'     => cart_selected_count(),
            'selectedItemCount' => count($selectedCart),
            'isAllSelected'     => $isAllSelected,
            'csrf_token'        => csrf_hash(),
        ]);
    }

    // ---------------------------------------------------------------
    // SELECT ALL ITEMS (AJAX)
    // ---------------------------------------------------------------
    public function selectAll()
    {
        $selected = (bool) $this->request->getPost('selected');

        update_cart_select_all($selected);

        $selectedCart = get_selected_cart();

        return $this->response->setJSON([
            'success'           => true,
            'selectedTotal'     => number_format(cart_selected_total()),
            'rawSelectedTotal'  => cart_selected_total(),
            'selectedCount'     => cart_selected_count(),
            'selectedItemCount' => count($selectedCart),
            'isAllSelected'     => $selected,
            'csrf_token'        => csrf_hash(),
        ]);
    }

    // ---------------------------------------------------------------
    // CHECKOUT PROCESOR DARI KERANJANG
    // Membawa hanya produk yang dicentang ke halaman transaksi
    // ---------------------------------------------------------------
    public function checkout()
    {
        sync_cart_prices();
        $selectedItems = $this->request->getPost('selected_items');

        if (is_array($selectedItems)) {
            set_selected_items($selectedItems);
        }

        $selectedCart = get_selected_cart();

        if (empty($selectedCart)) {
            return redirect()->to('cart')
                ->with('error', 'Silakan pilih (centang) minimal 1 produk untuk melanjutkan checkout.');
        }

        // Hapus session buy_now jika ada agar alur checkout menggunakan keranjang terpilih
        if (session()->has('buy_now')) {
            session()->remove('buy_now');
        }

        return redirect()->to('checkout');
    }

    // ---------------------------------------------------------------
    // HAPUS SATU ITEM
    // ---------------------------------------------------------------
    public function remove()
    {
        $productId = (int) $this->request->getPost('product_id');
        remove_from_cart($productId);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success'           => true,
                'count'             => cart_item_count(),
                'cartTotal'         => number_format(cart_total()),
                'selectedTotal'     => number_format(cart_selected_total()),
                'rawSelectedTotal'  => cart_selected_total(),
                'selectedCount'     => cart_selected_count(),
                'selectedItemCount' => cart_selected_item_count(),
                'csrf_token'        => csrf_hash(),
            ]);
        }

        return redirect()->to('cart')->with('success', 'Produk dihapus dari keranjang.');
    }

    public function updateDirect()
{
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(403);
    }

    $productId = $this->request->getPost('product_id');
    $newQty    = (int) $this->request->getPost('qty');

    // Lakukan validasi stok dan pembaruan session cart di sini...
    // Kembalikan response JSON berisi status success, subtotal, dan csrf_token baru.
}

    // ---------------------------------------------------------------
    // HAPUS HANYA PRODUK YANG SEDANG DICENTANG
    // ---------------------------------------------------------------
    public function removeSelected()
    {
        $selectedItems = $this->request->getPost('selected_items');

        if (is_array($selectedItems) && !empty($selectedItems)) {
            remove_selected_items($selectedItems);
        } else {
            remove_selected_from_cart();
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success'           => true,
                'count'             => cart_item_count(),
                'cartTotal'         => number_format(cart_total()),
                'selectedTotal'     => number_format(cart_selected_total()),
                'rawSelectedTotal'  => cart_selected_total(),
                'selectedCount'     => cart_selected_count(),
                'selectedItemCount' => cart_selected_item_count(),
                'csrf_token'        => csrf_hash(),
            ]);
        }

        return redirect()->to('cart')->with('success', 'Produk yang dipilih berhasil dihapus dari keranjang.');
    }

    // ---------------------------------------------------------------
    // KOSONGKAN SELURUH KERANJANG
    // ---------------------------------------------------------------
    public function clear()
    {
        clear_cart();
        return redirect()->to('cart');
    }

    
    public function syncPrices()
    {
        $cart = sync_cart_prices();
        $items = [];

        foreach ($cart as $productId => $item) {
            $items[] = [
                'product_id' => (int) $productId,
                'price'      => (int) $item['price'],
                'qty'        => (int) $item['qty'],
                'stok'       => isset($item['stok']) ? (int) $item['stok'] : null,
                'subtotal'   => (int) $item['price'] * (int) $item['qty'],
            ];
        }

        return $this->response->setJSON([
            'success'    => true,
            'items'      => $items,
            'csrf_token' => csrf_hash(),
        ]);
    }
 
    // ---------------------------------------------------------------
    // BUY NOW
    // ---------------------------------------------------------------
    public function buyNow()
    {
        $productId = (int) $this->request->getPost('product_id');
        $qty       = max(1, (int) ($this->request->getPost('qty') ?? 1));

        if ($productId <= 0) return $this->response->setJSON(['success'=>false,'message'=>'Produk tidak valid.']);

        $product = model('ProductModel')
            ->select('products.*, product_inventories.qty as stok')
            ->join(
                'product_inventories',
                'products.id = product_inventories.product_id',
                'left'
            )
            ->find($productId);
        if (!$product) return $this->response->setJSON(['success'=>false,'message'=>'Produk tidak ditemukan.']);

        if ($product->type === 'configurable') 
            return $this->response->setJSON([
                'success'=>false,
                'needs_variant'=>true,
                'redirect'=>site_url("products/{$product->sku}/{$product->slug}")
            ]);

        if ($product->stok < $qty) 
            return $this->response->setJSON(['success'=>false,'message'=>'Stok tidak mencukupi.']);

        session()->set('buy_now', [
            'product_id' => $product->id,
            'qty'        => $qty,
            'name'       => $product->name,
            'price'      => $product->price,
            'sku'        => $product->sku,
            'image'      => $product->image ?? null,
        ]);

        return $this->response->setJSON(['success'=>true,'redirect'=>site_url('checkout')]);
    }
}
