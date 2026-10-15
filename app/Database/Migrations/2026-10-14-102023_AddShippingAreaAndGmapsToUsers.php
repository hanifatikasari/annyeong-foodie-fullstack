<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddShippingAreaAndGmapsToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'shipping_area_id' => [
                'type'       => 'SMALLINT',
                'constraint' => 5,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'postal_code',
            ],
            'gmaps' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'shipping_area_id',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['shipping_area_id', 'gmaps_link']);
    }
}
