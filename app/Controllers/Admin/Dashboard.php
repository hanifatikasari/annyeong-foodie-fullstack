<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DashboardModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $dashboard = new DashboardModel();

        $data = [
            'title'               => 'Dashboard Annyeong Foodie',
            'currentAdminMenu'    => 'dashboard',
            'currentAdminSubMenu' => 'dashboard',

            'penjualanHariIni' => $dashboard->getPenjualanHariIni(),
            'produksiHariIni'  => $dashboard->getProduksiHariIni(),
            'bahanMenipis'     => $dashboard->getBahanMenipis(),
            'totalPelanggan'   => $dashboard->getTotalPelanggan(),
            'grafikPenjualan'  => $dashboard->getGrafikPenjualan(),
            'stokMenipis'      => $dashboard->getStokMenipis(),
            'topProduk'        => $dashboard->getTopProduk(),
        ];

        return view('admin/dashboard/index',$data);
    }
}