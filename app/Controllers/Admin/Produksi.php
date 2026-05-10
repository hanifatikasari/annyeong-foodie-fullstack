<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Produksi extends BaseController
{
    public function __construct()
    {
        helper(['url', 'form']);
    }

    public function index()
    {
        $keyword = $this->request->getGet('table_search');
        $perPage = $this->request->getGet('perPage') ?? 10;

        $model = model('ProduksiModel');
        $model->select('t_produksi.*, products.name as nama_produk, products.sku, users.first_name, users.last_name')
              ->join('products', 'products.id = t_produksi.product_id')
              ->join('users', 'users.id = t_produksi.user_id', 'left');

        if (!empty($keyword)) {
            $model->groupStart()
                  ->like('t_produksi.kode_produksi', $keyword)
                  ->orLike('products.name', $keyword)
                  ->groupEnd();
        }

        $data = [
            'title'               => 'Proses Produksi',
            'currentAdminMenu'    => 'production',
            'currentAdminSubMenu' => 'process',
            'produksi'            => $model->orderBy('t_produksi.id', 'DESC')->paginate($perPage, 'bootstrap'),
            'pager'               => $model->pager,
            'keyword'             => $keyword,
            'perPage'             => $perPage,
        ];

        return view('admin/produksi/index', $data);
    }

    public function tambah()
    {
        // Hanya produk yang sudah punya resep yang bisa diproduksi
        $produkBisaDiproduksi = model('ProductModel')
            ->select('products.id, products.name, products.sku, products.hpp_total')
            ->join('m_recipes', 'm_recipes.product_id = products.id')
            ->where('products.type', 'simple')
            ->where('products.deleted_at IS NULL')
            ->where('products.parent_id IS NULL')
            ->groupBy('products.id')
            ->findAll();

        $data = [
            'title'               => 'Catat Produksi Baru',
            'currentAdminMenu'    => 'production',
            'currentAdminSubMenu' => 'process',
            'products'            => $produkBisaDiproduksi,
        ];

        return view('admin/produksi/tambah', $data);
    }

    /**
     * Simulasi: hitung kebutuhan bahan & cek stok sebelum produksi dikonfirmasi
     */
    public function simulasi()
    {
        $productId = $this->request->getPost('product_id');
        $qty       = (int) $this->request->getPost('qty_hasil');

        if (!$productId || $qty <= 0) {
            return $this->response->setJSON(['error' => 'Data tidak valid']);
        }

        $recipes = model('RecipeModel')
            ->select('m_recipes.*, m_bahan_baku.nama_bahan, m_bahan_baku.satuan, m_bahan_baku.stok_sekarang, m_bahan_baku.harga_beli_satuan')
            ->join('m_bahan_baku', 'm_bahan_baku.id = m_recipes.bahan_baku_id')
            ->where('m_recipes.product_id', $productId)
            ->findAll();

        $result   = [];
        $cukup    = true;

        foreach ($recipes as $r) {
            $dibutuhkan = $r->jumlah_kebutuhan * $qty;
            $kurang     = $dibutuhkan > $r->stok_sekarang;
            if ($kurang) $cukup = false;

            $result[] = [
                'nama_bahan'    => $r->nama_bahan,
                'satuan'        => $r->satuan,
                'stok_sekarang' => (float) $r->stok_sekarang,
                'dibutuhkan'    => $dibutuhkan,
                'kurang'        => $kurang,
                'biaya'         => round($dibutuhkan * $r->harga_beli_satuan),
            ];
        }

        return $this->response->setJSON([
            'bahan'    => $result,
            'cukup'    => $cukup,
            'qty'      => $qty,
        ]);
    }

    /**
     * Proses produksi dengan transaction:
     * 1. Simpan header t_produksi
     * 2. Simpan detail t_produksi_detail (snapshot)
     * 3. Kurangi stok m_bahan_baku
     * 4. Tambah stok product_inventories
     */
    public function simpan()
    {
        $rules = [
            'product_id'        => 'required|numeric',
            'qty_hasil'         => 'required|numeric|greater_than[0]',
            'tanggal_produksi'  => 'required|valid_date',
            'status_qc'         => 'required|in_list[Lolos,Reject]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $productId  = $this->request->getPost('product_id');
        $qty        = (int) $this->request->getPost('qty_hasil');
        $tanggal    = $this->request->getPost('tanggal_produksi');
        $statusQc   = $this->request->getPost('status_qc');
        $catatan    = $this->request->getPost('catatan');
        $userId     = session()->get('user_id');

        // Ambil resep
        $recipes = model('RecipeModel')
            ->select('m_recipes.*, m_bahan_baku.nama_bahan, m_bahan_baku.satuan, m_bahan_baku.stok_sekarang')
            ->join('m_bahan_baku', 'm_bahan_baku.id = m_recipes.bahan_baku_id')
            ->where('m_recipes.product_id', $productId)
            ->findAll();

        if (empty($recipes)) {
            return redirect()->back()->with('error', 'Produk ini belum memiliki resep!');
        }

        // Validasi stok sebelum transaksi
        foreach ($recipes as $r) {
            $dibutuhkan = $r->jumlah_kebutuhan * $qty;
            if ($dibutuhkan > $r->stok_sekarang) {
                return redirect()->back()->with(
                    'error',
                    "Stok {$r->nama_bahan} tidak cukup! Butuh: {$dibutuhkan} {$r->satuan}, Tersedia: {$r->stok_sekarang} {$r->satuan}"
                );
            }
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $produksiModel = model('ProduksiModel');

            // 1. Simpan header produksi
            $kode = $produksiModel->generateKode($tanggal);
            $produksiModel->save([
                'kode_produksi'     => $kode,
                'product_id'        => $productId,
                'user_id'           => $userId,
                'qty_hasil'         => $qty,
                'tanggal_produksi'  => $tanggal,
                'status_qc'         => $statusQc,
                'catatan'           => $catatan,
            ]);
            $produksiId = $db->insertID();

            // 2. Simpan detail & kurangi stok bahan
            $detailModel  = model('ProduksiDetailModel');
            $bahanModel   = model('BahanBakuModel');

            foreach ($recipes as $r) {
                $qtyDigunakan = $r->jumlah_kebutuhan * $qty;

                // Simpan snapshot detail
                $detailModel->save([
                    'produksi_id'   => $produksiId,
                    'bahan_baku_id' => $r->bahan_baku_id,
                    'qty_digunakan' => $qtyDigunakan,
                ]);

                // Kurangi stok bahan baku (hanya jika QC Lolos)
                if ($statusQc === 'Lolos') {
                    $stokBaru = $r->stok_sekarang - $qtyDigunakan;
                    $bahanModel->update($r->bahan_baku_id, ['stok_sekarang' => $stokBaru]);
                }
            }

            // 3. Tambah stok produk (hanya jika QC Lolos)
            if ($statusQc === 'Lolos') {
                $inventoryModel = model('ProductInventoryModel');
                $existing = $inventoryModel->where('product_id', $productId)->first();

                if ($existing) {
                    $inventoryModel->update($existing->id, [
                        'qty' => $existing->qty + $qty,
                    ]);
                } else {
                    $inventoryModel->save([
                        'product_id' => $productId,
                        'qty'        => $qty,
                    ]);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi database gagal.');
            }

            return redirect()->to('admin/produksi')
                ->with('success', "Produksi $kode berhasil dicatat! " .
                    ($statusQc === 'Lolos' ? "Stok produk +{$qty} porsi." : "Status: REJECT, stok tidak berubah."));

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $produksi = model('ProduksiModel')
            ->select('t_produksi.*, products.name as nama_produk, products.sku, products.hpp_total,
                      users.first_name, users.last_name')
            ->join('products', 'products.id = t_produksi.product_id')
            ->join('users', 'users.id = t_produksi.user_id', 'left')
            ->find($id);

        if (!$produksi) {
            return redirect()->to('admin/produksi')->with('error', 'Data tidak ditemukan.');
        }

        $details = model('ProduksiDetailModel')
            ->select('t_produksi_detail.*, m_bahan_baku.nama_bahan, m_bahan_baku.satuan, m_bahan_baku.harga_beli_satuan')
            ->join('m_bahan_baku', 'm_bahan_baku.id = t_produksi_detail.bahan_baku_id')
            ->where('produksi_id', $id)
            ->findAll();

        $data = [
            'title'               => 'Detail Produksi: ' . $produksi->kode_produksi,
            'currentAdminMenu'    => 'production',
            'currentAdminSubMenu' => 'process',
            'produksi'            => $produksi,
            'details'             => $details,
        ];

        return view('admin/produksi/show', $data);
    }
}