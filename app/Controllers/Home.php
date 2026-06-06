<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        helper('cart_helper');

        // FIX ISSUE #1: Hanya tampilkan child categories dari "Produk Jadi"
        $categoryModel = model('CategoryModel');
        $this->data['categories'] = $categoryModel->getStorefrontCategories();

        // *** TRENDING PRODUCTS (best seller) ***
        $bestSelling = model('PenjualanDetailModel')->getBestSelling(8);

        $trendingProducts = [];

        if (!empty($bestSelling)) {
            $bestIds = array_map(fn($b) => is_object($b) ? $b->product_id : $b['product_id'], $bestSelling);

            $trendingProducts = model('ProductModel')
                ->select('products.*, COALESCE(product_inventories.qty, 0) as stok')
                ->join('product_inventories', 'product_inventories.product_id = products.id', 'left')
                ->whereIn('products.id', $bestIds)
                ->where('products.parent_id IS NULL')
                ->where('products.published_at IS NOT NULL')
                ->findAll();
        }

        // Fallback: jika kurang dari 4, tambahkan produk terbaru
        if (count($trendingProducts) < 4) {
            $existingIds = array_map(fn($p) => is_object($p) ? $p->id : $p['id'], $trendingProducts);

            $fallbackQuery = model('ProductModel')
                ->select('products.*, COALESCE(product_inventories.qty, 0) as stok')
                ->join('product_inventories', 'product_inventories.product_id = products.id', 'left')
                ->where('products.parent_id IS NULL')
                ->where('products.published_at IS NOT NULL');

            if (!empty($existingIds)) {
                $fallbackQuery->whereNotIn('products.id', $existingIds);
            }

            $extra = $fallbackQuery
                ->orderBy('products.id', 'DESC')
                ->findAll(4 - count($trendingProducts));

            $trendingProducts = array_merge($trendingProducts, $extra);
        }

        // Attach featured image
        foreach ($trendingProducts as &$p) {
            $img = model('ProductImageModel')
                ->where('product_id', is_object($p) ? $p->id : $p['id'])
                ->orderBy('id', 'ASC')
                ->first();
            if (is_object($p)) {
                $p->featured_image = $img;
            } else {
                $p['featured_image'] = $img;
            }
        }

        $this->data['trendingProducts'] = $trendingProducts;
        $this->data['title']            = 'Annyeong Foodie - Korean Food';

        return view('themes/' . $this->data['currentTheme'] . '/pages/home', $this->data);
    }
}