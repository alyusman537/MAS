<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\ModelUmum;
use App\Models\ModelTmpUmum;
use App\Models\ModelAnggota;

use App\Libraries\JwtDecode;
use App\Libraries\LibFonnte;

class Umum extends BaseController
{
    use ResponseTrait;
    private $db;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
    public function periode($tgl_awal, $tgl_akhir)
    {
        $mu = new ModelUmum();
        return $this->respond($mu->periode($tgl_awal, $tgl_akhir));
    }

    public function byId($kode)
    {
        $mu = new ModelUmum();
        $data = $mu->select('*')->where('kode', $kode)->first();
        if (!$data) return $this->fail('Data Infaq umum tidak ditemukan.', 402);

        if (!$data['validator']) {
            $validasi = null;
            $tgl_validasi = null;
            $jam_validasi = null;
        } else {
            $validasi = explode('-', $data['tanggal_validasi']);
            $tgl_validasi = substr($validasi[2], 0, 2) . '-' . $validasi[1] . '-' . $validasi[0];
            $jam_validasi = explode(' ', $data['tanggal_validasi'])[1];
        }
        $tanggal = explode('-', $data['tanggal']);
        $bukti = $data['bukti'] == null ? base_url() . 'No_Image_Available.jpg' : base_url() . 'api/render/bukti/' . $data['bukti'];
        $resp = [
            'bukti' => $bukti,
            'tanggal' => $tanggal[2] . '-' . $tanggal[1] . '-' . $tanggal[0],
            'kode' => $data['kode'],
            'nominal' => $data['nominal'],
            'keterangan' => $data['keterangan'],
            'validator' => $data['validator'],
            'tanggal_validasi' => $tgl_validasi,
            'jam_validasi' => $jam_validasi
        ];
        return $this->respond($resp);
    }

    public function new()
    {
        $header = $this->request->getServer('HTTP_AUTHORIZATION');
        $decoder = new JwtDecode();
        $user = $decoder->decoder($header);
        $nia = $user->sub; //dari token

        $mu = new ModelTmpUmum();
        $tmp = $mu->select('*')->where('nia', $nia)->first();
        if ($tmp) {
            $mu->where(['nia' => $nia]);
            $mu->delete();
        }
        $data = [
            'nominal' => 0,
            'keterangan' => null
        ];
        $mu->insert($data);
        return $this->respondCreated($data);
    }


