<?php

namespace App\Models;

use CodeIgniter\Model;

class BahanBakuModel extends Model
{
    protected $table            = 'm_bahan_baku';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'App\Entities\BahanBaku';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'kode_bahan', 
        'category_id', 
        'nama_bahan', 
        'satuan', 
        'harga_beli_satuan', 
        'stok_sekarang', 
        'stok_minimal'
    ];

    
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime'; 
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

}
