<?php

namespace App\Models;

use CodeIgniter\Model;

class ShippingAreaModel extends Model
{
    protected $table            = 'm_shipping_area';
    protected $primaryKey       = 'id';
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $allowedFields = [
        'area_name',
        'ongkir',
        'estimasi',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
}