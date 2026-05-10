<?php namespace App\Entities;

use CodeIgniter\Entity\Entity;

class AttributeOption extends Entity
{
    protected $dates = ['created_at', 'updated_at'];
    // Tambahkan fungsi ini agar slug otomatis terisi saat nama diinput
    public function setName(string $name)
    {
        $this->attributes['name'] = $name;
        $this->attributes['slug'] = url_title($name, '-', true);
        return $this;
    }
}
