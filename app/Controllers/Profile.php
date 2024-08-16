<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

use App\Models\ModelAnggota;
use App\Models\ModelPembayaran;

use App\Libraries\JwtDecode;

class Profile extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        $header = $this->request->getServer('HTTP_AUTHORIZATION');
        $decoder = new JwtDecode();
        $user = $decoder->decoder($header);

        $nia = $user->sub; //dari token
        $ma = new ModelAnggota();
        $mp = new ModelPembayaran();
        $profile = $ma->select('*')->where('nia',$nia)->first();
        $foto = $profile['foto'] === null ? base_url() . 'api/render/foto/no_photo.jpg' : base_url() . 'api/render/foto/' . $profile['foto'];

        $bayar = $mp->selectCount('nomor_pembayaran')->where(['nia' => $nia])->where('validator IS NULL')->first();
        $data = [
            'nia' => $profile['nia'],
            'nama' => strtoupper($profile['nama']),
            'wa' => $profile['wa'],
            'alamat' => $profile['alamat'],
            'kode_wilayah' => $profile['wilayah'],
            'level' => $profile['level'],
            'foto' => $foto,
            'email' => $profile['email'],
            'aktif' => $profile['aktif'],
            'iuran_belum_terbayar' =>(int) $bayar['nomor_pembayaran']
        ];

        return $this->respond($data);
    }

    public function edit($nia)
    {
        $ma = new ModelAnggota();
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

    public function update($nia)
    {
        $header = $this->request->getServer('HTTP_AUTHORIZATION'); // header("Authorization");
        $decoder = new JwtDecode();
        $user = $decoder->decoder($header); 
        $nia_from_token = $user->sub; //dari token
        if ($nia != $nia_from_token) return $this->fail('Anda tidak berhak mengedit data anggota lainnya.', 400);

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
                'rules' => 'required|min_length[4]|max_length[100]|valid_email',//|is_unique[anggota.email]',
                'errors' => [
                    'required' => '{field} tidak boleh kosong',
                    'min_length' => '{field} tidak boleh kurang dari 4 karakter.',
                    'max_length' => '{field} tidak boleh lebih dari 100 karakter.',
                    // 'is_unique' => '{field} sudah terdaftar.',
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

        $json = $this->request->getJSON();
        // $nia = $json->nia;
        $email = $json->email;

        $cek = $ma->select('*')->where('nia', $nia)->first();
        if (!$cek) return $this->fail('Nomor induk anggota Anda belum terdaftar.', 400);
        $data = [
            'nama' => $json->nama,
            'alamat' => $json->alamat,
            'wa' => $json->wa,
            'email' => $email,
        ];

        try {
            $ma->set($data);
            $ma->where('nia', $nia);
            $ma->update();
            return $this->respond(['pesan' => 'Data Anda berhasil diperbaharui.']);
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

    public function updatePassword($nia)
    {
        $header = $this->request->getServer('HTTP_AUTHORIZATION'); // header("Authorization");
        $decoder = new JwtDecode();
        $user = $decoder->decoder($header); 
        $nia_from_token = $user->sub; //dari token

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

        $password_lama = $json->password_lama;
        $password_baru = $json->password_baru;
        // $konfirmasi_password = $json->konfirmasi_password;

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

    public function foto()
    {
        $header = $this->request->getServer('HTTP_AUTHORIZATION');
        $decoder = new JwtDecode();
        $user = $decoder->decoder($header);
        $nia = $user->sub; //dari token

        helper(['form', 'url']);
        $validationRule = [
            'foto' => [
                // 'label' => 'Image File',
                'rules' => [
                    'uploaded[foto]',
                    'is_image[foto]',
                    'mime_in[foto,image/jpg,image/jpeg]',
                    'max_size[foto,4096]',
                    // 'max_dims[userfile,1024,768]',
                ],
                'errors' => [
                    'uploaded' => 'tidak ada gambar yagn diupload',
                    'is_image' => 'file harus berupa gambar',
                    'mime_in' => 'gambar harus berupa jpg atau jpeg',
                    'max_size' => 'ukurang gambar harus kurang dari 4mb'
                ]
            ],
        ];
        if (! $this->validateData([], $validationRule)) {
            return $this->fail($this->validator->getErrors());

        }
        $mm = new ModelAnggota();

        $fotoLama = $mm->select('*')->where('nia', $nia)->first();

        $foto = isset($fotoLama['foto']) ? $fotoLama['foto'] : false;
        $path_ori = WRITEPATH . 'uploads/profile/' . $foto;

        $x_file = $this->request->getFile('foto');
        $namaFoto = $x_file->getRandomName();

        $x_file->move(WRITEPATH . 'uploads/profile/', $namaFoto);

        $mm->set(['foto' => $namaFoto]);
        $mm->where('nia', $nia);
        $mm->update();

        if ($foto) {
            if (file_exists($path_ori)) {
                unlink($path_ori);
            }
        }

        return $this->respond(['image' => $namaFoto]);
    }
}
