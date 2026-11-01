<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserCartItemsTable extends Migration
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
            'user_id' => [
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
                'type'       => 'mediumint',
                'constraint' => 8,
                'unsigned'   => true,
                'default'    => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);

        // Satu user tidak boleh memiliki dua baris
        // untuk product_id yang sama.
        $this->forge->addUniqueKey(
            ['user_id', 'product_id'],
            'user_product_unique'
        );

        $this->forge->addKey('user_id');
        $this->forge->createTable('user_cart_items', true);
    }

    public function down()
    {
        $this->forge->dropTable('user_cart_items', true);
    }
}

