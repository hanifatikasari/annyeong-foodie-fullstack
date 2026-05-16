<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * PenjualanDetailModel — disesuaikan dengan skema DB aktual.
 *
 * Skema DB aktual dari prompt:
 *   t_penjualan_detail:
 *     - id
 *     - penjualan_id
 *     - product_id
 *     - qty
 *     - selling_price  (bukan price)
 *     - subtotal
 *     - hpp_price      (untuk hitung laba — opsional)
 */
class PenjualanDetailModel extends Model
{
    protected $table         = 't_penjualan_detail';
    protected $primaryKey    = 'id';
    protected $returnType    = 'object';
    protected $allowedFields = [
        'penjualan_id',
        'product_id',
        'qty',
        'selling_price',      
        'subtotal',
        'hpp_price',  
    ];
    protected $useTimestamps = false;

    /**
     * Ambil detail beserta nama produk.
     * Return: array of objects
     */
    public function getDetailByPenjualan(int $penjualanId): array
    {
        return $this->select('t_penjualan_detail.*, products.name as product_name, products.sku')
                    ->join('products', 'products.id = t_penjualan_detail.product_id')
                    ->where('t_penjualan_detail.penjualan_id', $penjualanId)
                    ->findAll();
    }

    /**
     * Ambil produk terlaris berdasarkan total qty terjual.
     * Return: array of objects
     */
    public function getBestSelling(int $limit = 4): array
    {
        return $this->select('product_id, SUM(qty) as total_terjual')
                    ->groupBy('product_id')
                    ->orderBy('total_terjual', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
}