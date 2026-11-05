<?php

// Mengambil ID user yang sedang login dari session IonAuth
if (!function_exists('cart_user_id')) {
    function cart_user_id(): int
    {
        return (int) (session()->get('user_id') ?? 0);
    }
}

// Mengambil data produk terbaru untuk dimasukkan ke session cart.
if (!function_exists('cart_get_product_data')) {
    function cart_get_product_data(int $productId, int $qty = 1): ?array
    {
        $product = model('ProductModel')
            ->select('products.*, product_inventories.qty as stok')
            ->join(
                'product_inventories',
                'products.id = product_inventories.product_id',
                'left'
            )
            ->find($productId);

        if (!$product || $product->type === 'configurable') {
            return null;
        }

        $image = model('ProductImageModel')
            ->where('product_id', $productId)
            ->orderBy('id', 'ASC')
            ->first();

        if (!$image && $product->parent_id) {
            $image = model('ProductImageModel')
                ->where('product_id', $product->parent_id)
                ->orderBy('id', 'ASC')
                ->first();
        }

        return [
            'product_id' => $productId,
            'name'       => $product->name,
            'sku'        => $product->sku,
            'price'      => (int) $product->price,
            'qty'        => $qty,
            'stok'       => (int) ($product->stok ?? 0),
            'image'      => $image->small ?? null,
            'selected'   => false,
        ];
    }
}

// Menyimpan satu item keranjang ke database jika user login.
if (!function_exists('cart_save_to_database')) {
    function cart_save_to_database(int $productId, int $qty): void
    {
        $userId = cart_user_id();

        if ($userId <= 0) {
            return;
        }

        model('UserCartModel')->saveCartItem(
            $userId,
            $productId,
            $qty
        );
    }
}

// Menghapus satu item keranjang dari database.
if (!function_exists('cart_delete_from_database')) {
    function cart_delete_from_database(int $productId): void
    {
        $userId = cart_user_id();

        if ($userId <= 0) {
            return;
        }

        model('UserCartModel')->removeCartItem($userId, $productId);
    }
}

// Memuat keranjang akun dari database ke session.
if (!function_exists('cart_load_from_database')) {
    function cart_load_from_database(): array
    {
        $userId = cart_user_id();

        if ($userId <= 0) {
            return [];
        }

        $rows = model('UserCartModel')->getUserCart($userId);
        $cart = [];

        foreach ($rows as $row) {
            $productId = (int) $row['product_id'];
            $item = cart_get_product_data($productId, (int) $row['qty']);

            if (!$item || $item['stok'] <= 0) {
                continue;
            }

            $item['qty'] = min($item['qty'], $item['stok']);
            $cart[$productId] = $item;
        }

        return $cart;
    }
}

/**
 * Menggabungkan keranjang guest dengan keranjang akun.
 * Jika produk sama, jumlahnya dijumlahkan sesuai stok.
 */
if (!function_exists('merge_guest_cart_into_user_cart')) {
    function merge_guest_cart_into_user_cart(): void
    {
        $userId = cart_user_id();

        if ($userId <= 0) {
            return;
        }

        $guestCart = session()->get('cart') ?? [];
        $userCart  = cart_load_from_database();

        foreach ($guestCart as $productId => $guestItem) {
            $productId = (int) $productId;

            if (isset($userCart[$productId])) {
                $totalQty = $userCart[$productId]['qty']
                    + (int) ($guestItem['qty'] ?? 0);

                $userCart[$productId]['qty'] = min(
                    $totalQty,
                    $userCart[$productId]['stok']
                );
            } else {
                if (count($userCart) >= 100) {
                    continue;
                }

                $item = cart_get_product_data(
                    $productId,
                    (int) ($guestItem['qty'] ?? 1)
                );

                if (!$item || $item['stok'] <= 0) {
                    continue;
                }

                $item['qty'] = min($item['qty'], $item['stok']);
                $userCart[$productId] = $item;
            }
        }

        // Simpan hasil gabungan ke database.
        $cartModel = model('UserCartModel');

        foreach ($userCart as $productId => $item) {
            $cartModel->saveCartItem(
                $userId,
                (int) $productId,
                (int) $item['qty']
            );
        }

        session()->set('cart', $userCart);
    }
}

if (!function_exists('get_cart')) {
     //Ambil seluruh item keranjang dari session.
     //Memastikan setiap item memiliki key 'selected' (default: false).

    function get_cart(): array
    {
        $session = session();
        $cart    = $session->get('cart') ?? [];

        // Jika session kosong dan user login, pulihkan dari database.
        if (empty($cart) && cart_user_id() > 0) {
            $cart = cart_load_from_database();
            $session->set('cart', $cart);
        }

        // Normalisasi agar setiap item memiliki key 'selected' boolean
        $modified = false;
        foreach ($cart as $id => $item) {
            if (!isset($item['selected'])) {
                $cart[$id]['selected'] = false;
                $modified = true;
            }
        }

        if ($modified) {
            $session->set('cart', $cart);
        }

        return $cart;
    }
}

    /**
     * Sinkronkan harga produk dalam session cart
     * dengan harga terbaru pada tabel products.
     */
