<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TStokMasuk extends Migration
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
            'bahan_baku_id' => [ // Mengambil 'id' dari m_bahan_baku
                'type'       => 'mediumint',
                'constraint' => 8,
                'unsigned'   => true,
            ],
            'qty' => [
                'type'       => 'mediumint', //16jt
                'constraint' => 8,
                'unsigned'   => true,
            ],
            'isi_per_satuan' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2', // Pakai decimal agar aman untuk angka besar/koma
                'default'    => 1,      // Default 1, artinya eceran (1 x Qty)
                'null'       => false,
            ],
            'harga_satuan' => [
                'type'       => 'mediumint',
                'constraint' => 8,
                'unsigned'   => true,
            ],
            'total_harga' => [ // qty * harga_satuan (Dihitung di controller)
                'type'       => 'int',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'nama_supplier' => [ // Tidak perlu tabel tambahan, cukup ketik manual
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'tanggal_masuk' => [
                'type' => 'DATE',
            ],
            'keterangan' => [ // Untuk catatan tambahan
                'type' => 'varchar',
                'constraint' => 150,
                'null' => true,
            ],
           	'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        // Relasi: Jika bahan baku dihapus (Restrict), stok masuk tidak bisa yatim piatu
        $this->forge->addForeignKey('bahan_baku_id', 'm_bahan_baku', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('t_stok_masuk');
        }

    public function down()
    {
        $this->forge->dropTable('t_stok_masuk');
    }
}
