<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        helper('cart_helper');

        // ==========================================================
        // Ambil produk terlaris dari t_penjualan_detail
        // ==========================================================
        $bestSelling = model('PenjualanDetailModel')->getBestSelling(8);

        $bestSellingIds = [];
        foreach ($bestSelling as $item) {
            $bestSellingIds[] = $item->product_id;
        }

        // ==========================================================
        // Ambil data produk trending
        // ==========================================================
        $trendingProducts = [];

        if (!empty($bestSellingIds)) {
            $trendingProducts = model('ProductModel')
                ->select('products.*, product_inventories.qty as stok')
                ->join(
                    'product_inventories',
                    'products.id = product_inventories.product_id',
                    'left'
                )
                ->whereIn('products.id', $bestSellingIds)
                ->where('products.published_at IS NOT NULL')
                ->where('products.parent_id IS NULL')
                ->findAll();
        }

        // ==========================================================
        // Jika produk trending kurang dari 4, tambahkan produk terbaru
        // ==========================================================
        if (count($trendingProducts) < 4) {
            $existingIds = [];

            foreach ($trendingProducts as $product) {
                $existingIds[] = $product->id;
            }

            $fallback = model('ProductModel')
                ->select('products.*, product_inventories.qty as stok')
                ->join(
                    'product_inventories',
                    'products.id = product_inventories.product_id',
                    'left'
                )
                ->where('products.published_at IS NOT NULL')
                ->where('products.parent_id IS NULL');

            if (!empty($existingIds)) {
                $fallback->whereNotIn('products.id', $existingIds);
            }

            $extra = $fallback
                ->orderBy('products.id', 'DESC')
                ->findAll(4 - count($trendingProducts));

            $trendingProducts = array_merge($trendingProducts, $extra);
        }

        // ==========================================================
        // Attach featured image ke setiap produk
        // ==========================================================
        foreach ($trendingProducts as $product) {
            $image = model('ProductImageModel')
                ->where('product_id', $product->id)
                ->orderBy('id', 'ASC')
                ->first();

            $product->featured_image = $image;
        }

        // ==========================================================
        // Ambil kategori produk
        // ==========================================================
        $categories = model('CategoryModel')
            ->where('name', 'Produk Jadi')
            ->orderBy('name', 'ASC')
            ->findAll(10);

        // ==========================================================
        // Kirim data ke view
        // ==========================================================
        $this->data['trendingProducts'] = $trendingProducts;
        $this->data['categories'] = $categories;
        $this->data['title'] = 'Annyeong Foodie - Korean Food';

        return view(
            'themes/' . $this->data['currentTheme'] . '/pages/home',
            $this->data
        );
    }
}