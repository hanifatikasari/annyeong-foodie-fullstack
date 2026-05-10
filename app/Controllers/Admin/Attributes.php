<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AttributeModel;

class Attributes extends BaseController
{
    protected $attributeModel;

    public function __construct()
    {
        helper(['url', 'form']);
        $this->attributeModel = new AttributeModel();
    }

    public function index()
    {
        $keyword = $this->request->getGet('table_search');
        $perPage = $this->request->getGet('perPage') ?? 5;

        if ($keyword) {
            $this->attributeModel->like('name', $keyword)->orLike('code', $keyword);
        }

        $data = [
            'title'             => 'Attributes',
            'attributes'        => $this->attributeModel->paginate($perPage, 'bootstrap'),
            'pager'             => $this->attributeModel->pager,
            'keyword'           => $keyword,
            'perPage'           => $perPage,
            // Konstanta dropdown dari Model
            'attributeTypes'    => $this->attributeModel::ATTRIBUTE_TYPES,
            'isRequiredOptions' => $this->attributeModel::IS_REQUIRED_OPTIONS,
            'isUniqueOptions'   => $this->attributeModel::IS_UNIQUE_OPTIONS,
            'validations'       => $this->attributeModel::VALIDATIONS,
            'isConfigurableOptions' => $this->attributeModel::IS_CONFIGURABLE_OPTIONS,
            'isFilterableOptions'   => $this->attributeModel::IS_FILTERABLE_OPTIONS,
            'categories' => model('CategoryModel')->findAll(),
        ];

        return view('admin/attributes/index', $data);
    }

    public function edit($id)
    {
        $attribute = $this->attributeModel->find($id);
        if (!$attribute) {
            return redirect()->to('admin/attributes')->with('error', 'Data tidak ditemukan');
        }

        $keyword = $this->request->getGet('table_search');
        $perPage = $this->request->getGet('perPage') ?? 5;

        $data = [
            'title'             => 'Edit Attribute',
            'attributes'        => $this->attributeModel->paginate($perPage, 'bootstrap'),
            'pager'             => $this->attributeModel->pager,
            'attribute'         => $attribute, // Data tunggal untuk form
            'keyword'           => $keyword,
            'perPage'           => $perPage,
            'attributeTypes'    => $this->attributeModel::ATTRIBUTE_TYPES,
            'isRequiredOptions' => $this->attributeModel::IS_REQUIRED_OPTIONS,
            'isUniqueOptions'   => $this->attributeModel::IS_UNIQUE_OPTIONS,
            'validations'       => $this->attributeModel::VALIDATIONS,
            'isConfigurableOptions' => $this->attributeModel::IS_CONFIGURABLE_OPTIONS,
            'isFilterableOptions'   => $this->attributeModel::IS_FILTERABLE_OPTIONS,
            'categories' => model('CategoryModel')->findAll(),
            'selectedCategories' => array_column(
                db_connect()->table('attribute_categories')
                    ->where('attribute_id', $id)
                    ->get()->getResultArray(),
                'category_id'
            ),
        ];
        

        return view('admin/attributes/index', $data);
    }

    public function store()
    {
        $data = $this->request->getPost();
        
        // Logic slug otomatis jika code kosong
        if (empty($data['code'])) {
            $data['code'] = url_title($data['name'], '-', true);
        }

        $categoryIds = $this->request->getPost('category_ids');

        if ($this->attributeModel->save($data)) {

             $attributeId = $this->attributeModel->getInsertID();
             if (!empty($categoryIds)) {
                foreach ($categoryIds as $catId) {
                    db_connect()->table('attribute_categories')->insert([
                        'attribute_id' => $attributeId,
                        'category_id'  => $catId
                    ]);
                }
            }
            return redirect()->to('admin/attributes')->with('success', 'Attribute saved.');
        }
        return redirect()->back()->withInput()->with('errors', $this->attributeModel->errors());
    }

    public function update($id = null)
    {
        $data = $this->request->getPost();
        $data['id'] = $id;

        $categoryIds = $this->request->getPost('category_ids');

        if ($this->attributeModel->save($data)) {
             // HAPUS RELASI LAMA
            db_connect()->table('attribute_categories')
                ->where('attribute_id', $id)
                ->delete();

            // INSERT ULANG
            if (!empty($categoryIds)) {
                foreach ($categoryIds as $catId) {
                    db_connect()->table('attribute_categories')->insert([
                        'attribute_id' => $id,
                        'category_id'  => $catId
                    ]);
                }
            }
            return redirect()->to('admin/attributes')->with('success', 'Attribute updated!');
        }
        return redirect()->back()->withInput()->with('errors', $this->attributeModel->errors());
    }

    public function destroy($id)
    {
        // Gunakan getGet jika link delete berupa tag <a> (bukan form DELETE)
        if ($this->attributeModel->delete($id)) {
            return redirect()->to('admin/attributes')->with('success', 'Attribute deleted.');
        }
        return redirect()->to('admin/attributes')->with('error', 'Gagal menghapus data.');
    }

    public function getCategoriesAjax()
    {
        $q = $this->request->getGet('q');

        $categories = model('CategoryModel')
            ->like('name', $q)
            ->findAll();

        $result = [];

        foreach ($categories as $cat) {
            $result[] = [
                'id' => $cat->id,
                'text' => $cat->name
            ];
        }

        return $this->response->setJSON($result);
    }
}