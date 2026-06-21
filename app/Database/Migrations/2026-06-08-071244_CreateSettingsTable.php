<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSettingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'smallint',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'setting_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'unique'     => true,
            ],
            'setting_value' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'setting_group' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'general',
            ],
            'label' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('settings');

        // ============================================================
        // Seed default payment settings
        // ============================================================
        $this->db->table('settings')->insertBatch([
            [
                'setting_key'   => 'payment_midtrans_enabled',
                'setting_value' => '1',
                'setting_group' => 'payment',
                'label'         => 'Aktifkan Pembayaran Midtrans',
            ],
            [
                'setting_key'   => 'payment_transfer_enabled',
                'setting_value' => '0',
                'setting_group' => 'payment',
                'label'         => 'Aktifkan Transfer Bank Manual',
            ],
            [
                'setting_key'   => 'payment_qris_enabled',
                'setting_value' => '0',
                'setting_group' => 'payment',
                'label'         => 'Aktifkan QRIS Manual (Upload Bukti)',
            ],
            [
                'setting_key'   => 'manual_bank_name',
                'setting_value' => 'BCA',
                'setting_group' => 'payment',
                'label'         => 'Nama Bank Transfer',
            ],
            [
                'setting_key'   => 'manual_account_number',
                'setting_value' => '1234567890',
                'setting_group' => 'payment',
                'label'         => 'Nomor Rekening',
            ],
            [
                'setting_key'   => 'manual_account_name',
                'setting_value' => 'Annyeong Foodie',
                'setting_group' => 'payment',
                'label'         => 'Nama Pemilik Rekening',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('settings');
    }
}