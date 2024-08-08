<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\ModelAnggota;
use App\Models\ModelWilayah;
use App\Models\ModelInfaq;
use App\Models\ModelPembayaran;
use App\Models\ModelUmum;

use App\Libraries\JwtDecode;

use function PHPUnit\Framework\returnSelf;

class Umum extends BaseController
{
    use ResponseTrait;
    public function periode($tgl_awal, $tgl_akhir)
    {
        $mu = new ModelUmum();
        return $this->respond($mu->periode($tgl_awal, $tgl_akhir));
    }

    public function byId($id)
    {
        $mu = new ModelUmum();
        $data = $mu->select('*')->find('id', $id);
        if(!$data) return $this->fail('Data Infaq umum tidak ditemukan.', 400);
        return $this->respond($data);
    }

    public function new()
    {
        $mu = new ModelUmum();
        $data = [
            'nominal' => 0,
            'keterangan' => null
        ];
        return $this->respond($data);
    }

    public function add()
    {
        $mu = new ModelUmum();
        $json = $this->request->getJSON();
        $nia = '000';//amil dari token
        $data = [
            'tanggal' => date('Y-m-d'),
            'kode' => time().'-'.$nia,
            'nominal' => $json->nominal,
            'nia' => $nia,
            'keterangan' => $json->keterangan
        ];
        try {
            $insert = $mu->insert($data);
            if(!$insert) return $this->fail($mu->errors(), 400);
            return $this->respondCreated($data);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage(), $th->getCode());
        }
    }

    public function edit($id)
    {
        $mu = new ModelUmum();
        $infaq = $mu->find($id);
        $nia = '0000'; //dari token
        if(!$infaq) return $this->fail('Data Infaq umum tidak ditemukan', 400);
        if($infaq['nia'] != $nia) return $this->fail('Anda tidak berhak merubah data Infaq orang lain.', 400);
        $data = [
            'nominal' => $infaq['nominal'],
            'keterangan' => $infaq['keterangan']
        ];
        try {
            $insert = $mu->insert($data);
            if(!$insert) return $this->fail($mu->errors(), 400);
            return $this->respondCreated($data);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage(), $th->getCode());
        }
    }

    public function update($id)
    {
        $mu = new ModelUmum();
        $infaq = $mu->find($id);
        $json = $this->request->getJSON();
        $nia = '0000'; //dari token
        if(!$infaq) return $this->fail('Data Infaq umum tidak ditemukan', 400);
        if($infaq['nia'] != $nia) return $this->fail('Anda tidak berhak merubah data Infaq orang lain.', 400);
        $data = [
            'nominal' => $json->nominal,
            'keterangan' => $json->keterangan,
        ];
        try {
            $insert = $mu->insert($data);
            if(!$insert) return $this->fail($mu->errors(), 400);
            return $this->respondCreated($data);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage(), $th->getCode());
        }
    }
    public function unggahBukti($id)
    {
        helper(['form', 'url']);
        $mm = new ModelUmum();

        $fotoLama = $mm->find($id);
        $foto = isset($fotoLama['image']) ? $fotoLama['image'] : false;

        $path_ori = WRITEPATH . 'uploads/foto/' . $foto;
        $path_thumb = WRITEPATH . 'uploads/thumbnail/' . $foto;
        $validateImg = $this->validate(
            [
                'file' => [
                    'uploaded[file]',
                    'mime_in[file,image/jpg,image/jpeg]',
                    //'mime_in[file,image/jpg,image/jpeg,image/png,image/gif]',
                    'max_size[file,4096]',
                ],
            ]
        );
        if (!$validateImg) {
            return $this->fail('Ukuran foto maximal 4mb');
        }

        $x_file = $this->request->getFile('file');
        $namaFoto = $x_file->getRandomName();
        $image = \Config\Services::image()
            ->withFile($x_file)
            ->resize(100, 100, true, 'height')
            ->save(WRITEPATH . '/uploads/thumbnail/' . $namaFoto);

        $x_file->move(WRITEPATH . 'uploads/foto', $namaFoto);

        $mm->set(['image' => $namaFoto]);
        $mm->where('id', $id);
        $mm->update();

        if ($foto) {
            if (file_exists($path_ori)) {
                unlink($path_ori);
            }

            if (file_exists($path_thumb)) {
                unlink($path_thumb);
            }

        }

        return $this->respond(['image' => $namaFoto]);

    }
}
