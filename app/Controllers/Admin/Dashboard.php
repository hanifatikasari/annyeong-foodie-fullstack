<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        // === STATISTIK HARI INI ===
        $today = date('Y-m-d');

        // Penjualan hari ini
        $penjualanHariIni = $db->table('t_penjualan')
            ->selectSum('total_bayar', 'total')
            ->selectCount('id', 'jumlah')
            ->where('DATE(created_at)', $today)
            ->get()->getRow();

        // Produksi hari ini
        $produksiHariIni = $db->table('t_produksi')
            ->selectSum('qty_hasil', 'total')
            ->selectCount('id', 'jumlah')
            ->where('tanggal_produksi', $today)
            ->where('status_qc', 'Lolos')
            ->get()->getRow();

        // Bahan baku menipis (stok <= stok_minimal)
        $bahanMenipis = model('BahanBakuModel')
            ->where('stok_sekarang <=', $db->escape('stok_minimal'), false)
            ->where('deleted_at IS NULL')
            ->countAllResults();

        // Total pelanggan (user group pelanggan)
        $totalPelanggan = $db->table('users_groups')
            ->join('groups', 'groups.id = users_groups.group_id')
            ->where('groups.name', 'pelanggan')
            ->countAllResults();

        // === GRAFIK PENJUALAN 7 HARI TERAKHIR ===
        $grafikPenjualan = [];
        for ($i = 6; $i >= 0; $i--) {
            $tgl = date('Y-m-d', strtotime("-$i days"));
            $row = $db->table('t_penjualan')
                ->selectSum('total_bayar', 'total')
                ->where('DATE(created_at)', $tgl)
                ->get()->getRow();
            $grafikPenjualan[] = [
                'tanggal' => date('d/m', strtotime($tgl)),
                'total'   => (int) ($row->total ?? 0),
            ];
        }

        // === STOK PRODUK MENIPIS ===
        $stokMenipis = $db->table('products')
            ->select('products.name, products.sku, product_inventories.qty, product_inventories.low_stock_threshold')
            ->join('product_inventories', 'product_inventories.product_id = products.id')
            ->where('product_inventories.qty <=', $db->escape('product_inventories.low_stock_threshold'), false)
            ->where('products.deleted_at IS NULL')
            ->orderBy('product_inventories.qty', 'ASC')
            ->limit(5)
            ->get()->getResult();

        // === TOP 5 PRODUK TERLARIS (30 HARI) ===
        $topProduk = $db->table('t_penjualan_detail')
            ->select('products.name, SUM(t_penjualan_detail.qty) as total_qty, SUM(t_penjualan_detail.subtotal) as total_omzet')
            ->join('products', 'products.id = t_penjualan_detail.product_id')
            ->join('t_penjualan', 't_penjualan.id = t_penjualan_detail.penjualan_id')
            ->where('t_penjualan.created_at >=', date('Y-m-d', strtotime('-30 days')))
            ->groupBy('t_penjualan_detail.product_id')
            ->orderBy('total_qty', 'DESC')
            ->limit(5)
            ->get()->getResult();

        $data = [
            'title'               => 'Dashboard Annyeong Foodie',
            'currentAdminMenu'    => 'dashboard',
            'currentAdminSubMenu' => 'dashboard',
            'penjualanHariIni'    => $penjualanHariIni,
            'produksiHariIni'     => $produksiHariIni,
            'bahanMenipis'        => $bahanMenipis,
            'totalPelanggan'      => $totalPelanggan,
            'grafikPenjualan'     => $grafikPenjualan,
            'stokMenipis'         => $stokMenipis,
            'topProduk'           => $topProduk,
        ];

        return view('admin/dashboard/index', $data);
    }
}