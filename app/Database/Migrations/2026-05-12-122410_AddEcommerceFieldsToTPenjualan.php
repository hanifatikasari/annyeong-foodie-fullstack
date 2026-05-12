<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEcommerceFieldsToTPenjualan extends Migration
{
    public function up()
    {
        $this->forge->addColumn('t_penjualan', [
            'customer_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'order_status' => [
                'type'       => 'ENUM',
                'constraint' => [
                    'pending',
                    'diproses',
                    'dikirim',
                    'selesai',
                    'dibatalkan',
                ],
                'default' => 'pending',
                'after'   => 'customer_id',
            ],

            'payment_status' => [
                'type'       => 'ENUM',
                'constraint' => [
                    'menunggu_pembayaran',
                    'menunggu_verifikasi',
                    'lunas',
                ],
                'default' => 'menunggu_pembayaran',
                'after'   => 'order_status',
            ],

            'payment_proof' => [ //menyimpan nama file bukti pembayaran (jika metode bukan Cash)
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'payment_status',
            ],

            'shipping_address' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'payment_proof',
            ],

            'catatan_customer' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'shipping_address',
            ],

            'verified_at' => [
                'type'  => 'DATETIME',
                'null'  => true,
                'after' => 'catatan_customer',
            ],

            'verified_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'verified_at',
            ],
        ]);

        // Foreign key customer_id -> users.id
         $this->db->query("
            ALTER TABLE t_penjualan
            ADD CONSTRAINT fk_t_penjualan_customer
            FOREIGN KEY (customer_id)
            REFERENCES users(id)
            ON DELETE SET NULL
            ON UPDATE CASCADE
        ");
        
         // Foreign key verified_by -> users.id
        $this->db->query("
            ALTER TABLE t_penjualan
            ADD CONSTRAINT fk_t_penjualan_verified_by
            FOREIGN KEY (verified_by)
            REFERENCES users(id)
            ON DELETE SET NULL
            ON UPDATE CASCADE
        ");
    }

    public function down()
    {
        // Hapus foreign key terlebih dahulu
         $this->db->query("
            ALTER TABLE t_penjualan
            DROP FOREIGN KEY fk_t_penjualan_customer
        ");

        $this->db->query("
            ALTER TABLE t_penjualan
            DROP FOREIGN KEY fk_t_penjualan_verified_by
        ");

        $this->forge->dropColumn('t_penjualan', [
            'customer_id',
            'order_status',
            'payment_status',
            'payment_proof',
            'shipping_address',
            'catatan_customer',
            'verified_at',
            'verified_by',
        ]);
    
    }
}
