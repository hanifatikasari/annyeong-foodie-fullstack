<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserAdminSeeder extends Seeder
{
    public function run()
    {
        // 1. Definisikan password. 
        // IonAuth menggunakan Bcrypt, jadi kita hash dulu passwordnya.
        // Silakan ganti 'admin123' dengan password keinginanmu.
        $password = password_hash('admin123', PASSWORD_BCRYPT);

        // 2. Data untuk tabel 'users'
        $userData = [
            'ip_address' => '127.0.0.1',
            'username'   => 'admin',
            'password'   => $password,
            'email'      => 'admin@annyeongfoodie.com',
            'active'     => date('Y-m-d H:i:s'),
            'first_name' => 'Super',
            'last_name'  => 'Admin',
            'created_on' => time(), // Menggunakan timestamp unix sesuai standar IonAuth
        ];

        // Masukkan ke tabel users
        $this->db->table('users')->insert($userData);

        // Ambil ID user yang baru saja dibuat
        $userId = $this->db->insertID();

        // 3. Hubungkan ke group 'admin'
        // Kita cari dulu ID group yang namanya 'admin' dari tabel groups
        $group = $this->db->table('groups')->where('name', 'admin')->get()->getRow();

        if ($group) {
            $userGroupData = [
                'user_id'  => $userId,
                'group_id' => $group->id,
            ];

            $this->db->table('users_groups')->insert($userGroupData);
        }
    }
}
