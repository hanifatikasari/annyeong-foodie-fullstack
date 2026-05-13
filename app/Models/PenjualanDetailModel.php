<?php

namespace App\Models;

use CodeIgniter\Model;

class PenjualanDetailModel extends Model
{
    protected $table         = 't_penjualan_detail';
    protected $primaryKey    = 'id';
    protected $returnType    = 'object';

    protected $allowedFields = [
        'penjualan_id',
        'product_id',
        'qty',
        'hpp_price',
        'selling_price',
        'subtotal',
    ];

    /**
     * Ambil detail item berdasarkan transaksi penjualan.
     */
    public function getDetailByPenjualan(int $penjualanId): array
    {
        return $this->select('t_penjualan_detail.*, products.name as product_name, products.sku')
                    ->join('products', 'products.id = t_penjualan_detail.product_id')
                    ->where('penjualan_id', $penjualanId)
                    ->findAll();
    }

    /**
     * Ambil produk terlaris.
     */
    public function getBestSelling(int $limit = 4): array
    {
        return $this->select('
                        t_penjualan_detail.product_id,
                        SUM(t_penjualan_detail.qty) as total_terjual,
                        products.name,
                        products.slug,
                        products.sku,
                        products.price
                    ')
                    ->join('products', 'products.id = t_penjualan_detail.product_id')
                    ->groupBy('t_penjualan_detail.product_id')
                    ->orderBy('total_terjual', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
}