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
        $header = $this->request->getServer('HTTP_AUTHORIZATION');
        $decoder = new JwtDecode();
        $user = $decoder->decoder($header);
        $nia = $user->sub; //dari token

        $mu = new ModelUmum();
        $infaq = $mu->find($id);
        $json = $this->request->getJSON();

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
        $foto = isset($fotoLama['bukti']) ? $fotoLama['bukti'] : false;

        $path_ori = WRITEPATH . 'uploads/foto/' . $foto;
        helper(['form', 'url']);
        $validationRule = [
            'bukti' => [
                // 'label' => 'Image File',
                'rules' => [
                    'uploaded[bukti]',
                    'is_image[bukti]',
                    'mime_in[bukti,image/jpg,image/jpeg,image/png]',
                    'max_size[bukti,4096]',
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
            return $this->fail($this->validator->getErrors(), 400);
        }

        $x_file = $this->request->getFile('bukti');
        $namaFoto = $x_file->getRandomName();
        $x_file->move(WRITEPATH . 'uploads/foto', $namaFoto);

        $mm->set(['image' => $namaFoto]);
        $mm->where('id', $id);
        $mm->update();

        if ($foto) {
            if (file_exists($path_ori)) {
                unlink($path_ori);
            }
        }

        return $this->respond(['image' => $namaFoto]);

    }
}
