<?php

if (!function_exists('get_cart')) {
    function get_cart(): array
    {
        $session = session();
        return $session->get('cart') ?? [];
    }
}

if (!function_exists('add_to_cart')) {
    function add_to_cart(int $productId, int $qty = 1): bool
    {
        $session = session();
        $cart    = get_cart();
        $model   = model('ProductModel');
        $product = $model->select('products.*, product_inventories.qty as stok')
                         ->join('product_inventories', 'products.id = product_inventories.product_id', 'left')
                         ->find($productId);

        if (!$product) return false;

        $price = $product->price ?? 0;
        $stok  = $product->stok ?? 0;

        if (isset($cart[$productId])) {
            $newQty = $cart[$productId]['qty'] + $qty;
            if ($newQty > $stok) return false;
            $cart[$productId]['qty'] = $newQty;
        } else {
            if ($qty > $stok) return false;
            $cart[$productId] = [
                'product_id'   => $productId,
                'name'         => $product->name,
                'sku'          => $product->sku,
                'price'        => $price,
                'qty'          => $qty,
                'stok'         => $stok,
                'image'        => null,
            ];

            $img = model('ProductImageModel')
                        ->where('product_id', $productId)
                        ->first();
            if ($img) $cart[$productId]['image'] = $img->small;
        }

        $session->set('cart', $cart);
        return true;
    }
}

if (!function_exists('update_cart')) {
    function update_cart(int $productId, int $qty): bool
    {
        $session = session();
        $cart    = get_cart();
        if (!isset($cart[$productId])) return false;
        if ($qty <= 0) {
            unset($cart[$productId]);
        } else {
            if ($qty > $cart[$productId]['stok']) return false;
            $cart[$productId]['qty'] = $qty;
        }
        $session->set('cart', $cart);
        return true;
    }
}

if (!function_exists('remove_from_cart')) {
    function remove_from_cart(int $productId): void
    {
        $session = session();
        $cart    = get_cart();
        unset($cart[$productId]);
        $session->set('cart', $cart);
    }
}

if (!function_exists('clear_cart')) {
    function clear_cart(): void
    {
        session()->remove('cart');
    }
}

if (!function_exists('cart_total')) {
    function cart_total(): int
    {
        $total = 0;
        foreach (get_cart() as $item) {
            $total += $item['price'] * $item['qty'];
        }
        return $total;
    }
}

if (!function_exists('cart_count')) {
    function cart_count(): int
    {
        return array_sum(array_column(get_cart(), 'qty'));
    }
}