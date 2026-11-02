<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Menambah users.google_id (Google `sub`) untuk Login with Google.
 * - NULL untuk semua akun lama (tidak memengaruhi login email/password)
 * - UNIQUE: satu akun Google hanya boleh terhubung ke satu akun Batterbit
 */
class AddGoogleIdToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'google_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
                'unique'     => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'google_id');
    }
}