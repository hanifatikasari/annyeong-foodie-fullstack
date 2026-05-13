<?php namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\ProductImageModel;

class Products extends BaseController
{
    protected $productModel;
    protected $categoryModel;
    protected $productImageModel;
    protected $perPage = 12;

    public function __construct()
    {
        helper('cart_helper');
        $this->productModel      = new ProductModel();
        $this->categoryModel     = new CategoryModel();
        $this->productImageModel = new ProductImageModel();

        $this->data['auth']       = new \IonAuth\Libraries\IonAuth();
        $this->data['categories'] = $this->categoryModel->getNestedCategories();
        $this->data['ordering']   = [
            site_url('products')                         => 'Default',
            site_url('products?order=price-asc')         => 'Harga Terendah',
            site_url('products?order=price-desc')        => 'Harga Tertinggi',
            site_url('products?order=created_at-desc')   => 'Terbaru',
        ];
        $this->data['selectedOrder'] = site_url('products');
    }

    public function index()
    {
        $products = $this->productModel
            ->select('products.*,
                (SELECT MIN(price) FROM products AS v
                 WHERE v.parent_id = products.id OR (v.id = products.id AND products.type = "simple")) as lowest_price')
            ->where('products.published_at IS NOT NULL')
            ->where('products.parent_id IS NULL');

        $category = $this->request->getGet('category');
        if ($category) {
            $cat = $this->categoryModel->where('slug', $category)->first();
            if ($cat) {
                $products->join('product_categories', 'products.id = product_categories.product_id')
                         ->where('product_categories.category_id', $cat->id);
            }
        }

        if ($priceRange = $this->request->getGet('price')) {
            [$low, $high] = array_map('intval', explode('-', $priceRange));
            if ($low && $high && $low < $high) {
                $products->where("products.price BETWEEN $low AND $high");
            }
        }

        $orderField = 'created_at';
        $orderType  = 'desc';
        if ($order = $this->request->getGet('order')) {
            [$orderField, $orderType] = explode('-', $order);
        }
        $products->orderBy($orderField, $orderType);

        $results = $products->paginate($this->perPage, 'bootstrap');
        foreach ($results as &$p) {
            $img = $this->productImageModel->where('product_id', $p->id)->orderBy('id','ASC')->first();
            $p->featured_image = $img;
        }

        $this->data['products']      = $results;
        $this->data['pager']         = $this->productModel->pager;
        $this->data['selectedOrder'] = site_url('products' . ($order ? '?order=' . $order : ''));

        return view('themes/' . $this->data['currentTheme'] . '/products/index', $this->data);
    }

    public function show($sku, $slug = null)
    {
        $product = $this->productModel
            ->where('sku', $sku)
            ->where('parent_id', null)
            ->first();

        if (!$product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Images
        $product->images = $this->productImageModel
            ->where('product_id', $product->id)
            ->orderBy('id', 'ASC')
            ->findAll();

        $product->featured_image = count($product->images) > 0 ? $product->images[0] : null;

        // EAV Attributes
        $attributes = \Config\Database::connect()
            ->table('product_attribute_values pav')
            ->select('a.name as attr_name, ao.name as attr_value')
            ->join('attributes a', 'a.id = pav.attribute_id')
            ->join('attribute_options ao', 'ao.id = pav.attribute_option_id', 'left')
            ->where('pav.product_id', $product->id)
            ->get()->getResult();

        $product->attributes = $attributes;

        // Inventory
        $inv = model('ProductInventoryModel')->where('product_id', $product->id)->first();
        $product->stok = $inv ? $inv->qty : 0;

        // Reviews
        $reviewModel          = model('ReviewModel');
        $this->data['reviews'] = $reviewModel->getByProduct($product->id);
        $this->data['avg_rating'] = $reviewModel->getAverageRating($product->id);

        // Related products (same category)
        $catIds = \Config\Database::connect()
            ->table('product_categories')
            ->where('product_id', $product->id)
            ->get()->getResult();

        $related = [];
        if (!empty($catIds)) {
             // Ambil semua category_id dari object
            $categoryIds = [];
            foreach ($catIds as $cat) {
                $categoryIds[] = $cat->category_id;
            }
            
            $related = $this->productModel
                ->select('products.*')
                ->join('product_categories pc', 'products.id = pc.product_id')
                ->whereIn('pc.category_id', $categoryIds)
                ->where('products.id !=', $product->id)
                ->where('products.published_at IS NOT NULL')
                ->where('products.parent_id IS NULL')
                ->limit(4)
                ->findAll();

            // Attach image
            foreach ($related as $item) {
                $img = $this->productImageModel
                    ->where('product_id', $item->id)
                    ->orderBy('id', 'ASC')
                    ->first();

                $item->featured_image = $img;
            }
        }

        $this->data['related']  = $related;
        $this->data['product']  = $product;
        $this->data['title']    = $product->name;

        return view('themes/' . $this->data['currentTheme'] . '/products/show', $this->data);
    }
}