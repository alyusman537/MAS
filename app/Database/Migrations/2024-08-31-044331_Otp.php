<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Otp extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'tanggal' => [
                'type' => 'DATE',
                'default' => null,
            ],
            'nia' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'otp' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'token' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'default' => null,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'default' => null,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('otp');
    }

    public function down()
    {
        //
    }
}
