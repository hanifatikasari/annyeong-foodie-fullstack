<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Reports extends BaseController
{
    public function __construct()
    {
        helper(['url', 'form']);
    }

    public function index()
    {
        $data = [
            'title'               => 'Laporan',
            'currentAdminMenu'    => 'report',
            'currentAdminSubMenu' => 'report',
        ];
        return view('admin/reports/index', $data);
    }

    /**
     * Laporan Penjualan
     */
    public function penjualan()
    {
        $dari  = $this->request->getGet('dari')  ?? date('Y-m-01');
        $sampai = $this->request->getGet('sampai') ?? date('Y-m-d');

        $db = \Config\Database::connect();

        $rows = $db->table('t_penjualan')
            ->select('DATE(created_at) as tanggal, COUNT(id) as jumlah_transaksi,
                      SUM(total_harga) as gross, SUM(diskon) as diskon, SUM(total_bayar) as nett')
            ->where('DATE(created_at) >=', $dari)
            ->where('DATE(created_at) <=', $sampai)
            ->groupBy('DATE(created_at)')
            ->orderBy('tanggal', 'ASC')
            ->get()->getResult();

        // Hitung laba dari detail
        $labaRows = $db->table('t_penjualan_detail')
            ->select('DATE(t_penjualan.created_at) as tanggal,
                      SUM((t_penjualan_detail.selling_price - t_penjualan_detail.hpp_price) * t_penjualan_detail.qty) as laba')
            ->join('t_penjualan', 't_penjualan.id = t_penjualan_detail.penjualan_id')
            ->where('DATE(t_penjualan.created_at) >=', $dari)
            ->where('DATE(t_penjualan.created_at) <=', $sampai)
            ->groupBy('DATE(t_penjualan.created_at)')
            ->get()->getResult();

        // Index laba by tanggal
        $labaByTanggal = [];
        foreach ($labaRows as $l) {
            $labaByTanggal[$l->tanggal] = $l->laba;
        }

        // Merge laba ke rows
        foreach ($rows as &$r) {
            $r->laba = $labaByTanggal[$r->tanggal] ?? 0;
        }

        $data = [
            'title'               => 'Laporan Penjualan',
            'currentAdminMenu'    => 'report',
            'currentAdminSubMenu' => 'report',
            'rows'                => $rows,
            'dari'                => $dari,
            'sampai'              => $sampai,
            'totalGross'          => array_sum(array_column($rows, 'gross')),
            'totalNett'           => array_sum(array_column($rows, 'nett')),
            'totalLaba'           => array_sum(array_column($rows, 'laba')),
            'totalDiskon'         => array_sum(array_column($rows, 'diskon')),
        ];

        return view('admin/reports/penjualan', $data);
    }

    /**
     * Laporan Produksi
     */
    public function produksi()
    {
        $dari   = $this->request->getGet('dari')   ?? date('Y-m-01');
        $sampai = $this->request->getGet('sampai') ?? date('Y-m-d');

        $db = \Config\Database::connect();

        $rows = $db->table('t_produksi')
            ->select('t_produksi.*, products.name as nama_produk, products.hpp_total')
            ->join('products', 'products.id = t_produksi.product_id')
            ->where('tanggal_produksi >=', $dari)
            ->where('tanggal_produksi <=', $sampai)
            ->orderBy('tanggal_produksi', 'DESC')
            ->get()->getResult();

        $data = [
            'title'               => 'Laporan Produksi',
            'currentAdminMenu'    => 'report',
            'currentAdminSubMenu' => 'report',
            'rows'                => $rows,
            'dari'                => $dari,
            'sampai'              => $sampai,
            'totalQty'            => array_sum(array_column($rows, 'qty_hasil')),
            'totalHpp'            => array_sum(array_map(fn($r) => $r->qty_hasil * $r->hpp_total, $rows)),
        ];

        return view('admin/reports/produksi', $data);
    }

    /**
     * Laporan Stok Bahan Baku saat ini
     */
    public function stokBahan()
    {
        $bahan = model('BahanBakuModel')
            ->select('m_bahan_baku.*, categories.name as kategori')
            ->join('categories', 'categories.id = m_bahan_baku.category_id', 'left')
            ->where('m_bahan_baku.deleted_at IS NULL')
            ->orderBy('categories.name')
            ->orderBy('m_bahan_baku.nama_bahan')
            ->findAll();

        $data = [
            'title'               => 'Laporan Stok Bahan Baku',
            'currentAdminMenu'    => 'report',
            'currentAdminSubMenu' => 'report',
            'bahan'               => $bahan,
        ];

        return view('admin/reports/stok_bahan', $data);
    }
}