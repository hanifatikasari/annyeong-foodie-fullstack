<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MBahanBaku extends Migration
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
            'kode_bahan' => [ // Ini yang formatnya AYM-001, TPG-001, BUM-001 dst pakai prefix di categories
                'type'       => 'CHAR',
                'constraint' => 7,
                'unique'     => true, // Supaya tidak ada kode ganda
            ],
            'category_id' => [
                'type'           => 'MEDIUMINT',
                'constraint'     => 8, //ambil dari categories misal id=10 (tepung) id=11 (daging) 
                'unsigned'       => true,
                'null'           => true, // Set null jika tidak wajib diisi
            ],
            'nama_bahan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'satuan' => [
                'type'       => 'VARCHAR',
                'constraint' => 5, // Kg, Gram, Pcs, pack
            ],
            'harga_beli_satuan' => [
                'type'       => 'mediumint',
                'constraint' => '8', //menampung sampai 16jt
                'default'    => 0,
            ],
                'stok_sekarang' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '8,2', //999 ribuan
                    'default'    => 0,
            ],
            'stok_minimal' => [
                'type'       => 'DECIMAL',
                'constraint' => '6,2', //9 ribuan
                'default'    => 0,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],    
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('category_id', 'categories', 'id', 'SET NULL', 'CASCADE');
        //............addForeignKey(kolom_ini, tabel_tujuan, kolom_tujuan, ON_DELETE, ON_UPDATE).
        $this->forge->createTable('m_bahan_baku');
    }

    public function down()
    {
        $this->forge->dropTable('m_bahan_baku');
    }
}