if (!function_exists('sync_cart_prices')) {
    function sync_cart_prices(): array
    {
        $session = session();
        $cart    = get_cart();

        if (empty($cart)) {
            return [];
        }

        
        $productIds = array_map('intval', array_keys($cart));

        $db = \Config\Database::connect();

        $products = $db->table('products p')
            ->select('p.id, p.price, pi.qty AS stok')
            ->join(
                'product_inventories pi',
                'pi.product_id = p.id',
                'left'
            )
            ->whereIn('p.id', $productIds)
            ->get()
            ->getResultArray();

        $latestData = [];

        foreach ($products as $product) {
            $latestData[(int) $product['id']] = [
                'price' => (int) $product['price'],
                'stok' => $product['stok'] !== null
                    ? (int) $product['stok']
                    : null,
            ];
        }
            $changed = false;
            foreach ($cart as $productId => &$item) {
                $id = (int) $productId;

                if (!isset($latestData[$id])) {
                    continue;
                }

                $newPrice = $latestData[$id]['price'];
                $newStock = $latestData[$id]['stok'];

                if ((int) ($item['price'] ?? 0) !== $newPrice) {
                    $item['price'] = $newPrice;
                    $changed = true;
                }

                if (($item['stok'] ?? null) !== $newStock) {
                    $item['stok'] = $newStock;
                    $changed = true;
                }
            }

            unset($item);

        if ($changed) {
            $session->set('cart', $cart);
        }
        return $cart;
    }
}


if (!function_exists('get_selected_cart')) {
    /**
     * Ambil HANYA item keranjang yang sedang dicentang (selected = true).
     */
    function get_selected_cart(): array
    {
        $selected = [];
        foreach (get_cart() as $productId => $item) {
            if (!empty($item['selected'])) {
                $selected[$productId] = $item;
            }
        }
        return $selected;
    }
}

if (!function_exists('add_to_cart')) {
    /**
     * Tambahkan produk ke keranjang.
     *
     * PENTING:
     * - Status default: TIDAK LANGSUNG TERPILIH (selected = false).
     *   Keranjang berfungsi sebagai wadah penyimpanan/wishlist sementara.
     * - Untuk configurable product, $productId yang dikirim HARUS berupa
     *   ID variant (child/simple), bukan ID parent configurable.
     */
    if (!function_exists('add_to_cart')) {
        function add_to_cart(int $productId, int $qty = 1): bool
        {
            if ($qty <= 0) {
                return false;
            }

            $session = session();
            $cart = get_cart();

            $product = model('ProductModel')
                ->select('products.*, product_inventories.qty as stok')
                ->join(
                    'product_inventories',
                    'products.id = product_inventories.product_id',
                    'left'
                )
                ->find($productId);

            if (!$product || $product->type === 'configurable') {
                return false;
            }

            $stok = (int) ($product->stok ?? 0);

            if ($stok <= 0) {
                return false;
            }

            if (isset($cart[$productId])) {
                $newQty = (int) $cart[$productId]['qty'] + $qty;

                if ($newQty > $stok) {
                    return false;
                }

                $cart[$productId]['qty'] = $newQty;
                $cart[$productId]['price'] = (int) $product->price;
                $cart[$productId]['stok'] = $stok;
            } else {
                // Maksimal 100 jenis produk berbeda.
                if (count($cart) >= 100 || $qty > $stok) {
                    return false;
                }

                $item = cart_get_product_data($productId, $qty);

                if (!$item) {
                    return false;
                }

                $cart[$productId] = $item;
            }

            $session->set('cart', $cart);

            // Persistensi akun.
            cart_save_to_database($productId, $cart[$productId]['qty']);

            return true;
        }
    }
}

if (!function_exists('update_cart')) {
    function update_cart(int $productId, int $qty): bool
    {
        $session = session();
        $cart    = get_cart();
        if (!isset($cart[$productId])) {
            return false;
        }
        if ($qty <= 0) {
            unset($cart[$productId]);
            cart_delete_from_database($productId);
        } else {
            if ($qty > (int) $cart[$productId]['stok']) {
                return false;
            }
            $cart[$productId]['qty'] = $qty;
            cart_save_to_database($productId, $qty);
        }
        $session->set('cart', $cart);
        return true;
    }
}

