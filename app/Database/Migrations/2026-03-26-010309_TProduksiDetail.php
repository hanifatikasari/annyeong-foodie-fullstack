<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TProduksiDetail extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'produksi_id' => [ // Menunjuk ke t_produksi (Masak apa hari ini?)
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'bahan_baku_id' => [ // Bahan apa yang dipakai?
                'type'       => 'mediumint',
                'constraint' => 8,
                'unsigned'   => true,
            ],
            'qty_digunakan' => [ 
                // Hasil hitungan: (Jumlah di Resep) x (Total Produksi)
                // contoh resep butuh 0.250 kg ayam untuk 1 porsi, lalu hari ini produksi 100 porsi, maka qty_digunakan = 0.250 x 100 = 25 kg ||misal satuan gak mgkn >9k perhari
                //satuan ambil pakai join ke tabel m_bahan_baku untuk ambil satuan (kg, liter, dll)
                'type'       => 'DECIMAL',
                'constraint' => '7,3',
            ],
        	'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);

        // Foreign Keys
        $this->forge->addForeignKey('produksi_id', 't_produksi', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('bahan_baku_id', 'm_bahan_baku', 'id', 'RESTRICT', 'CASCADE');

        $this->forge->createTable('t_produksi_detail');
    }

    public function down()
    {
        $this->forge->dropTable('t_produksi_detail');
    }
}
