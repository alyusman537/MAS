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
        $header = $this->request->getServer('HTTP_AUTHORIZATION'); // header("Authorization");
        $key = new JwtDecode();
        $decoded = $key->decoder($header);
        $tangal = date('Y-m-d H:i:s', $decoded[0]->exp);
        return $this->respond(['key' => $decoded, 'tang' => $tangal]);
    }

    public function all()
    {
        $ma = new ModelAnggota();
        return $this->respond($ma->allAnggota());
    }

    public function byId($id = null)
    {
        $ma = new ModelAnggota();
        $mw = new ModelWilayah();
        $anggota = $ma->select('*')->where('id', $id)->first();
        if (!$anggota) return $this->fail('ID anggota ' . $id . ' tidak ditemukan.', 402);
        $data = [
            'nia' => $anggota['nia'],
            'nama' => $anggota['nama'],
            'wa'  => $anggota['wa'],
            'alamat' => $anggota['alamat'],
            'wilayah' => $anggota['wilayah'],
            'level' => $anggota['level'],
            'foto' => !$anggota['foto'] ? base_url() . 'no_photo.jpg' : base_url() . 'api/render/foto/' . $anggota['foto'],
            'email' => $anggota['email'],
            'aktif' => $anggota['aktif'],
        ];
        return $this->respond($data);
    }

    public function byWilayah($wilayah)
    {
        $ma = new ModelAnggota();
        $mw = new ModelWilayah();
        $cekWilayah = $mw->select('*')->where('kode', $wilayah)->first();
        if (!$cekWilayah) return $this->fail('Kode wilayah ' . $wilayah . ' tidak ada.', 402);
        $anggota = $ma->select('id, nia, nama, alamat, wa, wilayah, level, aktif')->where(['wilayah' => $wilayah, 'aktif' => 'aktif'])->findAll();
        // if (!$anggota) return $this->fail('ID anggota ' . $id . ' tidak ditemukan.', 402);
        // $data = [
        //     'anggota' => $anggota,
        //     'wilayah' => $mw->select('keterangan')->where('kode', $anggota['wilayah'])->first()
        // ];
        return $this->respond($anggota);
    }

    public function new()
    {
        $mw = new ModelWilayah();
        $wilayah = $mw->select('*')->where('aktif', '1')->findAll();
        $data = [
            'anggota' => [
                'nia' => null,
                'nama' => null,
                'alamat' => null,
                'wa' => null,
                'email' => null,
                'wilayah' => null
            ],
            'wilayah' => $wilayah
        ];
        return $this->respond($data);
    }

    public function add()
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
            'nama'          => [
                'rules' => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                    'min_length' => '{field} tidak boleh kurang dari 3 karakter.',
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
            'wilayah'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} tidak boleh kosong',
                ]
            ]
        ];

        if (!$this->validate($rules)) return $this->fail($this->validator->getErrors(), 409);
        $ma = new ModelAnggota();
        $mw = new ModelWilayah();
        $json = $this->request->getJSON();
        $nia = $json->nia;
        $email = $json->email;

        $cek = $ma->select('*')->where('nia', $nia)->first();
        if ($cek) return $this->fail('Nomor induk anggota ' . $nia . ' sudah ada.', 402);
        // if ($cek['email'] == $email) return $this->fail('Alamat email ' . $nia . ' sudah ada.', 402);

        $wilayah = $json->wilayah;
        $cekWilayah = $mw->select('*')->where(['kode' => $wilayah, 'aktif' => '1'])->first();
        if (!$cekWilayah) return $this->fail('Kode wilayah ' . $wilayah . ' tidak terdaftar.', 402);
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
            return $this->respond(['pesan' => 'Data anggota baru berhasil disimpan.'], 201);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage(), $th->getCode());
        }
    }

    public function edit($id)
    {
        $ma = new ModelAnggota();
        $mw = new ModelWilayah();
        $anggota = $ma->select('*')->where('id', $id)->first();
        if (!$anggota) return $this->fail('ID anggota tidak ditemukan.', 402);
        $wilayah = $mw->select('*')->where('aktif', '1')->findAll();
        // $cekWilayah = $mw->select('*')->where('kode', $wilayah)->first();
        $data = [
            'anggota' => [
                'id' => $anggota['id'],
                'nia' => $anggota['nia'],
                'nama' => $anggota['nama'],
                'alamat' => $anggota['alamat'],
                'wa' => $anggota['wa'],
                'wilayah' => strtoupper($anggota['wilayah']),
                'email' => $anggota['email'],
                'level' => $anggota['level'],
                'aktif' => $anggota['aktif'],
            ],
            'ubah' => [
                'nama' => $anggota['nama'],
                'alamat' => $anggota['alamat'],
                'wa' => $anggota['wa'],
                'wilayah' => strtoupper($anggota['wilayah']),
                'email' => $anggota['email'],
                'level' => $anggota['level'],
            ],
            'wilayah' => $wilayah
        ];

        return $this->respond($data);
    }

    public function update($id)
    {
        helper(['form']);
        $rules = [
            'nama'          => [
                'rules' => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                    'min_length' => '{field} tidak boleh kurang dari 3 karakter.',
                    'max_length' => '{field} tidak boleh lebih dari 100 karakter.',
                    'is_unique' => '{field} sudah terdaftar.'
                ],
            ],
            'email'         => [
                'rules' => 'required|min_length[4]|max_length[100]|valid_email',
                'errors' => [
                    'required' => '{field} tidak boleh kosong',
                    'min_length' => '{field} tidak boleh kurang dari 4 karakter.',
                    'max_length' => '{field} tidak boleh lebih dari 100 karakter.',
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
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.'
                ]
            ],
            'level'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.'
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
        $email = $json->email;
        $input_wilayah = strtoupper($json->wilayah);

        $cek = $ma->select('*')->where('id', $id)->first();
        if (!$cek) return $this->fail('ID anggota tidak ditemukan.', 402);

        $wilayah = $mw->select('*')->where(['kode' => $input_wilayah, 'aktif' => '1'])->first();
        if (!$wilayah) return $this->fail('Kode ' . $input_wilayah . ' yang anda masukkan tidak terdaftar.', 402);
        $data = [
            'nama' => $json->nama,
            'alamat' => $json->alamat,
            'wa' => $json->wa,
            'wilayah' => strtoupper($input_wilayah),
            'email' => $email,
            'level' => $json->level,
        ];

        try {
            $ma->set($data);
            $ma->where('id', $id);
            $ma->update();
            return $this->respond(['pesan' => 'Data anggota berhasil diperbarui.']);
        } catch (\Throwable $th) {
            return $this->fail(['error' => $th->getMessage()], 402);
        }
    }

    public function delete($id)
    {
        $ma = new ModelAnggota();

        try {
            $ma->set(['aktif' => 'nonaktif']);
            $ma->where('id', $id);
            $ma->update();
            return $this->respond(['pesan' => 'Data anggota berhasil dihapus.']);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage(), $th->getCode());
        }
    }

    public function resetPassword($nia)
    {
        $ma = new ModelAnggota();
        $cek = $ma->select('*')->where('nia', $nia)->first();
        if (!$cek) return $this->fail('Data anggota NIA ' . $nia . ' tidak ada.', 402);
        if ($cek['aktif'] != 'aktif') return $this->fail('Data anggota NIA ' . $nia . ' masuk dalam daftar anggota nonaktif.', 402);
        $data = [
            'password' => password_hash($nia, PASSWORD_DEFAULT),
        ];

        try {
            $ma->set($data);
            $ma->where('nia', $nia);
            $ma->update();
            return $this->respond(['pesan' => 'Password anggota NIA ' . $nia . ' berhasil direset.']);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage(), $th->getCode());
        }
    }
}
