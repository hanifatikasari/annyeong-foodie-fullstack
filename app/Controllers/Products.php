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
    // Shared data untuk semua halaman frontend produk
    // ---------------------------------------------------------------
    protected function getSharedData(): array
    {
        $categories = $this->categoryModel->getStorefrontCategories();

        $ordering = [
            site_url('products')                       => 'Default',
            site_url('products?order=price-asc')       => 'Harga Terendah',
            site_url('products?order=price-desc')      => 'Harga Tertinggi',
            site_url('products?order=created_at-desc') => 'Terbaru',
        ];

        return [
            'categories' => $categories,
            'ordering'   => $ordering,
        ];
    }

    // ---------------------------------------------------------------
    // PRODUCT LISTING
    // Hanya tampilkan parent product (parent_id IS NULL)
    // ---------------------------------------------------------------
    public function index(): string
    {
        $sharedData = $this->getSharedData();

        $builder = $this->productModel
            ->select('products.*, COALESCE(product_inventories.qty, 0) as stok')
            ->join('product_inventories', 'product_inventories.product_id = products.id', 'left')
            ->where('products.parent_id IS NULL')
            ->where('products.published_at IS NOT NULL');

        // Filter: Kategori
        $categorySlug     = $this->request->getGet('category');
        $selectedCategory = null;

        if ($categorySlug) {
            $cat = $this->categoryModel->getBySlug($categorySlug);
            if ($cat) {
                $selectedCategory = $cat;
                $builder->join('product_categories', 'product_categories.product_id = products.id')
                        ->where('product_categories.category_id', $cat->id);
            }
        } else {
            $storefrontCategories = $sharedData['categories'];
            if (!empty($storefrontCategories)) {
                $catIds = array_map(fn($c) => $c->id, $storefrontCategories);
                $builder->join('product_categories AS pc_filter', 'pc_filter.product_id = products.id')
                        ->whereIn('pc_filter.category_id', $catIds);
            }
        }

        // =======================
        // Filter Harga
        // =======================
        $minPrice=(int)$this->request->getGet('min_price');
        $maxPrice=(int)$this->request->getGet('max_price');

        if($minPrice || $maxPrice){
            if($minPrice>$maxPrice){
                [$minPrice,$maxPrice]=[$maxPrice,$minPrice];
            }

            if($minPrice>$maxPrice){
                [$minPrice,$maxPrice]=[$maxPrice,$minPrice];
            }
            $builder->groupStart()
                // produk simple
                ->groupStart()
                    ->where('products.type','simple')
                    ->where('products.price >=',$minPrice)
                    ->where('products.price <=',$maxPrice)
                ->groupEnd()
                // produk configurable
                ->orWhere("
                    EXISTS(
                        SELECT 1
                        FROM products variants
                        WHERE variants.parent_id=products.id
                        AND variants.deleted_at IS NULL
                        AND variants.price BETWEEN {$minPrice} AND {$maxPrice}
                    )
                ",null,false)
            ->groupEnd();
        }

        // Sorting
        $orderParam    = $this->request->getGet('order');
        $allowedOrders = [
            'price-asc'       => ['products.price', 'ASC'],
            'price-desc'      => ['products.price', 'DESC'],
            'created_at-desc' => ['products.created_at', 'DESC'],
            'created_at-asc'  => ['products.created_at', 'ASC'],
            'name-asc'        => ['products.name', 'ASC'],
        ];

        [$orderField, $orderDir] = $orderParam && isset($allowedOrders[$orderParam])
            ? $allowedOrders[$orderParam]
            : ['products.created_at', 'DESC'];

        $builder->orderBy($orderField, $orderDir);
        $products = $builder->paginate($this->perPage, 'products');

        // Lampirkan gambar utama ke setiap produk
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
    // NEW: Load variants lengkap (stok + gambar) untuk configurable
    // ---------------------------------------------------------------
    public function show($sku, $slug = null): string
    {
        // Ambil produk parent
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

        // Gambar parent
        $product->images       = $this->productImageModel
            ->where('product_id', $product->id)
            ->orderBy('id', 'ASC')
            ->findAll();
        $product->featured_image = !empty($product->images) ? $product->images[0] : null;

        // ---------------------------------------------------------------
        // VARIANT LOADING — hanya untuk configurable product
        // ---------------------------------------------------------------
        $variants     = [];
        $variantsJson = '[]';

        if ($product->type === 'configurable') {
            $rawVariants = $this->productModel
                ->select('products.*, COALESCE(product_inventories.qty, 0) as stok')
                ->join('product_inventories', 'product_inventories.product_id = products.id', 'left')
                ->where('products.parent_id', $product->id)
                ->orderBy('products.price', 'ASC')
                ->findAll();
            // Catatan: useSoftDeletes = true pada ProductModel secara otomatis
            // menambahkan WHERE deleted_at IS NULL — variant yang di-soft-delete
            // tidak akan muncul tanpa perlu filter manual.

            $variantsForJs = [];

            foreach ($rawVariants as $v) {
                // Gambar variant; jika tidak ada → fallback ke gambar parent
                $variantImages = $this->productImageModel
                    ->where('product_id', $v->id)
                    ->orderBy('id', 'ASC')
                    ->findAll();

                $imagesToUse       = !empty($variantImages) ? $variantImages : $product->images;
                $v->variant_images = $imagesToUse;
                $v->featured_image = !empty($imagesToUse) ? $imagesToUse[0] : null;

                $variants[] = $v;

                // Siapkan data untuk JS (di-embed sebagai JSON ke view)
                $imagesForJs = array_map(fn($img) => [
                    'large'  => base_url($img->large),
                    'medium' => base_url($img->medium),
                    'small'  => base_url($img->small),
                ], $imagesToUse);

                $variantsForJs[] = [
                    'id'              => (int) $v->id,
                    'name'            => $v->name,
                    'sku'             => $v->sku,
                    'price'           => (int) $v->price,
                    'price_formatted' => 'Rp ' . number_format((int) $v->price),
                    'stok'            => (int) $v->stok,
                    'images'          => $imagesForJs,
                ];
            }

            // Encode aman: escape karakter HTML di dalam JSON
            $variantsJson = json_encode(
                $variantsForJs,
                JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
            );
        }

        // EAV Attributes
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

        // Produk terkait (kategori sama, limit 4)
        $catIds = $db->table('product_categories')
            ->select('category_id')
            ->where('product_id', $product->id)
            ->get()
            ->getResultArray();

        $related = [];
        if (!empty($catIds)) {
            $ids     = array_column($catIds, 'category_id');
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
            'product'      => $product,
            'variants'     => $variants,      // array enriched variant objects
            'variantsJson' => $variantsJson,  // JSON string untuk JavaScript
            'reviews'      => $reviews,
            'avg_rating'   => $avgRating,
            'related'      => $related,
            'title'        => $product->name,
        ]);

        return view('themes/' . $this->data['currentTheme'] . '/products/show', $this->data);
    }
}