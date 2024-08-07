<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Infaq extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'tanggal' => [
                'type'       => 'DATE',
            ],
            'kode' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'acara' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            
            'tanggal_acara' => [
                'type'       => 'DATE',
            ],
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => '500',
            ],
            'nominal' => [
                'type'       => 'INTEGER',
                'default' => 0,
            ],
            'rutin' => [
                'type' => 'BOOLEAN',
                'default' => true,
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
        $this->forge->createTable('infaq');
    }

    public function down()
    {
        //
    }
}
