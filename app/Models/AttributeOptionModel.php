<?php 

namespace App\Models;

use CodeIgniter\Model;

class AttributeOptionModel extends Model
{
    protected $table      = 'attribute_options';
    protected $primaryKey = 'id';
    protected $returnType = 'App\Entities\AttributeOption';

    protected $allowedFields = ['attribute_id', 'name', 'slug'];

    protected $useTimestamps = true;

    // Rules: Nama wajib ada. Untuk is_unique, pastikan abaikan ID sendiri saat update
    protected $validationRules = [
        'id'          => 'permit_empty|numeric', //  WAJIB untuk placeholder
        'attribute_id' => 'required|numeric',
        'name'         => 'required|is_unique[attribute_options.name,id,{id}]',
    ];

    protected $beforeInsert = ['generateSlug'];
    protected $beforeUpdate = ['generateSlug']; // Tambahkan ini!

    protected function generateSlug(array $data)
    {
        // Cek apakah ada data name yang dikirim
        if (isset($data['data']['name'])) {
            $data['data']['slug'] = strtolower(url_title($data['data']['name']));
        }

        return $data;
    }
}