    public function add()
    {
        $header = $this->request->getServer('HTTP_AUTHORIZATION');
        $decoder = new JwtDecode();
        $user = $decoder->decoder($header);
        $nia = $user->sub; //dari token

        helper(['form', 'url']);
        $rules = [
            'nominal'         => [
                'rules' =>  'required',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                ]
            ],
            'keterangan'         => [
                'rules' =>  'required|min_length[4]|max_length[300]',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                    'min_length' => '{field} tidak boleh kurang dari 4 karakter',
                    'max_length' => '{field} tidak boleh lebih dari 300 karakter'
                ]
            ],
            'bukti' => [
                'rules' => [
                    'uploaded[bukti]',
                    'is_image[bukti]',
                    'mime_in[bukti,image/jpg,image/jpeg,image/png]',
                    'max_size[bukti,2048]',
                    // 'max_dims[userfile,1024,768]',
                ],
                'errors' => [
                    'uploaded' => 'tidak ada gambar yagn diupload',
                    'is_image' => 'file harus berupa gambar',
                    'mime_in' => 'gambar harus berupa jpg atau jpeg',
                    'max_size' => 'ukurang gambar harus kurang dari 2mb'
                ]
            ]
        ];

        if (!$this->validate($rules)) return $this->fail($this->validator->getErrors(), 409);

        $mu = new ModelUmum();
        // $json = $this->request->getJSON();
        // $this->db->transBegin();
        try {
            $x_file = $this->request->getFile('bukti');
            $ukuran = filesize($x_file);
            $namaFoto = $x_file->getRandomName();
            
            $image = service('image');
            $image->withFile($x_file)
            ->resize(500, 500, true, 'height')
            ->save(WRITEPATH . '/uploads/bukti/' . $namaFoto);
            
            unlink($x_file);
            try {
                $data = [
                    'tanggal' => date('Y-m-d'),
                    'kode' => time() . '-' . $nia,
                    'nominal' => $this->request->getVar('nominal'), // $json->nominal,
                    'nia' => $nia,
                    'keterangan' => $this->request->getVar('keterangan'), //$json->keterangan
                    'bukti' => $namaFoto
                ];
                $insert = $mu->insert($data);
                if (!$insert) unlink(WRITEPATH . '/uploads/bukti/' . $namaFoto);

                $fonnte = new LibFonnte();
                $ma = new ModelAnggota();
                $admin = $ma->select('wa')->where(['level' => 'admin'])->findAll();
                $nomoradmin = [];
                foreach ($admin as $key => $val) {
                    $nomoradmin[] = $val['wa'];
                }
                $nomor = implode(",", $nomoradmin);
                $pesan = 'Mohon segera terima pembayaran infaq Umum untuk *' . $this->request->getVar('keterangan') . '* dari Nomor anggoa *' . $nia . '*';
                $fonnte::kirimPesan($nomor, $pesan);

                return $this->respondCreated($data);
            } catch (\Throwable $th) {
                return $this->fail($th->getMessage(), 500);
            }
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage(), 500);
        }
    }

    public function edit($id)
    {
        $header = $this->request->getServer('HTTP_AUTHORIZATION');
        $decoder = new JwtDecode();
        $user = $decoder->decoder($header);
        $nia = $user->sub; //dari token

        $mu = new ModelUmum();
        $infaq = $mu->find($id);

        if (!$infaq || $infaq['nia'] != $nia) return $this->fail('Data Infaq umum tidak ditemukan', 409);

        $data = [
            'nominal' => $infaq['nominal'],
            'keterangan' => $infaq['keterangan'],
        ];
        try {
            $insert = $mu->insert($data);
            if (!$insert) return $this->fail($mu->errors(), 409);
            return $this->respondCreated($data);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage(), $th->getCode());
        }
    }

    public function update($id)
    {
        $header = $this->request->getServer('HTTP_AUTHORIZATION');
        $decoder = new JwtDecode();
        $user = $decoder->decoder($header);
        $nia = $user->sub; //dari token

        $mu = new ModelUmum();
        $infaq = $mu->find($id);
        $json = $this->request->getJSON();

        if (!$infaq) return $this->fail('Data Infaq umum tidak ditemukan', 402);
        if ($infaq['nia'] != $nia) return $this->fail('Anda tidak berhak merubah data Infaq orang lain.', 402);
        $data = [
            'nominal' => $json->nominal,
            'keterangan' => $json->keterangan,
        ];
        try {
            $insert = $mu->insert($data);
            if (!$insert) return $this->fail($mu->errors(), 409);
            return $this->respondCreated($data);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage(), $th->getCode());
        }
    }

    public function delete($kode)
    {
        $header = $this->request->getServer('HTTP_AUTHORIZATION');
        $decoder = new JwtDecode();
        $user = $decoder->decoder($header);
        $nia = $user->sub; //dari token

        $mu = new ModelUmum();
        $infaq = $mu->select('*')->where('kode', $kode)->first();

        if (!$infaq) return $this->fail('Data Infaq umum tidak ditemukan', 402);
        if ($infaq['nia'] != $nia) return $this->fail('Anda tidak berhak menghapus data Infaq orang lain.', 402);
        if ($infaq['validator'] != null) return $this->fail('Infaq yang sudah diterima tidak dapat dihapus.', 402);

        $mu->where(['kode' => $kode]);
        $del = $mu->delete();
        if (!$del) return $this->fail('Gagal menghapus data infaq umum', 402);
        return $this->respondDeleted($data = null, 'Data infaq umum telah terhapus');
    }

    public function unggahBukti($kode)
    {
        $header = $this->request->getServer('HTTP_AUTHORIZATION');
        $decoder = new JwtDecode();
        $user = $decoder->decoder($header);
        $nia = $user->sub; //dari token

        helper(['form', 'url']);
        $mm = new ModelUmum();

        $fotoLama = $mm->select('*')->where('kode', $kode)->first();
        if (!$fotoLama || $fotoLama['nia'] != $nia) return $this->fail('Kode infaq umum ' . $kode . ' tidak ditemukan.', 402);
        // if($fotoLama['nia'] != $nia) return $this->fail('Anda tidak berhak upload bukti transaksi pada kode infaq ini.', 400);
        $foto = isset($fotoLama['bukti']) ? $fotoLama['bukti'] : false;

        $path_ori = WRITEPATH . 'uploads/bukti/' . $foto;
        helper(['form', 'url']);
        $validationRule = [
            'bukti' => [
                // 'label' => 'Image File',
                'rules' => [
                    'uploaded[bukti]',
                    'is_image[bukti]',
                    'mime_in[bukti,image/jpg,image/jpeg,image/png]',
                    'max_size[bukti,2048]',
                    // 'max_dims[userfile,1024,768]',
                ],
                'errors' => [
                    'uploaded' => 'tidak ada gambar yagn diupload',
                    'is_image' => 'file harus berupa gambar',
                    'mime_in' => 'gambar harus berupa jpg atau jpeg',
                    'max_size' => 'ukurang gambar harus kurang dari 2mb'
                ]
            ],
        ];
        if (! $this->validateData([], $validationRule)) {
            return $this->fail($this->validator->getErrors(), 409);
        }

        $x_file = $this->request->getFile('bukti');
        $namaFoto = $x_file->getRandomName();
        // $x_file->move(WRITEPATH . 'uploads/bukti/', $namaFoto);
        $image = service('image');
        $image->withFile($x_file)
            ->resize(500, 500, true, 'height')
            ->save(WRITEPATH . '/uploads/bukti/' . $namaFoto);

        unlink($x_file);

        $mm->set(['bukti' => $namaFoto]);
        $mm->where('kode', $kode);
        $mm->update();

        if ($foto) {
            if (file_exists($path_ori)) {
                unlink($path_ori);
            }
        }

        return $this->respond(['image' => $namaFoto]);
    }
}
