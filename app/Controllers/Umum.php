<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\ModelUmum;
use App\Models\ModelTmpUmum;

use App\Libraries\JwtDecode;

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
        $data = $mu->select('*')->where('id', $id)->first();
        if(!$data) return $this->fail('Data Infaq umum tidak ditemukan.', 400);
        return $this->respond($data);
    }

    public function new()
    {
        $header = $this->request->getServer('HTTP_AUTHORIZATION');
        $decoder = new JwtDecode();
        $user = $decoder->decoder($header);
        $nia = $user->sub; //dari token

        $mu = new ModelTmpUmum();
        $tmp = $mu->select('*')->where('nia', $nia)->first();
        if($tmp) {
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

        $mu = new ModelUmum();
        $json = $this->request->getJSON();
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
