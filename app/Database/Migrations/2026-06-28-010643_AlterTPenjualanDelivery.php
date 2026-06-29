<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use Config\Database;

class AlterTPenjualanDelivery extends Migration
{
    public function up()
    {
        $this->forge->addColumn('t_penjualan', [

            'delivery_type' => [
                'type'       => 'ENUM',
                'constraint' => ['pickup', 'delivery'],
                'default'    => 'pickup',
                'after'      => 'shipping_address',
            ],

            'shipping_area_id' => [
                'type'       => 'SMALLINT',
                'constraint' => 5,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'delivery_type',
            ],

            'shipping_cost' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 8,
                'unsigned'   => true,
                'default'    => 0,
                'after'      => 'shipping_area_id',
            ],

            'handling_fee' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 8,
                'unsigned'   => true,
                'default'    => 0,
                'after'      => 'shipping_cost',
            ],

            'delivery_courier_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'handling_fee',
            ],
        ]);

        $db = Database::connect();

        $db->query("
            ALTER TABLE t_penjualan
            ADD CONSTRAINT fk_shipping_area
            FOREIGN KEY (shipping_area_id)
            REFERENCES m_shipping_area(id)
            ON DELETE SET NULL
            ON UPDATE CASCADE
        ");

        $db->query("
            ALTER TABLE t_penjualan
            ADD CONSTRAINT fk_delivery_courier
            FOREIGN KEY (delivery_courier_id)
            REFERENCES users(id)
            ON DELETE SET NULL
            ON UPDATE CASCADE
        ");
    }

    public function down()
    {
        $db = Database::connect();

        $db->query("
            ALTER TABLE t_penjualan
            DROP FOREIGN KEY fk_shipping_area
        ");

        $db->query("
            ALTER TABLE t_penjualan
            DROP FOREIGN KEY fk_delivery_courier
        ");

        $this->forge->dropColumn('t_penjualan', [
            'delivery_type',
            'shipping_area_id',
            'shipping_cost',
            'handling_fee',
            'delivery_courier_id',
        ]);
    }
}