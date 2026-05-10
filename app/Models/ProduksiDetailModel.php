<?php

namespace App\Models;

use CodeIgniter\Model;

class ProduksiDetailModel extends Model
{
    protected $table         = 't_produksi_detail';
    protected $primaryKey    = 'id';
    protected $returnType    = 'object';
    protected $allowedFields = ['produksi_id', 'bahan_baku_id', 'qty_digunakan'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
}