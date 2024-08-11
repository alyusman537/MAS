<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class User extends Seeder
{
    public function run()
    {
        $data = [
            'nia' => '0537',
            'nama' => 'Aly Usman',
            'wa' => '085850468588',
            'alamat' => 'Pasuruan Jawa Timur',
            'wilayah' => 'PUSAT',
            'level' => 'admin',
            'password' => password_hash('0537', PASSWORD_DEFAULT),
            'foto' => null,
            'email' => 'liusmanx@gmail.com',
            'aktif' => 'aktif'
        ];

        // Simple Queries
        // $this->db->query('INSERT INTO users (username, email) VALUES(:username:, :email:)', $data);

        // Using Query Builder
        $this->db->table('anggota')->insert($data);
    }
}
