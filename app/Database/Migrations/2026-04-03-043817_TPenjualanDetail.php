<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TPenjualanDetail extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'penjualan_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'product_id' => [
                'type'       => 'mediumint',
                'constraint' => 8,
                'unsigned'   => true,
            ],
            'qty' => [
                'type'       => 'smallint', //65rb
                'constraint' => 5,
                'unsigned'   => true,
            ],
            'hpp_price' => [ // Harga modal (untuk hitung laba bersih)
                'type'       => 'mediumint',
                'constraint' => '8',
                'unsigned'   => true,
                'default'    => 0,
            ],
            'selling_price' => [ // Penting! Kalau suatu saat harga naik, data lama gak berubah
                'type'       => 'mediumint',
                'constraint' => '8',
                'unsigned'   => true,
            ],
            'subtotal' => [
                'type'       => 'int',
                'constraint' => '11',
                'unsigned'   => true    ,
                'default'    => 0,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('penjualan_id', 't_penjualan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('t_penjualan_detail');
    }

    public function down()
    {
        $this->forge->dropTable('t_penjualan_detail');
    }
}
