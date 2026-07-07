<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCheckoutFieldsToTransaction extends Migration
{
    public function up()
    {
        $fields = [
            'biaya_admin' => [
                'type'       => 'DOUBLE',
                'null'       => true,
                'after'      => 'ongkir', // menyesuaikan field ongkir yang sudah ada
            ],
            'kupon_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'biaya_admin',
            ],
            'diskon_kupon' => [
                'type'       => 'DOUBLE',
                'null'       => true,
                'after'      => 'kupon_code',
            ],
            'cashback' => [
                'type'       => 'DOUBLE',
                'null'       => true,
                'after'      => 'diskon_kupon',
            ],
        ];

        $this->forge->addColumn('transaction', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('transaction', ['biaya_admin', 'kupon_code', 'diskon_kupon', 'cashback']);
    }
}