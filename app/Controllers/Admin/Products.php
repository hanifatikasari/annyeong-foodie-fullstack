<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Products extends BaseController
{
    protected $currentUser;
    protected $auth;
    protected $perPage = 10;
    protected $productImagesModel;

    public function __construct()
    {
        helper('general_helper');
        $this->auth = new \IonAuth\Libraries\IonAuth();
        $this->currentUser = $this->auth->user()->row();
        $this->productImageModel = model('ProductImageModel'); //karena dipakai berkali2 pendekatan property class lebih baik daripada instansiasi berulang di method yang butuh
    }

    private function getCommonData()
    {
        return [
            'currentAdminMenu'    => 'catalogue',
            'currentAdminSubMenu' => 'product',
            'statuses'            => model('ProductModel')::getStatuses(),
            'types'               => model('ProductModel')::getProductTypesDropdown(),
        ];
    }

    public function getCategoriesAjax()
    {
        $search = $this->request->getVar('q'); 
        
        $data = model('CategoryModel')
            ->where('parent_id', 2) 
            ->like('name', $search)
            ->limit(10)
            ->findAll();

        $result = [];
        foreach ($data as $row) {
            $result[] = [
                'id'   => $row->id,
                'text' => $row->name
            ];
        }

        return $this->response->setJSON($result);
    }


    public function index()
    {
         $keyword = $this->request->getGet('table_search');
        $this->perPage = (int) ($this->request->getGet('perPage') ?? 10);

        if (!in_array($this->perPage, [10, 15, 20, 25, 50])) {
            $this->perPage = 10;
        }

        $products = $this->fetchProducts([
            'keyword' => $keyword
        ]);
        // Gabungkan data umum dengan data spesifik index
        $data = array_merge($this->getCommonData(), [
            'products' => $products,
            'pager'    => model('ProductModel')->pager,
            'perPage'  => $this->perPage,
            'keyword'  => $keyword,
        ]);

        return view('admin/products/index', $data);
    }

    private function fetchProducts($options = [])
    {
        $productModel = model('ProductModel');
        $products = $productModel
            ->select('products.*, product_inventories.qty')
            ->join('product_inventories', 'product_inventories.product_id = products.id', 'left')
            ->groupBy('products.id');

        if (isset($options['onlyDeleted']) && $options['onlyDeleted']) {
            $products = $products->onlyDeleted();
        }

         // SEARCH berdasarkan nama atau SKU
        if (!empty($options['keyword'])) {
            $keyword = $options['keyword'];

            $products->groupStart()
                ->like('products.name', $keyword)
                ->orLike('products.sku', $keyword)
                ->groupEnd();
        }

        // Tampilkan data yang sudah dihapus jika diperlukan
        if (!empty($options['onlyDeleted'])) {
            $products->onlyDeleted();
        }


        return $products->paginate($this->perPage, 'bootstrap');
    }

    public function trashed()
    {
        $this->getProducts(['onlyDeleted' => true]);
        $this->data['currentAdminSubMenu'] = 'deleted-product';
        return view('admin/products/index', $this->data);
    }

    private function getProducts($options = [])
    {
        $products = model('ProductModel')
            ->select('products.*, product_inventories.qty')
            ->join('product_inventories', 'products.id = product_inventories.product_id', 'left');

        if (isset($options['onlyDeleted']) && $options['onlyDeleted']) {
            $products = $products->onlyDeleted();
        }

        $this->data['products'] = $products->paginate($this->perPage, 'bootstrap');

        $this->data['pager'] = model('ProductModel')->pager;
    }

    public function create()
    {
        $data = array_merge($this->getCommonData(), [
            'configurableAttributes' => [],
        ]);

        return view('admin/products/form', $data);
    }

    public function getAttributesByCategory()
    {
        $categoryIds = $this->request->getPost('category_ids');

        $attributes = $this->getConfigurableAttributes($categoryIds);
        // TAMBAHAN: ambil options
        foreach ($attributes as &$attr) {
            $attr->options = model('AttributeOptionModel')
                ->where('attribute_id', $attr->id)
                ->findAll();
        }
        return $this->response->setJSON($attributes);
    }

    private function getConfigurableAttributes($categoryIds = [])
    {
        if (empty($categoryIds)) {
            return [];
        }

        $attributes = model('AttributeModel')
            ->select('attributes.*')
            ->join('attribute_categories', 'attribute_categories.attribute_id = attributes.id')
            ->whereIn('attribute_categories.category_id', $categoryIds)
            ->where('attributes.is_configurable', 1)
            ->findAll();

        // ambil options
        foreach ($attributes as &$attr) {
            $attr->options = model('AttributeOptionModel')
                ->where('attribute_id', $attr->id)
                ->findAll();
        }

        return $attributes;
    }

    public function edit($id)
    {
        $product = model('ProductModel')->find($id);

        if (!$product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $categoryIds = [];

        if (!empty($product->categories)) {
            $categoryIds = array_map(function($cat) {
                return $cat->id;
            }, $product->categories);
        }

        $data = array_merge($this->getCommonData(), [
            'product' => $product,
            'configurableAttributes' => $this->getConfigurableAttributes($categoryIds),
            'categoryIds' => array_column($product->categories, 'id'),
            'productMenu' => 'product_details',
        ]);

        return view('admin/products/form', $data);
    }

   
    public function store()
    {
        $productModel = model('ProductModel');
        $categoryIds  = $this->request->getVar('categories');
        $statusInput  = $this->request->getVar('status');
        $typeInput    = $this->request->getVar('type');
        
        $autoSku = $this->generateAutoSku(!empty($categoryIds) ? $categoryIds[0] : null);

        $params = [
            'name'              => $this->request->getVar('name'),
            'sku'               => $autoSku,
            'type'              => $typeInput,
            'categories'        => $categoryIds,
            'user_id'           => session()->get('user_id'),
            'price'             => $this->request->getVar('price') ?? 0,
            // 'stock'             => $this->request->getVar('stock'),
            'weight'            => $this->request->getVar('weight'),
            'length'            => $this->request->getVar('length'),
            'width'             => $this->request->getVar('width'),
            'height'            => $this->request->getVar('height'),
            'short_description' => $this->request->getVar('short_description'),
            'description'       => $this->request->getVar('description'),
            'published_at'      => ($statusInput == 'active') ? date('Y-m-d H:i:s') : null,
        ];

        // Logika tambahan untuk Configurable
        if ($typeInput == $productModel::CONFIGURABLE) {
            $params['published_at'] = null; // Configurable biasanya draft dulu sampai varian diset
            $params['price']        = 0;
            $params['configurable'] = $this->request->getVar('configurable');
        }

        $db = \Config\Database::connect();
        $db->transStart();
        
        if ($productModel->save($params)) 
            {
                $productId = $db->insertID();
                    if ($typeInput == $productModel::SIMPLE) {
                        
                        // Simpan Stok ke tabel product_inventories
                        $stock = $this->request->getVar('stock') ?? 0;
                        $this->updateOrCreateInventory([
                            'product_id' => $productId, 
                            'qty'        => $stock
                        ]);

                        // Simpan Relasi Kategori
                        if (!empty($categoryIds)) {
                            $this->saveProductCategories($productId, $categoryIds);
                        }
                    } else {
                        // Jika Configurable, generate variannya
                        // Ambil object product untuk kebutuhan generateProductVariants
                        $productObj = $productModel->find($productId);
                        $this->generateProductVariants($productObj, $params);
                    }
            } else {
                // Jika save() gagal karena validasi model
                $db->transRollback();
                return redirect()->back()->withInput()->with('errors', $productModel->errors());
            }
    
        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan ke database.');
        }

        return redirect()->to('/admin/products')->with('success', 'Product saved successfully.');
    }

    public function update($id)
    {
        $product = model('ProductModel')->find($id);

        if (!$product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $statusInput = $this->request->getVar('status'); // Ambil dari dropdown view

        $params = [
            'id' => $id,
			'name' => $this->request->getVar('name'),
			'sku' => $this->request->getVar('sku'),
			'type' => $this->request->getVar('type'),
			'categories' => $this->request->getVar('categories'),
			'user_id' => $this->currentUser->id,
			'price' => $this->request->getVar('price'),
			'stock' => $this->request->getVar('stock'),
			'weight' => $this->request->getVar('weight'),
			'length' => $this->request->getVar('length'),
			'width' => $this->request->getVar('width'),
			'height' => $this->request->getVar('height'),
			'short_description' => $this->request->getVar('short_description'),
			'description' => $this->request->getVar('description'),
			'published_at' => ($statusInput == 'active') ? date('Y-m-d H:i:s') : null,
        ];

        if ($product->type == model('ProductModel')::CONFIGURABLE)
        {
            $params['variants'] = $this->request->getVar('variants');
        }

        model('ProductModel')->setValidationRule('sku', "required|is_unique[products.sku,id,$id]");

        $this->db->transStart();
        if (!model('ProductModel')->save($params)) {
            $this->db->transRollback(); // Paksa rollback jika validasi gagal
            
            $data = array_merge($this->getCommonData(), [
                'product' => $product,
                'productMenu' => 'product_details',
                'configurableAttributes' => $this->getConfigurableAttributes(),
                'categoryIds' => $this->request->getVar('categories') ?? [],
                'errors' => model('ProductModel')->errors(),
            ]);
            
            return view('admin/products/form', $data);
        }

        if ($product && $product->type == model('ProductModel')::SIMPLE) {
            // PERBAIKAN: Pisahkan delete dan insert
            $this->db->table('product_inventories')->where('product_id', $id)->delete();
            $this->db->table('product_inventories')->insert([
                'product_id' => $id,
                'qty' => $params['stock'],
            ]);

            $this->db->table('product_categories')->where('product_id', $id)->delete();
            if (!empty($params['categories'])) {
                foreach ($params['categories'] as $categoryId) {
                    $this->db->table('product_categories')->insert([
                        'product_id' => $id,
                        'category_id' => $categoryId,
                    ]);
                }
            }
        }

        if ($product && $product->type == model('ProductModel')::CONFIGURABLE) {
            $this->updateProductVariants($params);
        }

        $this->db->transComplete();

        if (model('ProductModel')->errors()) {
            $data = array_merge($this->getCommonData(), [
                'product' => $product,
                'productMenu' => 'product_details',
                'categoryIds' => $params['categories'],
                'errors' => model('ProductModel')->errors(),
            ]);
            return view('admin/products/form', $data);
        } else {
            $this->session->setFlashdata('success', 'Product has been updated.');
            return redirect()->to('/admin/products');
        }
    }

    public function destroy($id)
    {
        $product = model('ProductModel')->withDeleted()->find($id);

        if (!$product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (empty($product->deleted_at)) {
            model('ProductModel')->delete($id);

            $this->session->setFlashdata('success', 'Product has been deleted.');
            return redirect()->to('/admin/products');
        } else {
            $this->db->table('product_categories')->where('product_id', $id)->delete();
            model('ProductAttributeValueModel')->where('product_id', $id)->delete();
            model('ProductInventoryModel')->where('product_id', $id)->delete();

            model('ProductModel')->delete($id, true);

            $this->session->setFlashdata('success', 'Product has been deleted permanently.');
            return redirect()->to('/admin/products/trashed');
        }
    }

    public function restore($id)
    {
        $product = model('ProductModel')->withDeleted()->find($id);

        if (!$product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if ($product->deleted_at) {
            $params = [
                'deleted_at' => null,
            ];

            model('ProductModel')->update($id, $params);

            $this->session->setFlashdata('success', 'Product has been restored.');
            return redirect()->to('/admin/products');
        }
    }

    public function images($id)
    {
        $product = model('ProductModel')->find($id);

        if (!$product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        
        if ($product->parent_id) {
            return redirect()->to('/admin/products/'. $product->parent_id .'/images');
        }

       $productImages = model('ProductImageModel')
            ->where('product_id', $product->id)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $data = array_merge($this->getCommonData(), [
            'product'       => $product,
            'productImages' => $productImages,
            'productMenu'   => 'product_images',
        ]);

        return view('admin/products/images', $data);
    }

    public function uploadImage($productId)
    {
        $product = model('ProductModel')->find($productId);

        if (!$product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $this->data['product'] = $product;
        $this->data['productMenu'] = 'product_images';

        return view('admin/products/image_upload', $this->data);
    }

    public function doUploadImage($productId)
    {
        $product = model('ProductModel')->find($productId);

        if (!$product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $image = $this->request->getFile('image');

        if (!$image->isValid()) {
            return redirect()->back()->with('error', 'File upload failed.');
        }

        $fileName = $image->getRandomName();

        // Simpan langsung ke public/uploads/products
        $image->move(FCPATH . 'uploads/products', $fileName);

        // Path relatif yang akan disimpan di database
        $path = 'uploads/products/' . $fileName;

        // Generate thumbnail small, medium, dll
        $images = $this->generateImages($path, $fileName);

        $params = array_merge($images, [
            'product_id' => $productId,
            'original'   => $path,
        ]);

        model('ProductImageModel')->save($params);

        return redirect()
            ->to('/admin/products/' . $productId . '/images')
            ->with('success', 'Image has been saved.');
    }

    public function destroyImage($id)
    {
        $image = model('ProductImageModel')->find($id);

        if (!$image) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Semua path file yang tersimpan di database
        $paths = [
            $image->original ?? null,
            $image->small ?? null,
            $image->medium ?? null,
            $image->large ?? null,
        ];
        foreach ($paths as $path) {
             if (empty($path)) {
                continue;
            }
            $fullPath = FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $path);

            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
        //hapus record data di database
        model('ProductImageModel')->delete($id);

        return redirect()
            ->to('/admin/products/' . $image->product_id . '/images')
            ->with('success', 'Image has been deleted.');
    }

    private function generateImages($originalPath, $fileName)
    {
        $imageLib = \Config\Services::image();
        $publicDir = FCPATH; //agar image library bisa akses path ke public

        // Pecah nama file dan extension dengan aman
        $fileInfo = pathinfo($fileName);
        $name = $fileInfo['filename'];
        $extension = $fileInfo['extension'];
        
        $images = [];
        foreach (model('ProductImageModel')::IMAGE_SIZES as $size => $sizeDetails) {
            // Contoh hasil: uploads/products/abc_small.jpg
            $imagePath = 'uploads/products/' . $name . '_' . $size . '.' . $extension;

            // Baca file asli dari public/
            $imageLib->withFile($publicDir . $originalPath)
                ->fit(
                    $sizeDetails['width'],
                    $sizeDetails['height'],
                    'center'
                )
                ->save($publicDir . $imagePath);

            // Simpan path relatif ke database
            $images[$size] = $imagePath;
        }

        return $images;
    }

    private function updateProductVariants($params)
    {
        if ($params['variants']) {
            foreach ($params['variants'] as $variantParams) {
                $variantParams['published_at'] = $params['published_at'];
                model('ProductModel')->save($variantParams);

                $inventoryParams = [
                    'product_id' => $variantParams['id'],
                    'qty' => $variantParams['stock'],
                ];

                model('ProductInventoryModel')->save($inventoryParams);
            }
        }
    }

    private function updateOrCreateInventory($params)
    {
        $existInventory = model('ProductInventoryModel')
            ->where('product_id', $params['product_id'])
            ->first();

        if ($existInventory) {
            $params['id'] = $existInventory->id;
        }

        model('ProductInventoryModel')->save($params);
    }

    private function saveProductCategories($productId, $categoryIds)
    {
        $productCategoryTable = $this->db->table('product_categories');
        
        $productCategoryTable->where('product_id', $productId)->delete();

        foreach ($categoryIds as $categoryId) {
            $productCategoryTable->insert([
                'product_id'  => $productId,
                'category_id' => $categoryId,
            ]);
        }
    }

    private function generateProductVariants($product, $params)
    {
        $variantAttributes = !(empty($params['configurable'])) ? $params['configurable'] : [];
        $configurableAttributes = array_column(model('AttributeModel')->where('is_configurable', true)->findAll(), 'code');

        $variantAttributes = array_filter($variantAttributes, function ($value, $key) use ($configurableAttributes) {
            return in_array($key, $configurableAttributes);
        }, ARRAY_FILTER_USE_BOTH);

        $variants = $this->generateVariantsWithAttributeCombinations($variantAttributes);

        if ($variants) {
            foreach ($variants as $variant) {
                $variantParams = [
                    'parent_id' => $product->id,
                    'user_id' => $product->user_id,
                    'sku' => $this->generateAutoSku($params['categories'][0]),
                    'type' => model('ProductModel')::SIMPLE,
                    'name' => $product->name . $this->convertVariantAttributesAsName($variant),
                    'price' => 0,
                    'published_at' => $params['published_at'] ?? null,
                ];

                model('ProductModel')->save($variantParams);
                $newVariantID = model('ProductModel')->getInsertID();

                $productCategoryTable = $this->db->table('product_categories');
                if (!empty($params['categories'])) {
                    foreach ($params['categories'] as $key => $categoryId) {
                        $productCategoryTable->insert([
                            'product_id' => $newVariantID,
                            'category_id' => $categoryId,
                        ]);
                    }
                }

                $this->saveProductAttributeValues($newVariantID, $variant, $product->id);
            }
        }
    }

    private function saveProductAttributeValues($productID, $variant, $parentProductID)
    {
        foreach (array_values($variant) as $attributeOptionID) {
            $attributeOption = model('AttributeOptionModel')->find($attributeOptionID);
            
            if (!$attributeOption) continue;
            $attributeValueParams = [
                'parent_product_id' => $parentProductID,
                'product_id' => $productID,
                'attribute_id' => $attributeOption->attribute_id,
                'attribute_option_id' => $attributeOptionID,
                'text_value' => $attributeOption->name,
            ];

            model('ProductAttributeValueModel')->save($attributeValueParams);
        }
    }

    private function convertVariantAttributesAsName($variant)
    {
        $variantName = '';

        foreach (array_keys($variant) as $key => $code) {
            $attributeOptionID = $variant[$code];
            $attributeOption = model('AttributeOptionModel')->find($attributeOptionID);

            if ($attributeOption) {
                $variantName .= ' - ' . $attributeOption->name;
            }
        }

        return $variantName;
    }

    private function generateVariantsWithAttributeCombinations($arrays)
    {
        $result = [[]];
		foreach ($arrays as $property => $property_values) {
			$tmp = [];
			foreach ($result as $result_item) {
				foreach ($property_values as $property_value) {
					$tmp[] = array_merge($result_item, array($property => $property_value));
				}
			}
			$result = $tmp;
		}
		return $result;
    }

    
    private function generateAutoSku($categoryId)
    {
        if (!$categoryId) return 'GEN-0000';

        $category = model('CategoryModel')->find($categoryId);
        $prefix = ($category && !empty($category->prefix)) ? strtoupper($category->prefix) : 'PRO';

        $lastProduct = model('ProductModel')->withDeleted()
                            ->like("sku LIKE '$prefix-%'")
                            ->orderBy('id', 'DESC')
                            ->first();

        if (!$lastProduct) {
            $nextNumber = 1;
        } else {
            $lastNumber = (int) substr($lastProduct->sku, -4);
            $nextNumber = $lastNumber + 1;
        }

        return $prefix . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

   
    
}