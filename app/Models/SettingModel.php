<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table            = 'settings';
    protected $primaryKey       = 'id';

    protected $returnType       = 'object';

    protected $allowedFields = [
        'setting_key',
        'setting_value',
        'setting_group',
        'label',
    ];

    protected $useTimestamps = true;

    public function getValue($key, $default = null)
    {
        $row = $this->where('setting_key', $key)->first();

        return $row
            ? $row->setting_value
            : $default;
    }

    public function setValue($key, $value)
    {
        $row = $this->where('setting_key', $key)->first();

        if ($row) {
            return $this->update(
                $row->id,
                ['setting_value' => $value]
            );
        }

        return $this->insert([
            'setting_key'   => $key,
            'setting_value' => $value,
        ]);
    }
}