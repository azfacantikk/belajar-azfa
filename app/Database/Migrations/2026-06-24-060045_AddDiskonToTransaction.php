<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDiskonToTransaction extends Migration
{
    public function up()
    {
        $fields = [
            'diskon' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
        ];
        
        $this->forge->addColumn('transaction', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('transaction', 'diskon');
    }
}

