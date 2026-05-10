<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductsTable extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id' => [
				'type' => 'mediumint', //16jt
				'constraint' => 8,
				'unsigned' => true,
				'auto_increment' => true,
			],
			'parent_id' => [
				'type' => 'mediumint',
				'constraint' => 8,
				'unsigned' => true,
				'null' => true,
			],
			'user_id' => [
				'type'           => 'int',
				'constraint'     => 11,
				'unsigned'       => true,
				'null' => true,
			],
			
			'published_at' => [
				'type' => 'DATETIME',
				'null' => true, // Jika NULL berarti produk non-aktif/draft
		
			],
			'sku' => [ //kode untuk produk jadi -> MCH-0001
				'type' => 'CHAR',
				'constraint' => 8,
			],
			'type' => [
				'type' => 'CHAR',
				'constraint' => 8,
			],
			'name' => [
				'type' => 'VARCHAR',
				'constraint' => 100,
			],
			'slug' => [
				'type' => 'VARCHAR',
				'constraint' => 120,
			],
			'price' => [
				'type' => 'mediumint',
				'constraint' => '8', //maksimal 16jt
				'unsigned' => true,
			],
			'hpp_total' => [ // auto filled dari perhitungan resep (untuk hitung laba bersih)
                'type' => 'mediumint',
                'constraint' => '8',
                'default' => 0,
            ],
			'weight' => [
				'type' => 'DECIMAL',
				'constraint' => '5,2',
				'null' => true,
			],
			'length' => [
				'type' => 'DECIMAL',
				'constraint' => '5,2',
				'null' => true,
			],
			'width' => [
				'type' => 'DECIMAL',
				'constraint' => '5,2',
				'null' => true,
			],
			'height' => [
				'type' => 'DECIMAL',
				'constraint' => '5,2',
				'null' => true,
			],
			'short_description' => [
				'type' => 'varchar',
				'constraint' => 100,
				'null' => true,
			],
			'description' => [
				'type' => 'TEXT',
				'null' => true,
			],
			 'created_at' => ['type' => 'DATETIME', 'null' => true],
        	 'updated_at' => ['type' => 'DATETIME', 'null' => true],
			 'deleted_at' => ['type' => 'DATETIME', 'null' => true],
		]);

		$this->forge->addKey('id', true);
		$this->forge->addKey('slug');
		$this->forge->addKey('sku');
		$this->forge->addKey('parent_id');
		$this->forge->addForeignKey('user_id','users','id','SET NULL','CASCADE');
		$this->forge->createTable('products');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('products'); 
	}
}
