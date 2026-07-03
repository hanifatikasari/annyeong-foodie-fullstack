<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DashboardModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $dashboard = new DashboardModel();

        $canViewSales = $this->auth->inGroup(['admin', 'penjualan']);
        $canViewProduction = $this->auth->inGroup(['admin', 'produksi']);
        $canViewInventory = $this->auth->inGroup(['admin', 'gudang', 'produksi', 'pemilik']);
        $canViewReports = $this->auth->inGroup(['admin', 'pemilik']);

        $data = [
            'title'               => 'Dashboard Annyeong Foodie',
            'currentAdminMenu'    => 'dashboard',
            'currentAdminSubMenu' => 'dashboard',

            'canViewSales'       => $canViewSales,
            'canViewProduction'  => $canViewProduction,
            'canViewInventory'   => $canViewInventory,
            'canViewReports'     => $canViewReports,

            'penjualanHariIni' => $dashboard->getPenjualanHariIni(),
            'produksiHariIni'  => $dashboard->getProduksiHariIni(),
            'bahanMenipis'     => $dashboard->getBahanMenipis(),
            'metodeBayar'      => $dashboard->getMetodePembayaranTerpopuler(),
            'grafikPenjualan'  => $dashboard->getGrafikPenjualan(),
            'stokMenipis'      => $dashboard->getStokMenipis(),
            'topProduk'        => $dashboard->getTopProduk(),
        ];

        return view('admin/dashboard/index', $data);
    }
}