<?php namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'sku'          => 'AN-KIMBAB-01',
                'type'         => 'simple',
                'name'         => 'Kimbab Chicken Katsu',
                'slug'         => 'kimbab-chicken-katsu',
                'price'        => 22000,
                'published_at' => date('Y-m-d H:i:s'),
                'created_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'sku'          => 'Dimsum Kuah',
                'type'         => 'dimsum-kuah',
                'name'         => 'Spicy Tteokbokki',
                'slug'         => 'spicy-tteokbokki',
                'price'        => 30000,
                'published_at' => date('Y-m-d H:i:s'),
                'created_at'   => date('Y-m-d H:i:s'),
            ],
        ];

        // Masukkan ke tabel products
        $this->db->table('products')->insertBatch($data);
    }
}