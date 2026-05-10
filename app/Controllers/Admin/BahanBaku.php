<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class BahanBaku extends BaseController {

    public function __construct() {
        helper(['url', 'form']);
    }

    public function index()
    {
        $keyword = $this->request->getGet('table_search');
        $bahanModel = model('BahanBakuModel');

        if (!empty($keyword)) {
            $bahanModel->groupStart()
                       ->like('nama_bahan', $keyword)
                       ->orLike('kode_bahan', $keyword)
                       ->groupEnd();
        }

        $data = [
            'title'               => 'Daftar Bahan Baku',
            'currentAdminMenu'    => 'catalogue', 
            'currentAdminSubMenu' => 'bahanbaku', 
            'bahan'               => $bahanModel->orderBy('id','DESC')->paginate(10, 'bootstrap'),
            'categories'          => model('CategoryModel')->findAll(),
            'pager'               => $bahanModel->pager,
            'keyword'             => $keyword
        ];

        return view('admin/bahanbaku/index', $data);
    }

    public function tambah() {
        $data = [
            'title'               => 'Tambah Bahan Baku',
            'currentAdminMenu'    => 'inventory',
            'currentAdminSubMenu' => 'bahanbaku',
            'categories'          => model('CategoryModel')->where('parent_id', 1)->findAll(),
        ];
        return view('admin/bahanbaku/tambah', $data);
    }

    public function getCategoriesAjax()
    {
        $search = $this->request->getVar('q'); 
        $data = model('CategoryModel')
            ->where('parent_id', 1)
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

    public function simpan() {
        $rules = [
            'nama_bahan'        => 'required|min_length[3]',
            'category_id'       => 'required',
            'satuan'            => 'required',
            'harga_beli_satuan' => [
                'rules'  => 'required|numeric|greater_than[0]',
                'errors' => ['greater_than' => 'Harga beli tidak boleh 0 atau minus!']
            ],
            'stok_sekarang'     => 'required|decimal|greater_than_equal_to[0]',
            'stok_minimal'      => 'required|decimal|greater_than_equal_to[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Simpan Gagal! Periksa kembali inputan Anda.');
        }

        try {    
            $catId    = $this->request->getPost('category_id');
            $category = model('CategoryModel')->find($catId);
            $prefix   = $category->prefix ?? 'BHK';

            $bahanModel = model('BahanBakuModel');
            $lastBahan  = $bahanModel->where('category_id', $catId)
                                     ->orderBy('id', 'DESC')
                                     ->first();

            $nextNumber = (!$lastBahan) ? 1 : ((int) substr($lastBahan->kode_bahan, -3)) + 1;
            $kode       = $prefix . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            $bahanModel->save([
                'kode_bahan'        => $kode,
                'category_id'       => $catId,
                'nama_bahan'        => $this->request->getPost('nama_bahan'),
                'satuan'            => strtolower($this->request->getPost('satuan')),
                'harga_beli_satuan' => $this->request->getPost('harga_beli_satuan'),
                'stok_sekarang'     => $this->request->getPost('stok_sekarang') ?? 0,
                'stok_minimal'      => $this->request->getPost('stok_minimal') 
            ]);

            return redirect()->to('admin/bahanbaku')->with('success', "Bahan Baku $kode Berhasil Disimpan!");

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($id) {
        $bahan = model('BahanBakuModel')->find($id);
        if (!$bahan) return redirect()->to('admin/bahanbaku')->with('error', 'Data tidak ditemukan');

        $data = [
            'title'               => 'Edit Bahan Baku',
            'currentAdminMenu'    => 'inventory',
            'currentAdminSubMenu' => 'bahanbaku',
            'bahan'               => $bahan,
            'categories'          => model('CategoryModel')->where('parent_id', 1)->findAll(),
        ];
        return view('admin/bahanbaku/edit', $data);
    }

    public function update($id)
    {
        $rules = [
            'nama_bahan'        => 'required|min_length[3]',
            'category_id'       => 'required',
            'satuan'            => 'required',
            'harga_beli_satuan' => 'required|numeric|greater_than[0]',
            'stok_sekarang'     => 'required|decimal|greater_than_equal_to[0]',
            'stok_minimal'      => 'required|decimal|greater_than_equal_to[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Update Gagal!');
        }

        try {
            model('BahanBakuModel')->update($id, [
                'nama_bahan'        => $this->request->getPost('nama_bahan'),
                'category_id'       => $this->request->getPost('category_id'),
                'satuan'            => strtolower($this->request->getPost('satuan')),
                'harga_beli_satuan' => round($this->request->getPost('harga_beli_satuan')),
                'stok_minimal'      => $this->request->getPost('stok_minimal'),
                'stok_sekarang'     => $this->request->getPost('stok_sekarang')
            ]);

            return redirect()->to('admin/bahanbaku')->with('success', 'Data Berhasil Diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function hapus($id) {
        model('BahanBakuModel')->delete($id);
        return redirect()->to('/admin/bahanbaku')->with('success', 'Data Berhasil Dihapus!');
    }
}