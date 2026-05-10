<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TProduksi extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT', //Jika sehari ada 10 kali produksi, setahun sudah 3.650 baris. Dalam beberapa tahun, angka ini bisa melewati batas MEDIUMINT.
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kode_produksi' => [ 
                'type'       => 'CHAR', // misal: PRD-20260409-001 --->tgl Standard Internasional (ISO 8601)
                'constraint' => 16,
                'unique'     => true, // Harus unik, tidak boleh sama antar transaksi, buat otomatis di controller
            ],
            'product_id' => [ // Menu apa yang dibuat?
                'type'       => 'mediumint',
                'constraint' => 8,
                'unsigned'   => true,
            ],
            'user_id' => [ // Siapa yang memproduksi?
                'type'       => 'int',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true, //set null jiks user dihapus
            ],
            'qty_hasil' => [ // Berapa porsi yang jadi?
                'type'       => 'smallint', //hingga 65.535
                'constraint' => 5,
                'default'    => 0,
            ],
            'tanggal_produksi' => [
                'type' => 'DATE',
            ],
            'status_qc' => [ // Quality Control: 'Lolos' atau 'Gagal/Reject'
                'type'       => 'ENUM',
                'constraint' => ['Lolos', 'Reject'],
                'default'    => 'Lolos',
            ],
            'catatan' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => true,
            ],
             'created_at' => ['type' => 'DATETIME', 'null' => true],
        	 'updated_at' => ['type' => 'DATETIME', 'null' => true],
			 'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        // Relasi ke tabel Master
        $this->forge->addForeignKey('product_id', 'products', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('t_produksi');
    }

    public function down()
    {
        $this->forge->dropTable('t_produksi');
    }
}
