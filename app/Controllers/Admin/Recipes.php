<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Recipes extends BaseController
{
    public function __construct()
    {
        helper(['url', 'form']);
    }

    /**
     * Halaman utama: pilih produk untuk lihat/kelola resepnya
     */
    public function index()
    {
        $keyword = $this->request->getGet('table_search');
        $perPage = $this->request->getGet('perPage') ?? 10;

        $productModel = model('ProductModel');

        $productModel->select('products.*, product_inventories.qty')
                     ->join('product_inventories', 'product_inventories.product_id = products.id', 'left')
                     ->where('products.type', 'simple')
                     ->where('products.parent_id IS NULL')
                     ->where('products.deleted_at IS NULL');

        if (!empty($keyword)) {
            $productModel->groupStart()
                         ->like('products.name', $keyword)
                         ->orLike('products.sku', $keyword)
                         ->groupEnd();
        }

        $data = [
            'title'               => 'Manajemen Resep (BOM)',
            'currentAdminMenu'    => 'production',
            'currentAdminSubMenu' => 'recipes',
            'products'            => $productModel->orderBy('products.name', 'ASC')->paginate($perPage, 'bootstrap'),
            'pager'               => $productModel->pager,
            'keyword'             => $keyword,
            'perPage'             => $perPage,
        ];

        return view('admin/recipes/index', $data);
    }

    /**
     * Form tambah/edit bahan untuk satu produk
     */
    public function detail($productId)
    {
        $product = model('ProductModel')->find($productId);
        if (!$product) {
            return redirect()->to('admin/recipes')->with('error', 'Produk tidak ditemukan.');
        }

        $recipeModel = model('RecipeModel');

        // Ambil semua bahan dalam resep produk ini + info bahan baku
        $recipes = $recipeModel
            ->select('m_recipes.*, m_bahan_baku.nama_bahan, m_bahan_baku.satuan, m_bahan_baku.harga_beli_satuan')
            ->join('m_bahan_baku', 'm_bahan_baku.id = m_recipes.bahan_baku_id')
            ->where('m_recipes.product_id', $productId)
            ->findAll();

        // Hitung total HPP dari resep
        $hppTotal = 0;
        foreach ($recipes as $r) {
            $hppTotal += $r->jumlah_kebutuhan * $r->harga_beli_satuan;
        }

        $data = [
            'title'               => 'Resep: ' . $product->name,
            'currentAdminMenu'    => 'production',
            'currentAdminSubMenu' => 'recipes',
            'product'             => $product,
            'recipes'             => $recipes,
            'hppTotal'            => $hppTotal,
            'bahanList'           => model('BahanBakuModel')->where('deleted_at IS NULL')->orderBy('nama_bahan')->findAll(),
        ];

        return view('admin/recipes/detail', $data);
    }

    /**
     * Simpan bahan baru ke resep
     */
    public function simpan($productId)
    {
        $product = model('ProductModel')->find($productId);
        if (!$product) {
            return redirect()->to('admin/recipes')->with('error', 'Produk tidak ditemukan.');
        }

        $rules = [
            'bahan_baku_id'     => 'required|numeric',
            'jumlah_kebutuhan'  => 'required|decimal|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $bahanId = $this->request->getPost('bahan_baku_id');

        // Cek duplikasi
        $existing = model('RecipeModel')
            ->where('product_id', $productId)
            ->where('bahan_baku_id', $bahanId)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Bahan ini sudah ada dalam resep. Gunakan tombol Edit untuk mengubah jumlahnya.');
        }

        try {
            model('RecipeModel')->save([
                'product_id'        => $productId,
                'bahan_baku_id'     => $bahanId,
                'jumlah_kebutuhan'  => $this->request->getPost('jumlah_kebutuhan'),
                'keterangan'        => $this->request->getPost('keterangan'),
            ]);

            // Update HPP di tabel products
            $this->recalculateHpp($productId);

            return redirect()->to("admin/recipes/detail/$productId")
                ->with('success', 'Bahan berhasil ditambahkan ke resep!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    /**
     * Update jumlah bahan dalam resep
     */
    public function update($productId, $recipeId)
    {
        $rules = [
            'jumlah_kebutuhan' => 'required|decimal|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        try {
            model('RecipeModel')->update($recipeId, [
                'jumlah_kebutuhan' => $this->request->getPost('jumlah_kebutuhan'),
                'keterangan'       => $this->request->getPost('keterangan'),
            ]);

            $this->recalculateHpp($productId);

            return redirect()->to("admin/recipes/detail/$productId")
                ->with('success', 'Resep berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    /**
     * Hapus bahan dari resep
     */
    public function hapus($productId, $recipeId)
    {
        try {
            model('RecipeModel')->delete($recipeId);
            $this->recalculateHpp($productId);

            return redirect()->to("admin/recipes/detail/$productId")
                ->with('success', 'Bahan berhasil dihapus dari resep.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    /**
     * AJAX: cari bahan baku
     */
    public function getBahanAjax()
    {
        $q = $this->request->getGet('q');
        $data = model('BahanBakuModel')
            ->select('id, nama_bahan as text, satuan, harga_beli_satuan')
            ->like('nama_bahan', $q)
            ->where('deleted_at IS NULL')
            ->limit(15)
            ->findAll();

        return $this->response->setJSON(array_map(fn($b) => [
            'id'              => $b->id,
            'text'            => $b->nama_bahan . ' (' . $b->satuan . ')',
            'satuan'          => $b->satuan,
            'harga'           => $b->harga_beli_satuan,
        ], $data));
    }

    /**
     * Hitung ulang HPP produk berdasarkan resep terbaru
     * HPP = SUM(jumlah_kebutuhan * harga_beli_satuan) per 1 porsi
     */
    private function recalculateHpp(int $productId): void
    {
        $recipes = model('RecipeModel')
            ->select('m_recipes.jumlah_kebutuhan, m_bahan_baku.harga_beli_satuan')
            ->join('m_bahan_baku', 'm_bahan_baku.id = m_recipes.bahan_baku_id')
            ->where('m_recipes.product_id', $productId)
            ->findAll();

        $hpp = 0;
        foreach ($recipes as $r) {
            $hpp += $r->jumlah_kebutuhan * $r->harga_beli_satuan;
        }

        model('ProductModel')->update($productId, ['hpp_total' => round($hpp)]);
    }
}