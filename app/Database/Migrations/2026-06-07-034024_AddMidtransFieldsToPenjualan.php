<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMidtransToTPenjualan extends Migration
{
    public function up()
    {
        // ============================================================
        // Tambah kolom Midtrans
        // ============================================================
        $this->forge->addColumn('t_penjualan', [

            'snap_token' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'payment_proof',
            ],

            'midtrans_transaction_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'snap_token',
            ],

            'midtrans_payment_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
                'after'      => 'midtrans_transaction_id',
            ],

            'midtrans_status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'midtrans_payment_type',
            ],

        ]);

        // ============================================================
        // Tambah opsi Midtrans ke enum pembayaran
        // ============================================================
        $this->db->query("
            ALTER TABLE t_penjualan
            MODIFY COLUMN pembayaran
            ENUM('Cash', 'QRIS', 'Transfer', 'Midtrans')
            NOT NULL DEFAULT 'Cash'
        ");
    }

    public function down()
    {
        // ============================================================
        // Hapus kolom Midtrans
        // ============================================================
        $this->forge->dropColumn('t_penjualan', [
            'snap_token',
            'midtrans_transaction_id',
            'midtrans_payment_type',
            'midtrans_status',
        ]);

        // ============================================================
        // Kembalikan enum pembayaran
        // ============================================================
        $this->db->query("
            ALTER TABLE t_penjualan
            MODIFY COLUMN pembayaran
            ENUM('Cash', 'QRIS', 'Transfer')
            NOT NULL DEFAULT 'Cash'
        ");
    }
}