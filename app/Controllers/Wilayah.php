<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

use App\Models\ModelAnggota;
use App\Models\ModelWilayah;

class Wilayah extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        $mw = new ModelWilayah();
        $data = $mw->select('*')->orderBy('aktif', 'ASC')->findAll();
        return $this->respond($data);
    }
    public function wilayahById($id = null)
    {
        $mw = new ModelWilayah();
        $data = $mw->select('*')->where('id', $id )->first();
        if(!$data) return $this->fail('ID wilayah tidak ada.',402);
        return $this->respond($data);
    }

    public function add ()
    {
        helper(['form']);
        $rules = [
            'kode'          => [
                'rules' => 'required|min_length[4]|max_length[20]|is_unique[wilayah.kode]',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                    'min_length' => '{field} tidak boleh kurang dari 4 karakter.',
                    'max_length' => '{field} tidak boleh lebih dari 20 karakter.',
                    'is_unique' => '{field} sudah terdaftar.'
                ],
            ],
            'keterangan'      => [
                'rules' => 'required|min_length[4]|max_length[300]',
                'errors' => [
                    'required' => '{field} tidak boleh kosong',
                    'min_length' => '{field} tidak boleh kurang dari 4 karakter.',
                    'max_length' => '{field} tidak boleh lebih dari 300 karakter.',
                ]
            ],
        ];

        if(!$this->validate($rules)) return $this->fail($this->validator->getErrors(), 409);
        $mw = new ModelWilayah();
        $json = $this->request->getJSON();
        $kode = strtoupper($json->kode);
        $cek = $mw->select('*')->where('kode', $kode)->first();
        if($cek) return $this->fail('Kode Wilayah '.$kode.' sudah terpakai.', 402);
        $data = [
            'kode' => $kode,
            'keterangan' => $json->keterangan,
            'aktif' => true
        ];
        try {
            $mw->insert($data);
            return $this->respondCreated($data);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage(), $th->getCode());
        }
    }

    public function update ($id)
    {
        helper(['form']);
        $rules = [
            'keterangan'      => [
                'rules' => 'required|min_length[4]|max_length[300]',
                'errors' => [
                    'required' => '{field} tidak boleh kosong',
                    'min_length' => '{field} tidak boleh kurang dari 4 karakter.',
                    'max_length' => '{field} tidak boleh lebih dari 300 karakter.',
                ]
            ],
            'aktif'      => [
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} tidak boleh kosong',
                ]
            ],
        ];

        if(!$this->validate($rules)) return $this->fail($this->validator->getErrors(), 409);
        $mw = new ModelWilayah();
        $ma = new ModelAnggota();
        $json = $this->request->getJSON();
        $cek = $mw->select('*')->where('id', $id)->first();
        if(!$cek) return $this->fail('Data wilayah tidak ditemukan', 402);
        if($json->aktif == '0') {
            $anggota = $ma->where(['wilayah' => $cek['kode'], 'aktif' => 'aktif'])->first();
            if($anggota) return $this->fail('Wilayah yang masih terdapat anggota aktif tidak boleh dihapus.', 402);
        }
        $data = [
            'keterangan' => $json->keterangan,
            'aktif' => $json->aktif,
        ];
        try {
            $mw->set($data);
            $mw->where('id', $id);
            $mw->update();
            return $this->respond(['pesan' => 'Data wilayah berhasil diperbarui']);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage(), $th->getCode());
        }
    }

    public function delete ($id)
    {
        $mw = new ModelWilayah();

        $cek = $mw->where('id', $id)->first();
        if(!$cek) return $this->fail('Data wilayah tidak ditemukan', 400);
        $data = [
            'aktif' => false,
        ];
        try {
            $mw->set($data);
            $mw->where('id', $id);
            $mw->update();
            return $this->respond(['pesan' => 'Data wilayah berhasil dinonaktifkan.']);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage(), $th->getCode());
        }
    }
}
