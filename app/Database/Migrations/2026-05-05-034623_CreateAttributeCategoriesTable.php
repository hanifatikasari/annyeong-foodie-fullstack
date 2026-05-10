<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAttributeCategoriesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'mediumint',
                'constraint' => 8,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'attribute_id' => [
                'type' => 'SMALLINT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'category_id' => [
                'type' => 'MEDIUMINT',
                'constraint' => 8,
                'unsigned' => true,
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
        

        // Foreign Keys
        $this->forge->addForeignKey(
            'attribute_id',
            'attributes',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->addForeignKey(
            'category_id',
            'categories',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Obiar tidak duplicate
        $this->forge->addUniqueKey(['attribute_id', 'category_id']);

        $this->forge->createTable('attribute_categories');
    }

    public function down()
    {
        $this->forge->dropTable('attribute_categories');
    }
}