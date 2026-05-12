<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCustomerProfileFieldsToUsers extends Migration
{
    public function up()
    {
        $fields = [
            'address' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'phone',
            ],

            'city' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'address',
            ],

            'province' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'city',
            ],

            'postal_code' => [
                'type'       => 'char',
                'constraint' => 5,
                'null'       => true,
                'after'      => 'province',
            ],

            'avatar' => [ // menyimpan nama file avatar/profile picture
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'postal_code',
            ],
        ];

        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users', [
            'address',
            'city',
            'province',
            'postal_code',
            'avatar',
        ]);
    }
}
