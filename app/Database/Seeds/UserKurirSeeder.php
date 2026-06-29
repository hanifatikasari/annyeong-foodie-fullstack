<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserKurirSeeder extends Seeder
{
    public function run()
    {
        // 1. Hash password untuk kurir
        $password = password_hash('kurir123', PASSWORD_BCRYPT);

        // 2. Data untuk tabel 'users'
        $userData = [
            'ip_address' => '127.0.0.1',
            'username'   => 'kurir',
            'password'   => $password,
            'email'      => 'kurir@annyeongfoodie.com',
            'first_name' => 'Joko',
            'last_name'  => 'Kendil',
            'active'     => 1,
        ];

        // Insert ke tabel users
        $this->db->table('users')->insert($userData);
        $userId = $this->db->insertID();

        // 3. Hubungkan user ke group Kurir (id = 7)
        $this->db->table('users_groups')->insert([
            'user_id'  => $userId,
            'group_id' => 7
        ]);
    }
}
