<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAttributesTable extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id' => [
				'type' => 'smallint',
				'constraint' => 5,
				'unsigned' => true,
				'auto_increment' => true,
			],
			'code' => [
				'type' => 'VARCHAR',
				'constraint' => 60,
			],
			'name' => [
				'type' => 'VARCHAR',
				'constraint' => 50,
			],
			'type' => [
				'type'       => 'varchar',
				'constraint' => 20,
			],
			'validation' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => true,
			],
			'is_required' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'default' => false,
			],
			'is_unique' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'default' => false,
			],
			'is_filterable' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'default' => false,
			],
			'is_configurable' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'default' => false,
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
		$this->forge->addUniqueKey('code');
		$this->forge->createTable('attributes');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('attributes');
	}
}
