<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Anggota extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nia' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'wa' => [
                'type'       => 'VARCHAR',
                'constraint' => '15',
            ],
            'alamat' => [
                'type'       => 'VARCHAR',
                'constraint' => '300',
            ],
            'nia' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'wilayah' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'level' => [
                'type'       => 'ENUM',
                'constraint' => ['admin', 'user'],
                'default' => 'user',
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'aktif' => [
                'type' => 'BOOLEAN',
                'default' => true,
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
        $this->forge->createTable('anggota');
    }

    public function down()
    {
        //
    }
}
