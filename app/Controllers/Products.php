<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\ProductImageModel;

class Products extends BaseController
{
    protected ProductModel      $productModel;
    protected CategoryModel     $categoryModel;
    protected ProductImageModel $productImageModel;
    protected int               $perPage = 12;

    public function __construct()
    {
        helper('cart_helper');

        $this->productModel      = new ProductModel();
        $this->categoryModel     = new CategoryModel();
        $this->productImageModel = new ProductImageModel();
    }

    // ---------------------------------------------------------------
    // FIX ISSUE #1 & #2: Gunakan getStorefrontCategories() + fix query
    // ---------------------------------------------------------------
    protected function getSharedData(): array
    {
        // Hanya tampilkan child categories dari "Produk Jadi"
        $categories = $this->categoryModel->getStorefrontCategories();

        $ordering = [
            site_url('products')                          => 'Default',
            site_url('products?order=price-asc')          => 'Harga Terendah',
            site_url('products?order=price-desc')         => 'Harga Tertinggi',
            site_url('products?order=created_at-desc')    => 'Terbaru',
        ];

        return [
            'categories'   => $categories,
            'ordering'     => $ordering,
        ];
    }

    // ---------------------------------------------------------------
    // PRODUCT LISTING
    // FIX ISSUE #2:
    //   - Hapus subquery lowest_price yang bermasalah
    //   - Gunakan kolom price langsung
    //   - Hanya tampilkan produk yang published dan bukan varian (parent_id IS NULL)
    //   - Tambahkan filter berdasarkan storefront category (child dari Produk Jadi)
    // ---------------------------------------------------------------
    public function index(): string
    {
        $sharedData = $this->getSharedData();

        // *** BASE QUERY ***
        // Ambil produk: published, bukan varian produk (parent_id IS NULL = produk utama)
        // JOIN ke product_inventories untuk stok
        $builder = $this->productModel
            ->select('products.*, COALESCE(product_inventories.qty, 0) as stok')
            ->join('product_inventories', 'product_inventories.product_id = products.id', 'left')
            ->where('products.parent_id IS NULL')
            ->where('products.published_at IS NOT NULL');

        // *** FILTER: CATEGORY ***
        $categorySlug = $this->request->getGet('category');
        $selectedCategory = null;

        if ($categorySlug) {
            $cat = $this->categoryModel->getBySlug($categorySlug);
            if ($cat) {
                $selectedCategory = $cat;
                $builder->join('product_categories', 'product_categories.product_id = products.id')
                        ->where('product_categories.category_id', $cat->id);
            }
        } else {
            // *** FIX ISSUE #2 — "Semua Produk" ***
            // Jika tidak ada filter kategori, tampilkan semua produk yang termasuk
            // dalam child categories dari "Produk Jadi"
            $storefrontCategories = $sharedData['categories'];
            if (!empty($storefrontCategories)) {
                $catIds = array_map(fn($c) => $c->id, $storefrontCategories);
                $builder->join('product_categories AS pc_filter', 'pc_filter.product_id = products.id')
                        ->whereIn('pc_filter.category_id', $catIds);
            }
        }

        // *** FILTER: PRICE RANGE ***
        $priceRange = $this->request->getGet('price');
        if ($priceRange && strpos($priceRange, '-') !== false) {
            [$low, $high] = explode('-', $priceRange, 2);
            $low  = (int) $low;
            $high = (int) $high;
            if ($low >= 0 && $high > $low) {
                $builder->where("products.price BETWEEN {$low} AND {$high}");
            }
        }

        // *** SORTING ***
        $orderParam = $this->request->getGet('order');
        $allowedOrders = [
            'price-asc'          => ['products.price', 'ASC'],
            'price-desc'         => ['products.price', 'DESC'],
            'created_at-desc'    => ['products.created_at', 'DESC'],
            'created_at-asc'     => ['products.created_at', 'ASC'],
            'name-asc'           => ['products.name', 'ASC'],
        ];

        if ($orderParam && isset($allowedOrders[$orderParam])) {
            [$orderField, $orderDir] = $allowedOrders[$orderParam];
        } else {
            $orderField = 'products.created_at';
            $orderDir   = 'DESC';
        }

        $builder->orderBy($orderField, $orderDir);

        // *** PAGINATE ***
        $products = $builder->paginate($this->perPage, 'bootstrap');

        // *** ATTACH FEATURED IMAGE ***
        foreach ($products as &$p) {
            $img = $this->productImageModel
                ->where('product_id', $p->id)
                ->orderBy('id', 'ASC')
                ->first();
            $p->featured_image = $img;
        }

        $currentOrderUrl = $orderParam
            ? site_url('products?order=' . $orderParam)
            : site_url('products');

        $this->data = array_merge($this->data, $sharedData, [
            'products'         => $products,
            'pager'            => $this->productModel->pager,
            'selectedOrder'    => $currentOrderUrl,
            'selectedCategory' => $selectedCategory,
            'title'            => $selectedCategory ? esc($selectedCategory->name) : 'Semua Produk',
        ]);

        return view('themes/' . $this->data['currentTheme'] . '/products/index', $this->data);
    }

