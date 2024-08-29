<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Mutasi extends Migration
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
            'nomor_mutasi' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
            ],
            'jenis' => [
                'type'       => 'ENUM',
                'constraint' => ['D', 'K'],
            ],
            'nominal' => [
                'type'       => 'INTEGER',
                'default' => 0,
            ],
            'admin' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => '300',
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
        $this->forge->createTable('mutasi');
    }

    public function down()
    {
        //
    }
}
