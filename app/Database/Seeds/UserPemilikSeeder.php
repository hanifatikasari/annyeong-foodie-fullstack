<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserPemilikSeeder extends Seeder
{
    public function run()
    {
        // 1. Definisikan password.
        // IonAuth menggunakan Bcrypt, jadi kita hash dulu passwordnya.
        $password = password_hash('pemilik123', PASSWORD_BCRYPT);

        // 2. Data untuk tabel 'users'
        $userData = [
            'ip_address' => '127.0.0.1',
            'username'   => 'pemilik',
            'password'   => $password,
            'email'      => 'pemilik@annyeongfoodie.com',
            'active'     => date('Y-m-d H:i:s'),
            'first_name' => 'Pemilik',
            'last_name'  => 'User',
            'created_on' => time(),
        ];

        // Masukkan ke tabel users
        $this->db->table('users')->insert($userData);

        // Ambil ID user yang baru saja dibuat
        $userId = $this->db->insertID();

        // 3. Hubungkan ke group 'pemilik'
        $group = $this->db->table('groups')->where('name', 'pemilik')->get()->getRow();

        if ($group) {
            $userGroupData = [
                'user_id'  => $userId,
                'group_id' => $group->id,
            ];

            $this->db->table('users_groups')->insert($userGroupData);
        }
    }
}
