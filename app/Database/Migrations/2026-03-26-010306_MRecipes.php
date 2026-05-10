<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MRecipes extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'mediumint',
                'constraint'     => 8,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'product_id' => [ // Menghubungkan ke tabel products (Dimsum Mentai)
                'type'       => 'mediumint',
                'constraint' => 8,
                'unsigned'   => true,
            ],
            'bahan_baku_id' => [ // Menghubungkan ke m_bahan_baku (Ayam, tepung, dll)
                'type'       => 'MEDIUMINT',
                'constraint' => 8,
                'unsigned'   => true,
            ],
            'jumlah_kebutuhan' => [ 
                // Contoh: 999,999 
                'type'       => 'DECIMAL',
                'constraint' => '6,3', 
            ],
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
           	 'created_at' => ['type' => 'DATETIME', 'null' => true],
        	 'updated_at' => ['type' => 'DATETIME', 'null' => true],
			 'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        
        // Mencegah duplikasi bahan yang sama dalam satu resep produk
        $this->forge->addUniqueKey(['product_id', 'bahan_baku_id']);

        // Foreign Key agar data konsisten
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE'); //cascade brti jika data di master dihapus maka data di tabel lain ikut terhapus
        $this->forge->addForeignKey('bahan_baku_id', 'm_bahan_baku', 'id', 'RESTRICT', 'CASCADE'); 
        //Skenario: Kamu punya bahan baku "Ayam Fillet" (ID: 5) di tabel m_bahan_baku. Bahan ini sedang digunakan di resep "Dimsum Mentai", "Dimsum Ayam"
        //jika bahan di hapus maka resep dimsum akan kehilangan komponen bahan baku nya karena bahan nomor id 5 sudah dihapus
        //maka restrict akan memunculkan error di database untuk menolak penghapusan tersebut

        $this->forge->createTable('m_recipes');
    }

    public function down()
    {
        $this->forge->dropTable('m_recipes');
    }
}
