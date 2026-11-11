<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePromoTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_promo' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'kode_promo' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'tipe_promo' => [
                'type'       => 'ENUM',
                'constraint' => ['automatic', 'voucher'],
                'default'    => 'automatic',
            ],
            'jenis_diskon' => [
                'type'       => 'ENUM',
                'constraint' => ['fixed', 'percentage'],
                'default'    => 'fixed',
            ],
            'nilai_diskon' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'default'    => 0,
            ],
            'min_subtotal' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'default'    => 0,
            ],
            'max_diskon' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'starts_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'ends_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'usage_limit' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'usage_count' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'default'    => 0,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('tipe_promo');
        $this->forge->addKey('is_active');
        $this->forge->createTable('m_promo');

        // Promo otomatis awal Batterbit
        $now = date('Y-m-d H:i:s');

        $this->db->table('m_promo')->insertBatch([
            [
                'nama_promo' => 'Hemat 20 Ribu',
                'tipe_promo' => 'automatic',
                'jenis_diskon' => 'fixed',
                'nilai_diskon' => 1000,
                'min_subtotal' => 20000,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_promo' => 'Hemat 40 Ribu',
                'tipe_promo' => 'automatic',
                'jenis_diskon' => 'fixed',
                'nilai_diskon' => 2500,
                'min_subtotal' => 40000,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_promo' => 'Hemat 60 Ribu',
                'tipe_promo' => 'automatic',
                'jenis_diskon' => 'fixed',
                'nilai_diskon' => 4000,
                'min_subtotal' => 60000,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_promo' => 'Hemat 100 Ribu',
                'tipe_promo' => 'automatic',
                'jenis_diskon' => 'fixed',
                'nilai_diskon' => 7000,
                'min_subtotal' => 100000,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Referensi promo pada transaksi penjualan
        $this->forge->addColumn('t_penjualan', [
            'promo_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'after'    => 'diskon',
            ],
            'kode_promo' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'promo_id',
            ],
        ]);

        $this->db->query(
            'ALTER TABLE t_penjualan
             ADD CONSTRAINT fk_penjualan_promo
             FOREIGN KEY (promo_id) REFERENCES m_promo(id)
             ON DELETE SET NULL ON UPDATE CASCADE'
        );
    }

    public function down()
    {
        $this->db->query(
            'ALTER TABLE t_penjualan
             DROP FOREIGN KEY fk_penjualan_promo'
        );

        $this->forge->dropColumn('t_penjualan', ['promo_id', 'kode_promo']);
        $this->forge->dropTable('m_promo');
    }
}

