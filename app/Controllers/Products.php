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
    protected $data = [];

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->productImageModel = new ProductImageModel();  
        $this->data['auth'] = new \IonAuth\Libraries\IonAuth();
        

        $this->data['categories'] = $this->categoryModel->getNestedCategories();;
        $this->data['ordering'] = [
            site_url('products') => 'Default',
            site_url('products?order=price-asc') => 'Price - Low to High',
            site_url('products?order=price-desc') => 'Price - High to Low',
            site_url('products?order=created_at-desc') => 'Newest to Oldest',
            site_url('products?order=created_at-asc') => 'Oldest to Newest',
        ];
        $this->data['selectedOrder'] = site_url('products');
    }

    public function index()
    {
        // Query dibersihkan dari join brand dan select brandName/brandSlug
        $products = $this->productModel
            ->select("products.*, categories.name as categoryName, categories.slug as categorySlug, (SELECT MIN(price) FROM products AS variants WHERE (products.id = variants.id AND variants.type = 'simple') OR products.id = variants.parent_id LIMIT 1) price")
            ->join('product_categories', 'products.id = product_categories.product_id', 'left')
            ->join('categories', 'product_categories.category_id = categories.id', 'left')
            // ->where('status', $this->productModel::ACTIVE)
            ->where('published_at !=', null)
            ->where('products.parent_id IS NULL');
        
       
        
        if ($priceRange = $this->request->getGet('price')) {
            $prices = explode('-', $priceRange);
            $lowPrice = removeAllCharsExceptNumbers($prices[0]);
            $highPrice = removeAllCharsExceptNumbers($prices[1]);

            if ($lowPrice && $highPrice && ($lowPrice < $highPrice)) {
                $products = $products->where("products.price >= $lowPrice AND products.price <= $highPrice OR exists(SELECT * FROM products AS variants WHERE products.id = variants.parent_id AND price >= $lowPrice AND price <= $highPrice)");
            }
        }

        $orderField = 'created_at';
        $orderType = 'desc';
        if ($order = $this->request->getGet('order')) {
            list($orderField, $orderType) = explode('-', $order);
        }
        $products = $products->orderBy($orderField, $orderType);
        

        $this->data['products'] = $products->paginate($this->perPage, 'bootstrap');
        $this->data['pager'] = $this->productModel->pager;

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

        // Ambil gambar pertama produk
        $product->featured_image = $this->productImageModel
            ->where('product_id', $product->id)
            ->orderBy('id', 'ASC')
            ->first();

        // Tambahkan product ke data global
        $this->data['product'] = $product;

        // Gunakan view show.php
        return view(
            'themes/' . $this->data['currentTheme'] . '/products/show',
            $this->data
        );
    }
}