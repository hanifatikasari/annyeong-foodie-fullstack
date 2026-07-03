<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserProduksiSeeder extends Seeder
{
    public function run()
    {
        // 1. Definisikan password.
        // IonAuth menggunakan Bcrypt, jadi kita hash dulu passwordnya.
        $password = password_hash('produksi123', PASSWORD_BCRYPT);

        // 2. Data untuk tabel 'users'
        $userData = [
            'ip_address' => '127.0.0.1',
            'username'   => 'produksi',
            'password'   => $password,
            'email'      => 'produksi@annyeongfoodie.com',
            'active'     => date('Y-m-d H:i:s'),
            'first_name' => 'Produksi',
            'last_name'  => 'User',
            'created_on' => time(),
        ];

        // Masukkan ke tabel users
        $this->db->table('users')->insert($userData);

        // Ambil ID user yang baru saja dibuat
        $userId = $this->db->insertID();

        // 3. Hubungkan ke group 'produksi'
        $group = $this->db->table('groups')->where('name', 'produksi')->get()->getRow();

        if ($group) {
            $userGroupData = [
                'user_id'  => $userId,
                'group_id' => $group->id,
            ];

            $this->db->table('users_groups')->insert($userGroupData);
        }
    }
}
