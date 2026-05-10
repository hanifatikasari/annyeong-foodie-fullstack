<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAttrributeOptionsTable extends Migration
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
			'attribute_id' => [
				'type' => 'smallint',
				'constraint' => 5,
				'unsigned' => true,
			],
			'name' => [
				'type' => 'VARCHAR',
				'constraint' => 50,
			],
			'slug' => [
				'type' => 'VARCHAR',
				'constraint' => 60,
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
		$this->forge->addKey('slug');
		$this->forge->addForeignKey('attribute_id','attributes','id','CASCADE','CASCADE');
		$this->forge->createTable('attribute_options');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		//
		$this->forge->dropTable('attribute_options');
	}
}
