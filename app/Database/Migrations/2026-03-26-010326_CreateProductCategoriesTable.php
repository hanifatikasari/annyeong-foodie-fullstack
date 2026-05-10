<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductCategoriesTable extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => true,
				'auto_increment' => true,
			],
			'product_id' => [
				'type'           => 'mediumint',
				'constraint'     => 8,
				'unsigned'       => true,
			],
			'category_id' => [
				'type'           => 'mediumint',
				'constraint'     => 8,
				'unsigned'       => true,
			],
			 'created_at' => ['type' => 'DATETIME', 'null' => true],
        	 'updated_at' => ['type' => 'DATETIME', 'null' => true],
			 'deleted_at' => ['type' => 'DATETIME', 'null' => true],
		]);

		$this->forge->addKey('id', true);
		$this->forge->addForeignKey('product_id','products','id','CASCADE','CASCADE');
        $this->forge->addForeignKey('category_id','categories','id','CASCADE','CASCADE');
		$this->forge->createTable('product_categories');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('product_categories');
	}
}
