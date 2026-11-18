<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateActivityLogsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'comment'  => 'ID pengguna yang melakukan aktivitas (nullable jika guest/system)',
            ],
            'username' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Snapshot username pelaku saat aktivitas terjadi',
            ],
            'role' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'Role pengguna saat melakukan aktivitas',
            ],
            'action' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'comment'    => 'Jenis tindakan: create, update, delete, login, logout, dll',
            ],
            'module' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'comment'    => 'Modul tempat aktivitas dilakukan',
            ],
            'description' => [
                'type'       => 'TEXT',
                'null'       => true,
                'comment'    => 'Deskripsi aktivitas yang mudah dipahami',
            ],
            'old_values' => [
                'type'    => 'JSON',
                'null'    => true,
                'comment' => 'Data sebelum perubahan dalam format JSON',
            ],
            'new_values' => [
                'type'    => 'JSON',
                'null'    => true,
                'comment' => 'Data sesudah perubahan dalam format JSON',
            ],
            'reference_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'Jenis entitas terkait, misal: product, order, bahan_baku',
            ],
            'reference_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
                'comment'  => 'ID entitas terkait',
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 45,
                'null'       => true,
                'comment'    => 'IP address pengguna (mendukung IPv6)',
            ],
            'user_agent' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => 'Informasi browser/perangkat pengguna',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('module');
        $this->forge->addKey('action');
        $this->forge->addKey('created_at');

        $this->forge->createTable('activity_logs', true);
    }

    public function down()
    {
        $this->forge->dropTable('activity_logs', true);
    }
}
