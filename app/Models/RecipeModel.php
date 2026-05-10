<?php

namespace App\Models;

use CodeIgniter\Model;

class RecipeModel extends Model
{
    protected $table            = 'm_recipes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $allowedFields    = [
        'product_id',
        'bahan_baku_id',
        'jumlah_kebutuhan',
        'keterangan',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $useSoftDeletes = false; // resep tidak perlu soft delete
}