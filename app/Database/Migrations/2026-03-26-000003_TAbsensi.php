<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TAbsensi extends Migration
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
        'user_id' => [ // Relasi ke tabel users GieArt87
            'type'       => 'int',
            'constraint' => 11,
            'unsigned'   => true,
        ],
        'jam_masuk' => [
            'type' => 'DATETIME',
            'null' => true,
        ],
        'jam_keluar' => [
            'type' => 'DATETIME',
            'null' => true,
        ],
        'total_jam' => [
            'type'       => 'DECIMAL',
            'constraint' => '4,2',
            'null'       => true,
         ],
        'keterangan' => [ // Misal: "Izin", "Sakit", atau "Lembur"
            'type'       => 'VARCHAR',
            'constraint' => 20,
            'null'       => true,
        ],
        
        'created_at' => ['type' => 'DATETIME', 'null' => true],
        'updated_at' => ['type' => 'DATETIME', 'null' => true],
    ]);

    $this->forge->addKey('id', true);
    // Tambahkan Foreign Key ke tabel users
    $this->forge->addForeignKey('user_id', 'users', 'id', 'RESTRICT', 'CASCADE');
    $this->forge->createTable('t_absensi');
    }

    public function down()
    {
        $this->forge->dropTable('t_absensi');
    }
}
