<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductAttributeValuesTable extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id' => [
				'type' => 'INT', //ini tabel transaksi
				'constraint' => 11,
				'unsigned' => true,
				'auto_increment' => true,
			],
			'parent_product_id' => [
				'type' => 'mediumint',
				'constraint' => 8,
				'unsigned' => true,
				'null' => true,
			],
			'product_id' => [
				'type' => 'mediumint',
				'constraint' => 8,
				'unsigned' => true,
				'null' => true,
			],
			'attribute_id' => [
				'type'           => 'smallint',
				'constraint'     => 5,
				'unsigned'       => true,
				'null' => true,
			],
			'attribute_option_id' => [
				'type'           => 'smallint',
				'constraint'     => 5,
				'unsigned'       => true,
				'null' => true,
			],
			'text_value' => [
				'type' => 'TEXT',
				'null' => true,
			],
			'boolean_value' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'null' => true,

			],
			'integer_value' => [
				'type' => 'mediumint',
				'constraint' => 8,
				'unsigned' => true,
				'null' => true,
			],
			'float_value' => [
				'type' => 'DECIMAL',
				'constraint' => '6,3',
				'null' => true,
			],
			'datetime_value' => [
				'type' => 'DATETIME',
				'null' => true,
			],
			'date_value' => [
				'type' => 'DATE',
				'null' => true,
			],
			'json_value' => [
				'type' => 'TEXT',
				'null' => true,
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
		$this->forge->addForeignKey('attribute_option_id','attribute_options','id');
		$this->forge->addForeignKey('attribute_id','attributes','id');
		$this->forge->addForeignKey('product_id','products','id');
		$this->forge->addForeignKey('parent_product_id','products','id');
		$this->forge->createTable('product_attribute_values');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('product_attribute_values');
	}
}
