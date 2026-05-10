<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TPenjualan extends Migration
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
            'invoice_no' => [ // nomor faktur. Contoh: AES-20260404-001
                'type'       => 'CHAR',
                'constraint' => 16,
                'unique'     => true,
            ],
            'total_harga' => [
                'type'       => 'int',
                'constraint' => '11',
                'unsigned'   => true,
                'default'    => 0,
            ],
            'diskon' => [ // Potongan harga (jika ada promo)
                'type'       => 'mediumint',
                'constraint' => '8',
                'default'    => 0,
            ],
            'total_bayar' => [ // Harga akhir (total_harga - diskon)
                'type'       => 'int',
                'constraint' => '11',
                'unsigned'   => true,
                'default'    => 0,
            ],
            'pembayaran' => [
                'type'       => 'ENUM',
                'constraint' => ['Cash', 'QRIS', 'Transfer'],
                'default'    => 'Cash',
            ],
            'uang_diterima' => [ // Uang yang dikasih pelanggan (untuk hitung kembalian)
                'type'       => 'int',
                'constraint' => '11',
                'unsigned'   => true,
                'default'    => 0,
            ],
            'kasir_id' => [ // Siapa yang jaga kasir?
                'type'       => 'int',
                'constraint' => 11,
                'unsigned'   => true,
            ],
			 'created_at' => ['type' => 'DATETIME', 'null' => true],
        	 'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('kasir_id', 'users', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('t_penjualan');
        }

    public function down()
    {
        $this->forge->dropTable('t_penjualan');
    }
}
