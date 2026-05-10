<?php

namespace App\Models;

use CodeIgniter\Model;

class PenjualanDetailModel extends Model
{
    protected $table         = 't_penjualan_detail';
    protected $primaryKey    = 'id';
    protected $returnType    = 'object';
    protected $allowedFields = [
        'penjualan_id', 'product_id', 'qty',
        'hpp_price', 'selling_price', 'subtotal',
    ];
}