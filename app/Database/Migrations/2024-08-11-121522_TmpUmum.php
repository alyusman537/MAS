<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TmpUmum extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'nia' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
            ],
            'nominal' => [
                'type'       => 'INTEGER',
                'default' => 0,
            ],
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => '500',
                'default' => null,
            ],
            'bukti' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'default' => null,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tmp_umum');
    }

    public function down()
    {
        //
    }
}
