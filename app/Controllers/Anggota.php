<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

use App\Models\ModelAnggota;
use App\Models\ModelWilayah;

use App\Libraries\JwtDecode;

class Anggota extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        // $pass = password_hash('saya', PASSWORD_DEFAULT);
        // return $this->respond(['pass' => $pass]);
        $exp = getenv('JWT_EXP');
        $header = $this->request->getServer('HTTP_AUTHORIZATION');// header("Authorization");
        $key = new JwtDecode();
        $decoded = $key->decoder($header);
        $tangal = date('Y-m-d H:i:s', $decoded[0]->exp);
        return $this->respond(['key' => $decoded, 'tang' => $tangal]);
    }

    public function allAnggota()
    {
        $ma = new ModelAnggota;
        return $this->respond($ma->allAnggota());
    }

    public function anggotaById($id = null)
    {
        $ma = new ModelAnggota();
        $mw = new ModelWilayah();
        $anggota = $ma->select('id, nia, nama, alamat, wa, wilayah, level, aktif')->where('id', $id)->first();
        if (!$anggota) return $this->fail('ID anggota ' . $id . ' tidak ditemukan.', 400);
        $data = [
            'anggota' => $anggota,
            'wilayah' => $mw->select('keterangan')->where('kode', $anggota['wilayah'])->first()
        ];
        return $this->respond($data);
    }

    public function newAnggota()
    {
        $mw = new ModelWilayah();
        $wilayah = $mw->select('*')->where('aktif', '1')->findAll();
        $data = [
            'anggota' => [
                'nia' => null,
                'nama' => null,
                'alamat' => null,
                'wa' => null,
                'wilayah' => null
            ],
            'wilayah' => $wilayah
        ];
        return $this->respond($data);
    }
    public function addAnggota()
    {
        helper(['form']);
        $rules = [
            'nia'          => [
                'rules' => 'required|min_length[4]|max_length[20]|is_unique[anggota.nia]',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                    'min_length' => '{field} tidak boleh kurang dari 4 karakter.',
                    'max_length' => '{field} tidak boleh lebih dari 20 karakter.',
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
            'wilayah'  => [
                'rules' => 'matches[wilayah.kode]',
                'errors' => [
                    'matches' => 'Kode wilayah yang anda masukkan tidak valid.'
                ]
            ]
        ];

        if(!$this->validate($rules)) return $this->fail($this->validator->getErrors());
        $ma = new ModelAnggota();
        $mw = new ModelWilayah();
        $json = $this->request->getJSON();
        $nia = $json->nia;
        $email = $json->email;

        $cek = $ma->select('*')->where('nia', $nia)->first();
        if ($cek) return $this->fail('Nomor induk anggota ' . $nia . ' sudah ada.', 400);
        // if ($cek['email'] == $email) return $this->fail('Alamat email ' . $nia . ' sudah ada.', 400);

        $wilayah = $json->wilayah;
        // $cekWilayah = $mw->select('*')->where('kode', $wilayah)->first();
        // if(!$cekWilayah) return $this->fail('Kode ' . $wilayah . ' tidak terdaftar.', 400);
        $data = [
            'nia' => $nia,
            'nama' => $json->nama,
            'alamat' => $json->alamat,
            'wa' => $json->wa,
            'wilayah' => $wilayah,
            'email' => $email,
            'level' => 'user',
            'password' => password_hash($nia, PASSWORD_DEFAULT),
            'aktif' => 'aktif'
        ];

        try {
            $ma->insert($data);
            return $this->respondCreated($data);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage(), $th->getCode());
        }
    }

    public function newRegisterAnggota()
    {
        $mw = new ModelWilayah();
        $wilayah = $mw->select('*')->where('aktif', '1')->findAll();
        $data = [
            'anggota' => [
                'nia' => null,
                'nama' => null,
                'alamat' => null,
                'wa' => null,
                'wilayah' => null
            ],
            'wilayah' => $wilayah
        ];
        return $this->respond($data);
    }
    public function addRegisterAnggota()
    {
        helper(['form']);
        $rules = [
            'nia'          => [
                'rules' => 'required|min_length[4]|max_length[20]|is_unique[anggota.nia]',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                    'min_length' => '{field} tidak boleh kurang dari 4 karakter.',
                    'max_length' => '{field} tidak boleh lebih dari 20 karakter.',
                    'is_unique' => '{filed} sudah terpakai.'
                ],
            ],
            'email'         => 'required|min_length[4]|max_length[100]|valid_email|is_unique[anggota.email]',
            'alamat'      => 'required|min_length[4]|max_length[300]',
            'wilayah'  => 'matches[password]'
        ];

        if(!$this->validate($rules)) return $this->fail($this->validator->getErrors());
        $ma = new ModelAnggota();
        $json = $this->request->getJSON();
        $nia = $json->nia;

        $cek = $ma->select('*')->where('nia', $nia)->first();
        if ($cek) return $this->fail('Nomor induk anggota ' . $nia . ' sudah ada.', 400);
        $data = [
            'nia' => $nia,
            'nama' => $json->nama,
            'alamat' => $json->alamat,
            'wa' => $json->wa,
            'wilayah' => $json->wilayah,
            'level' => 'user',
            'password' => password_hash($nia, PASSWORD_DEFAULT),
            'aktif' => 'baru'
        ];

        try {
            $ma->insert($data);
            return $this->respondCreated($data);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage(), $th->getCode());
        }
    }
}
