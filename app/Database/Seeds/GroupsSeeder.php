<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class GroupsSeeder extends Seeder
{
    public function run()
    {
        // Kosongkan tabel dan reset ID ke angka 1 sebelum insert
        $this->db->query('SET FOREIGN_KEY_CHECKS=0;'); // Matikan cek relasi sebentar
        $this->db->table('groups')->truncate();
        $this->db->query('SET FOREIGN_KEY_CHECKS=1;'); // Nyalakan kembali
        
        $data = [
            [
                'name'        => 'admin',
                'description' => 'Super Administrator (Full Access)',
            ],
            [
                'name'        => 'pelanggan',
                'description' => 'User E-Business / Customer',
            ],
            [
                'name'        => 'produksi',
                'description' => 'Manajemen Dapur & Pengolahan Bahan',
            ],
            [
                'name'        => 'penjualan',
                'description' => 'Staf Kasir & Pelayanan Pelanggan',
            ],
            [
                'name'        => 'gudang',
                'description' => 'Manajemen Stok & Inventaris',
            ],
            [
                'name'        => 'pemilik',
                'description' => 'Owner / Monitoring Bisnis',
            ],
        ];

        // Masukkan data ke tabel groups
        $this->db->table('groups')->insertBatch($data);
    
    }
}
