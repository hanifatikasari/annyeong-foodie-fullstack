<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCategoriesTable extends Migration
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
			'name' => [
				'type' => 'VARCHAR', //tepung, gula, daging
				'constraint' => 50,
			],
			'slug' => [
				'type' => 'VARCHAR',
				'constraint' => 60,
			],
			'parent_id' => [ //Self-Referencing Relationship.[sub kategori]
				'type' => 'mediumint',
				'constraint' => 8,
				'unsigned' => true,
				'null' => true,
			],
			'prefix' => [
				'type'       => 'CHAR',
				'constraint' => 3, // Cukup 5 huruf: AYM, TPG, BUM
			],

            'created_at' => ['type' => 'DATETIME', 'null' => true],
        	'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],  
		]);

		$this->forge->addKey('id', true);
		$this->forge->addKey('slug');
		$this->forge->addForeignKey('parent_id', 'categories', 'id', 'SET NULL', 'CASCADE');
		$this->forge->createTable('categories');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('categories');
	}
}
