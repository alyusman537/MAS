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
                'unique' => true,
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'alamat' => [
                'type'       => 'VARCHAR',
                'constraint' => '300',
            ],
            'wilayah' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'wa' => [
                'type'       => 'VARCHAR',
                'constraint' => '15',
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'unique' => true,
            ],
            'foto' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'default' => null
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
                'type' => 'ENUM',
                'constraint' => ['baru', 'aktif', 'nonaktif'],
                'default' => 'baru',
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
