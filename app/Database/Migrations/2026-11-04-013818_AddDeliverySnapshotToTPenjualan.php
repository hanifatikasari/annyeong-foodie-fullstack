<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Snapshot data pengiriman per order, supaya order lama tidak berubah
 * ketika pelanggan mengedit profil / admin mengubah nama area.
 * Semua kolom nullable: order lama dan order pickup tetap valid.
 * `shipping_address`, `shipping_area_id`, `shipping_cost` yang sudah ada tidak diubah.
 */
class AddDeliverySnapshotToTPenjualan extends Migration
{
    private function columns(): array
    {
        return [
            'shipping_recipient_name'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'shipping_recipient_phone' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'shipping_area_name'       => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'shipping_province'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'shipping_city'            => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'shipping_postal_code'     => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'shipping_latitude'        => ['type' => 'DECIMAL', 'constraint' => '10,7', 'null' => true, 'default' => null],
            'shipping_longitude'       => ['type' => 'DECIMAL', 'constraint' => '10,7', 'null' => true, 'default' => null],
            'shipping_gmaps_link'      => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
        ];
    }

    public function up()
    {
        $fields = [];

        foreach ($this->columns() as $name => $definition) {
            if (! $this->db->fieldExists($name, 't_penjualan')) {
                $fields[$name] = $definition;
            }
        }

        if ($fields !== []) {
            $this->forge->addColumn('t_penjualan', $fields);
        }
    }

    public function down()
    {
        foreach (array_keys($this->columns()) as $name) {
            if ($this->db->fieldExists($name, 't_penjualan')) {
                $this->forge->dropColumn('t_penjualan', $name);
            }
        }
    }
}