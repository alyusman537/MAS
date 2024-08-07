<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Pembayaran extends Migration
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
            'nomor_pembayaran' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
            ],
            'kode_infaq' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'nia' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'bayar' => [
                'type'       => 'INTEGER',
                'default' => 0,
            ],
            'tanggal_bayar' => [
                'type' => 'DATETIME',
                'default' => null,
            ],
            'bukti_bayar' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'default' => null,
            ],
            'validator' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
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
        $this->forge->createTable('pembayaran');
    }

    public function down()
    {
        //
    }
}
