<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Struk extends BaseController
{
    public function index($id)
    {
        $penjualanModel = model('PenjualanModel');
        $detailModel    = model('PenjualanDetailModel');

        $penjualan = $penjualanModel->find($id);

        if (!$penjualan) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $detail = $detailModel
            ->select('t_penjualan_detail.*, m_product.name AS product_name')
            ->join('m_product', 'm_product.id = t_penjualan_detail.product_id')
            ->where('penjualan_id', $id)
            ->findAll();

        return view('admin/penjualan/struk', [
            'penjualan' => $penjualan,
            'detail'     => $detail,
        ]);
    }
}