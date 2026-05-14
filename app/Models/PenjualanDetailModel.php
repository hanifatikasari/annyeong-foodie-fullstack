<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * PenjualanDetailModel
 * FIX: returnType = 'object' sesuai requirement project
 */
class PenjualanDetailModel extends Model
{
    protected $table         = 't_penjualan_detail';
    protected $primaryKey    = 'id';
    protected $returnType    = 'object';
    protected $allowedFields = [
        'penjualan_id', 'product_id', 'qty',
        'hpp_price', 'selling_price', 'subtotal',
    ];
    protected $useTimestamps = false;

    /**
     * Ambil detail beserta nama produk.
     * NOTE: Mengembalikan array of objects (bukan array of arrays)
     * karena returnType = 'object'
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