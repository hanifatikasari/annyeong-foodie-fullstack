<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
         $this->call('AuthSeeder');
         $this->call('GroupsSeeder');
         $this->call('ProductSeeder');
         $this->call('UserAdminSeeder');
         $this->call('UserGudangSeeder');
        
        // Tambahkan seeder lainnya di sini
    }
}