<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserPenjualanSeeder extends Seeder
{
    public function run()
    {
        // 1. Definisikan password.
        // IonAuth menggunakan Bcrypt, jadi kita hash dulu passwordnya.
        $password = password_hash('penjualan123', PASSWORD_BCRYPT);

        // 2. Data untuk tabel 'users'
        $userData = [
            'ip_address' => '127.0.0.1',
            'username'   => 'penjualan',
            'password'   => $password,
            'email'      => 'penjualan@annyeongfoodie.com',
            'active'     => date('Y-m-d H:i:s'),
            'first_name' => 'Penjualan',
            'last_name'  => 'User',
            'created_on' => time(),
        ];

        // Masukkan ke tabel users
        $this->db->table('users')->insert($userData);

        // Ambil ID user yang baru saja dibuat
        $userId = $this->db->insertID();

        // 3. Hubungkan ke group 'penjualan'
        $group = $this->db->table('groups')->where('name', 'penjualan')->get()->getRow();

        if ($group) {
            $userGroupData = [
                'user_id'  => $userId,
                'group_id' => $group->id,
            ];

            $this->db->table('users_groups')->insert($userGroupData);
        }
    }
}
