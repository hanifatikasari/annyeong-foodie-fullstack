<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductInventoriesTable extends Migration
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
			'product_id' => [
				'type'           => 'mediumint',
				'constraint'     => 8,
				'unsigned'       => true,
			],
			'qty' => [ // Saldo stok saat ini
				'type' => 'mediumint',
				'constraint' => 8,
				'unsigned' => true,
			],
			'low_stock_threshold' => [ // Batas stok menipis (biar dapet alert)
				'type'       => 'smallint', //65rb
				'constraint' => 5,
				'unsigned'   => true,
				'default'    => 10,
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
		$this->forge->addForeignKey('product_id','products','id');
		$this->forge->createTable('product_inventories');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('product_inventories');
	}
}
