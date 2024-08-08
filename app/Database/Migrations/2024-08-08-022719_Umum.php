<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Umum extends Migration
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
            'nominal' => [
                'type'       => 'INTEGER',
                'default' => 0,
            ],
            'nia' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
            ],
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => '500',
            ],
            'bukti' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'validator' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'default' => null,
            ],
            'tanggal_validasi' => [
                'type' => 'DATETIME',
                'default' => null,
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
        $this->forge->createTable('umum');
    }

    public function down()
    {
        //
    }
}
