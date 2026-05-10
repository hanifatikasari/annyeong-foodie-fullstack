<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class StokMasuk extends BaseController
{
    public function __construct()
    {
        helper(['url', 'form']);
    }

    public function index()
    {
        $keyword = $this->request->getGet('table_search');
        $perPage = $this->request->getGet('perPage') ?? 10;
        
        $stokModel = model('StokMasukModel');

        // Query Builder dengan Join untuk mengambil data bahan baku
        $stokModel->select('t_stok_masuk.*, m_bahan_baku.nama_bahan, m_bahan_baku.satuan')
                  ->join('m_bahan_baku', 'm_bahan_baku.id = t_stok_masuk.bahan_baku_id');

        if (!empty($keyword)) {
            $stokModel->groupStart()
                      ->like('m_bahan_baku.nama_bahan', $keyword)
                      ->orLike('t_stok_masuk.nama_supplier', $keyword)
                      ->groupEnd();
        }

        $data = [
            'title'               => 'Riwayat Stok Masuk',
            'currentAdminMenu'    => 'inventory',
            'currentAdminSubMenu' => 'stokmasuk',
            'stok'                => $stokModel->orderBy('t_stok_masuk.id', 'DESC')->paginate($perPage, 'bootstrap'),
            'bahan'               => model('BahanBakuModel')->findAll(),
            'pager'               => $stokModel->pager,
            'keyword'             => $keyword,
            'perPage'             => $perPage
        ];

        return view('admin/stokmasuk/index', $data);
    }

    public function simpan()
    {
        $rules = [
            'bahan_baku_id'  => 'required',
            'qty'            => 'required|numeric|greater_than[0]',
            'isi_per_satuan' => 'required|numeric|greater_than[0]',
            'harga_satuan'   => 'required|numeric',
            'nama_supplier'  => 'permit_empty|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $bahanID = $this->request->getPost('bahan_baku_id');
            $qty     = $this->request->getPost('qty');
            $isi     = $this->request->getPost('isi_per_satuan'); 
            $harga   = $this->request->getPost('harga_satuan');

            $totalGram  = $qty * $isi;
            $totalHarga = $qty * $harga;

            // 1. Simpan Riwayat
            model('StokMasukModel')->save([
                'bahan_baku_id' => $bahanID,
                'qty'           => $qty,
                'isi_per_satuan'=> $isi,
                'harga_satuan'  => $harga,
                'total_harga'   => $totalHarga,
                'nama_supplier' => $this->request->getPost('nama_supplier'),
                'tanggal_masuk' => $this->request->getPost('tanggal_masuk'),
                'keterangan'    => $this->request->getPost('keterangan'),
            ]);

            // 2. Update Stok di Master Bahan Baku
            $bahanModel = model('BahanBakuModel');
            $dataBahan  = $bahanModel->find($bahanID);

            if (!$dataBahan) {
                throw new \Exception("Data bahan baku tidak ditemukan!");
            }
            
            $stokBaru = $dataBahan->stok_sekarang + $totalGram;
            $bahanModel->update($bahanID, ['stok_sekarang' => $stokBaru]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Gagal memproses transaksi stok.');
            }

            return redirect()->to('admin/stokmasuk')->with('success', 'Stok berhasil ditambahkan!');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function tambah() 
    {
        $data = [
            'title'               => 'Tambah Stok Masuk',
            'currentAdminMenu'    => 'inventory',
            'currentAdminSubMenu' => 'stokmasuk',
            'bahan'               => model('BahanBakuModel')->findAll()
        ];
        return view('admin/stokmasuk/tambah', $data);
    }

    public function getBahanbakuAjax()
    {
        $search = $this->request->getVar('q');
        $data   = model('BahanBakuModel')
                    ->like('nama_bahan', $search)
                    ->limit(10)
                    ->findAll();

        $result = [];
        foreach ($data as $row) {
            $result[] = [
                'id'   => $row->id,
                'text' => $row->nama_bahan
            ];
        }

        return $this->response->setJSON($result);
    }
}