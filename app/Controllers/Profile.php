<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

use App\Models\ModelAnggota;

class Profile extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        $nia = '000'; //dari token
        $ma = new ModelAnggota();
        $profile = $ma->anggotaById($nia);
        $data = [
            'nia' => $profile['nia'],
            'nama' => strtoupper($profile['nama']),
            'wa' => $profile['wa'],
            'alamat' => $profile['alamat'],
            'kode_wilayah' => $profile['kode_wilayah'],
            'nama_wilayah' => $profile['nama_wilayah'],
            'level' => $profile['level'],
            'foto' => base_url() . 'render/image/' . $profile['foto'],
            'email' => $profile['email'],
            'aktif' => $profile['aktif']
        ];
    }

    public function edit($nia)
    {
        $ma = new ModelAnggota();
        $mw = new ModelWilayah();
        $anggota = $ma->select('*')->where('nia', $nia)->first();
        if (!$anggota) return $this->fail('NIA ' . $nia . ' belum terdaftar.', 400);

        $data = [
            'nama' => $anggota['nama'],
            'alamat' => $anggota['alamat'],
            'wa' => $anggota['wa'],
            'email' => $anggota['email'],
        ];

        return $this->respond($data);
    }

    public function udpate($nia)
    {
        helper(['form']);
        $rules = [
            'nama'          => [
                'rules' => 'required|min_length[4]|max_length[100]',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                    'min_length' => '{field} tidak boleh kurang dari 4 karakter.',
                    'max_length' => '{field} tidak boleh lebih dari 100 karakter.',
                    'is_unique' => '{field} sudah terdaftar.'
                ],
            ],
            'email'         => [
                'rules' => 'required|min_length[4]|max_length[100]|valid_email|is_unique[anggota.email]',
                'errors' => [
                    'required' => '{field} tidak boleh kosong',
                    'min_length' => '{field} tidak boleh kurang dari 4 karakter.',
                    'max_length' => '{field} tidak boleh lebih dari 100 karakter.',
                    'is_unique' => '{field} sudah terdaftar.',
                    'valid_email' => '{field} yang anda masukkan tidak valid.'
                ]
            ],
            'alamat'      => [
                'rules' => 'required|min_length[4]|max_length[300]',
                'errors' => [
                    'required' => '{field} tidak boleh kosong',
                    'min_length' => '{field} tidak boleh kurang dari 4 karakter.',
                    'max_length' => '{field} tidak boleh lebih dari 300 karakter.',
                ]
            ],

            'wa'  => [
                'rules' => 'required|min_length[10]|max_length[16]',
                'errors' => [
                    'required' => '{field} tidak boleh kosong',
                    'min_length' => '{field} tidak boleh kurang dari 10 karakter.',
                    'max_length' => '{field} tidak boleh lebih dari 16 karakter.',
                ]
            ],
        ];

        if (!$this->validate($rules)) return $this->fail($this->validator->getErrors());
        $ma = new ModelAnggota();
        $mw = new ModelWilayah();

        $json = $this->request->getJSON();
        $nia = $json->nia;
        $email = $json->email;

        $cek = $ma->select('*')->where('nia', $nia)->first();
        if (!$cek) return $this->fail('Nomor induk anggota ' . $nia . ' belum terdaftar.', 400);

        $wilayah = $mw->select('*')->where(['kode' => $cek['wilayah'], 'aktif' => '1'])->first();
        if (!$wilayah) return $this->fail('Kode ' . $wilayah . ' yang anda masukkan tidak terdaftar.', 400);
        $data = [
            'nama' => $json->nama,
            'alamat' => $json->alamat,
            'wa' => $json->wa,
            'wilayah' => $wilayah,
            'email' => $email,
            'level' => $json->level,
        ];

        try {
            $ma->set($data);
            $ma->where('nia', $nia);
            $ma->update();
            return $this->respond(['pesan' => 'Data anggota NIA ' . $nia . ' berhasil diperbaharui.']);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage(), $th->getCode());
        }
    }


    public function editPassword()
    {
        $data = [
            'password_lama' => null,
            'password_baru' => null,
            'konfirmasi_password' => null,
        ];

        return $this->respond($data);
    }

    public function ubahPassword($nia)
    {
        helper(['form']);
        $rules = [
            'password_lama'          => [
                'rules' => 'required|min_length[4]|max_length[20]',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                    'min_length' => '{field} tidak boleh kurang dari 4 karakter.',
                    'max_length' => '{field} tidak boleh lebih dari 20 karakter.',
                ],
            ],
            'password_baru'          => [
                'rules' => 'required|min_length[4]|max_length[20]',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                    'min_length' => '{field} tidak boleh kurang dari 4 karakter.',
                    'max_length' => '{field} tidak boleh lebih dari 20 karakter.',
                ],
            ],
            'konfirmasi_password'          => [
                'rules' => 'required|matches[password_baru]',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                    'matches' => '{field} harus sama dengan kolom password.',
                ],
            ],
        ];

        if (!$this->validate($rules)) return $this->fail($this->validator->getErrors());
        $ma = new ModelAnggota();

        $json = $this->request->getJSON();
        $nia_from_token = '000';
        $password_lama = $json->password_lama;
        $password_baru = $json->password_baru;
        $konfirmasi_password = $json->konfirmasi_password;

        $cek = $ma->select('*')->where(['nia' => $nia])->first();
        if ($nia_from_token != $nia) return $this->fail('Anda tidak berhak mengganti password anggota lain.', 400);

        if (!password_verify($password_lama, $cek['password'])) return $this->fail('Password lama yang Anda masukkan salah.', 400);
        try {
            $ma->set(['password' => password_hash($password_baru, PASSWORD_DEFAULT)]);
            $ma->where('nia', $nia);
            $ma->update();
            return $this->respond(['pesan' => 'Password Anda telah berhasil diganti.']);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage(), $th->getCode());
        }
    }
}
