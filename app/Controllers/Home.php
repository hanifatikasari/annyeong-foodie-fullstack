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

            // FIX: Cek apakah ID yang laris ini adalah varian (punya parent_id)
            $checkParents = model('ProductModel')
                ->select('id, parent_id')
                ->whereIn('id', $bestIds)
                ->findAll();

            $finalIds = [];
            foreach ($checkParents as $item) {
                $parentId = is_object($item) ? $item->parent_id : $item['parent_id'];
                $id = is_object($item) ? $item->id : $item['id'];
                
                // Jika punya parent_id, ambil ID induknya. Jika tidak, ambil ID-nya sendiri.
                $finalIds[] = $parentId ?? $id; 
            }
            $finalIds = array_unique($finalIds);

            // Gunakan $finalIds yang sudah divalidasi
            $trendingProducts = model('ProductModel')
                ->select('products.*, COALESCE(product_inventories.qty, 0) as stok')
                ->join('product_inventories', 'product_inventories.product_id = products.id', 'left')
                ->whereIn('products.id', $finalIds)
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

        $carouselPath = WRITEPATH . 'uploads/carousel_slides.json';
        $slides = [];
        if (is_file($carouselPath)) {
            $rawSlides = json_decode(file_get_contents($carouselPath), true);
            if (is_array($rawSlides)) {
                $slides = array_values(array_filter($rawSlides, function ($slide) {
                    return !empty($slide['is_active']);
                }));
            }
        }

        $this->data['trendingProducts'] = $trendingProducts;
        $this->data['slides']           = $slides;
        $this->data['title']            = 'Annyeong Foodie - Korean Food';

        return view('themes/' . $this->data['currentTheme'] . '/pages/home', $this->data);
    }
}