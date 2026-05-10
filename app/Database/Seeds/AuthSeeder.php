<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AuthSeeder extends Seeder
{
    public function run()
    {
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0;');

        // TRUNCATE akan me-reset Auto Increment kembali ke 1
        // Kalau TRUNCATE gagal karena relasi, kita gunakan DELETE + ALTER
        $this->db->table('users_groups')->truncate();
        $this->db->query("ALTER TABLE users_groups AUTO_INCREMENT = 1");

        $this->db->table('users')->truncate();
        $this->db->query("ALTER TABLE users AUTO_INCREMENT = 1");

        $this->db->table('groups')->truncate();
        $this->db->query("ALTER TABLE groups AUTO_INCREMENT = 1");

        $this->db->query('SET FOREIGN_KEY_CHECKS = 1;');


        // 2. Isi User
        $userData = [
            'ip_address' => '127.0.0.1',
            'username'   => 'hani',
            'password'   => password_hash('password123', PASSWORD_BCRYPT),
            'email'      => 'hanifa@gmail.com',
            'active'     => date('Y-m-d H:i:s'),
            'created_on' => time(),
        ];
        $this->db->table('users')->insert($userData);

        // Ambil ID user yang baru dibuat (Pasti ID = 1)
        $userId = $this->db->insertID();

        // 3. Hubungkan User ke Group
        $userGroups = [
            ['user_id' => $userId, 'group_id' => 2], // pelanggan
        ];
        $this->db->table('users_groups')->insertBatch($userGroups);
    }
}