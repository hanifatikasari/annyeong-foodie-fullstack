<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Categories extends BaseController {

    public function __construct() {
        // Hanya panggil helper jika diperlukan
        helper(['url', 'form']);
    }

    public function index() {
        $keyword = $this->request->getGet('table_search');
        $perPage = $this->request->getGet('perPage') ?? 5;
        
        $categoryModel = model('CategoryModel');

        if ($keyword) {
            $categoryModel->like('name', $keyword);
        }

        $data = [
            'title'            => 'Categories',
            'currentAdminMenu' => 'catalogue',
            'categories'       => $categoryModel->paginate($perPage, 'bootstrap'),
            'pager'            => $categoryModel->pager,
            'parentOptions'    => model('CategoryModel')->where('parent_id', null)->findAll(),
            'keyword'          => $keyword,
            'perPage'          => $perPage,
        ];
        return view('admin/categories/index', $data);
    }

    public function edit($id) {
        $category = model('CategoryModel')->find($id);
        $perPage = $this->request->getGet('perPage') ?? 5;

        if (!$category) {
            return redirect()->to('admin/categories')->with('error', 'Data tidak ditemukan');
        }

        $data = [
            'title'            => 'Edit Category',
            'currentAdminMenu' => 'catalogue',
            'categories'       => model('CategoryModel')->paginate($perPage, 'bootstrap'),
            'pager'            => model('CategoryModel')->pager,
            'category'         => $category, 
            'parentOptions'    => model('CategoryModel')->where('parent_id', null)->findAll(),
            'perPage'          => $perPage,
            'keyword'          => $this->request->getGet('table_search'),
        ];
        return view('admin/categories/index', $data);
    }

    public function simpan() {
        $id       = $this->request->getPost('id');
        $name     = $this->request->getPost('name');
        $parentId = $this->request->getPost('parent_id');   
        $prefix   = strtoupper($this->request->getPost('prefix'));
        
        // Rules Validasi (Gunakan nama tabel yang benar: m_categories atau categories)
        $rules = [
            'name' => [
                'rules'  => 'required|min_length[3]',
                'errors' => [
                    'required'   => 'Nama kategori harus diisi.',
                    'min_length' => 'Nama kategori minimal 3 karakter.'
                ]
            ],
            'prefix' => [
                'rules'  => "required|alpha|exact_length[3]|is_unique[categories.prefix,id,{$id}]",
                'errors' => [
                    'required'     => 'Prefix wajib diisi.',
                    'alpha'        => 'Prefix hanya boleh berisi huruf.',
                    'exact_length' => 'Prefix harus tepat 3 karakter.',
                    'is_unique'    => 'Prefix sudah digunakan.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Gagal menyimpan! Silakan cek kembali inputan Anda.');
        }

        $data = [
            'name'      => $name,
            'slug'      => url_title($name, '-', true),
            'prefix'    => $prefix,
            'parent_id' => (empty($parentId) || $parentId == '0') ? null : $parentId
        ];

        try {
            if ($id) {
                model('CategoryModel')->update($id, $data);
                $msg = "Kategori [$prefix] berhasil diupdate!";
            } else {
                model('CategoryModel')->save($data);
                $msg = "Kategori baru [$prefix] berhasil dibuat!";
            }

            return redirect()->to('admin/categories')->with('success', $msg);

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Database Error: ' . $e->getMessage());
        }
    }
    
    public function hapus($id) {
        $categoryModel = model('CategoryModel');
        
        // Cek Child
        $childExists = $categoryModel->where('parent_id', $id)->first();

        if ($childExists) {
            return redirect()->to('admin/categories')->with('error', 'Gagal hapus! Kategori ini adalah Parent dari kategori lain.');
        }

        $categoryModel->delete($id);
        return redirect()->to('admin/categories')->with('success', 'Kategori berhasil dihapus.');
    }
}