    // ---------------------------------------------------------------
    // PRODUCT DETAIL
    // FIX: akses sebagai object ($product->name, bukan $product['name'])
    // ---------------------------------------------------------------
    public function show($sku, $slug = null): string
    {
        // Ambil produk dengan stok
        $product = $this->productModel
            ->select('products.*, COALESCE(product_inventories.qty, 0) as stok')
            ->join('product_inventories', 'product_inventories.product_id = products.id', 'left')
            ->where('products.sku', $sku)
            ->where('products.parent_id IS NULL')
            ->first();

        if (!$product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                'Produk tidak ditemukan.'
            );
        }

        // Images
        $product->images = $this->productImageModel
            ->where('product_id', $product->id)
            ->orderBy('id', 'ASC')
            ->findAll();

        $product->featured_image = !empty($product->images) ? $product->images[0] : null;

        // EAV Attributes — raw query lebih aman
        $db = \Config\Database::connect();
        $product->attributes = $db->table('product_attribute_values pav')
            ->select('a.name as attr_name, ao.name as attr_value')
            ->join('attributes a', 'a.id = pav.attribute_id')
            ->join('attribute_options ao', 'ao.id = pav.attribute_option_id', 'left')
            ->where('pav.product_id', $product->id)
            ->get()
            ->getResultArray();

        // Reviews
        $reviewModel = model('ReviewModel');
        $reviews     = $reviewModel->getByProduct($product->id);
        $avgRating   = $reviewModel->getAverageRating($product->id);

        // Related products (same category, limit 4)
        $catIds = $db->table('product_categories')
            ->select('category_id')
            ->where('product_id', $product->id)
            ->get()
            ->getResultArray();

        $related = [];
        if (!empty($catIds)) {
            $ids = array_column($catIds, 'category_id');
            $related = $this->productModel
                ->select('products.*')
                ->join('product_categories pc', 'products.id = pc.product_id')
                ->whereIn('pc.category_id', $ids)
                ->where('products.id !=', $product->id)
                ->where('products.parent_id IS NULL')
                ->where('products.published_at IS NOT NULL')
                ->groupBy('products.id')
                ->limit(4)
                ->findAll();

            foreach ($related as &$r) {
                $img = $this->productImageModel
                    ->where('product_id', $r->id)
                    ->orderBy('id', 'ASC')
                    ->first();
                $r->featured_image = $img;
            }
        }

        $sharedData = $this->getSharedData();

        $this->data = array_merge($this->data, $sharedData, [
            'product'    => $product,
            'reviews'    => $reviews,
            'avg_rating' => $avgRating,
            'related'    => $related,
            'title'      => $product->name,
        ]);

        return view('themes/' . $this->data['currentTheme'] . '/products/show', $this->data);
    }
}