<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Tambah kolom moderasi dan relasi pesanan ke product_reviews.
 *
 * Perubahan:
 *  - is_visible  TINYINT(1) DEFAULT 1 → moderasi admin (1 = tampil, 0 = hidden)
 *  - penjualan_detail_id INT UNSIGNED NULL → relasi opsional ke t_penjualan_detail
 *
 * Kolom penjualan_detail_id bersifat nullable agar review lama
 * (yang dibuat sebelum fitur ini) tetap valid.
 *
 * UNIQUE KEY (product_id, user_id) dipertahankan: satu user satu review per produk.
 */
class AddModerationToProductReviews extends Migration
{
    public function up()
    {
        $this->forge->addColumn('product_reviews', [
            'is_visible' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'default'    => 1,
                'after'      => 'ulasan',
            ],
            'penjualan_detail_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
                'after'      => 'is_visible',
            ],
        ]);

        // Index for faster lookups
        $this->forge->addKey('penjualan_detail_id', false, false, 'idx_review_detail');
        // Note: We don't add a FK to t_penjualan_detail because old reviews
        // won't have this value and we want them to stay valid.
    }

    public function down()
    {
        $this->forge->dropColumn('product_reviews', ['is_visible', 'penjualan_detail_id']);
    }
}