if (!function_exists('update_cart_selected')) {
    /**
     * Update status centang (checkbox) untuk satu produk di keranjang.
     */
    function update_cart_selected(int $productId, bool $selected): bool
    {
        $session = session();
        $cart    = get_cart();
        if (!isset($cart[$productId])) {
            return false;
        }

        // Jika stok kosong, tidak bisa dipilih
        if ($selected && ($cart[$productId]['stok'] <= 0)) {
            $cart[$productId]['selected'] = false;
        } else {
            $cart[$productId]['selected'] = $selected;
        }

        $session->set('cart', $cart);
        return true;
    }
}

if (!function_exists('update_cart_select_all')) {
    /**
     * Pilih semua (Select All) atau Batalkan semua di keranjang.
     */
    function update_cart_select_all(bool $selected): void
    {
        $session = session();
        $cart    = get_cart();
        foreach ($cart as $id => $item) {
            // Hanya bisa dipilih jika stok masih tersedia
            if ($selected && ($item['stok'] <= 0)) {
                $cart[$id]['selected'] = false;
            } else {
                $cart[$id]['selected'] = $selected;
            }
        }
        $session->set('cart', $cart);
    }
}

if (!function_exists('set_selected_items')) {
    /**
     * Set hanya ID produk dalam array $productIds yang selected = true,
     * produk lainnya di keranjang otomatis selected = false.
     */
    function set_selected_items(array $productIds): void
    {
        $session = session();
        $cart    = get_cart();
        $idList  = array_map('intval', $productIds);

        foreach ($cart as $id => $item) {
            if (in_array((int)$id, $idList, true) && ($item['stok'] > 0)) {
                $cart[$id]['selected'] = true;
            } else {
                $cart[$id]['selected'] = false;
            }
        }
        $session->set('cart', $cart);
    }
}

if (!function_exists('remove_from_cart')) {
    function remove_from_cart(int $productId): void
    {
        $session = session();
        $cart    = get_cart();
        unset($cart[$productId]);
        $session->set('cart', $cart);
        cart_delete_from_database($productId);
    }
}

if (!function_exists('remove_selected_from_cart')) {
    /**
     * Hapus HANYA produk yang sedang dicentang (selected = true) dari keranjang.
     * Produk yang tidak dicentang tetap aman tersimpan.
     */
    function remove_selected_from_cart(): void
    {
        $session = session();
        $cart    = get_cart();
        foreach ($cart as $productId => $item) {
            if (!empty($item['selected'])) {
                unset($cart[$productId]);
                cart_delete_from_database((int) $productId);
            }
        }
        $session->set('cart', $cart);
    }
}

if (!function_exists('remove_selected_items')) {
    /**
     * Hapus daftar ID produk tertentu dari keranjang.
     */
    function remove_selected_items(array $productIds): void
    {
        $session = session();
        $cart    = get_cart();
        foreach ($productIds as $id) {
             $id = (int) $id;

            unset($cart[$id]);
            cart_delete_from_database($id);
        }
        $session->set('cart', $cart);
    }
}

if (!function_exists('clear_cart')) {
    function clear_cart(): void
    {
        session()->remove('cart');
        $userId = cart_user_id();

        if ($userId > 0) {
            model('UserCartModel')->clearUserCart($userId);
        }
    }
}

if (!function_exists('cart_total')) {
    /**
     * Total harga seluruh produk di keranjang (tanpa filter selected).
     */
    function cart_total(): int
    {
        $total = 0;
        foreach (get_cart() as $item) {
            $total += $item['price'] * $item['qty'];
        }
        return $total;
    }
}

if (!function_exists('cart_selected_total')) {
    /**
     * Total harga HANYA produk yang dicentang oleh user (Shopee style).
     */
    function cart_selected_total(): int
    {
        $total = 0;
        foreach (get_selected_cart() as $item) {
            $total += $item['price'] * $item['qty'];
        }
        return $total;
    }
}

if (!function_exists('cart_count')) {
    /**
     * Total kuantitas seluruh produk di keranjang (untuk badge keranjang).
     */
    function cart_count(): int
    {
        return array_sum(array_column(get_cart(), 'qty'));
    }
}

if (!function_exists('cart_selected_count')) {
    /**
     * Total kuantitas produk yang dicentang oleh user.
     */
    function cart_selected_count(): int
    {
        return array_sum(array_column(get_selected_cart(), 'qty'));
    }
}

if (!function_exists('cart_selected_item_count')) {
    /**
     * Jumlah macam produk (distinct items) yang dicentang oleh user.
     */
    function cart_selected_item_count(): int
    {
        return count(get_selected_cart());
    }
}

function cart_item_count(): int
{
    return count(get_cart());
}
