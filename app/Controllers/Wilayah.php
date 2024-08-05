<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

use App\Models\ModelWilayah;

class Wilayah extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        $mw = new ModelWilayah();
        $data = $mw->select('*')->orderBy('keterangan', 'ASC')->findAll();
        return $this->respond($data);
    }
    public function wilayahById($id = null)
    {
        $mw = new ModelWilayah();
        $data = $mw->select('*')->where('id', $id )->first();
        if(!$data) return $this->fail('ID wilayah tidak ada.',400);
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

        if(!$this->validate($rules)) return $this->fail($this->validator->getErrors());
        $mw = new ModelWilayah();
        $json = $this->request->getJSON();
        $kode = $json->kode;
        $cek = $mw->select('*')->where('kode', $kode)->first();
        if($cek) return $this->fail('Kode Wilayah '.$kode.' sudah terpakai.', 400);
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
        ];

        if(!$this->validate($rules)) return $this->fail($this->validator->getErrors());
        $mw = new ModelWilayah();
        $json = $this->request->getJSON();
        $cek = $mw->where('id', $id)->first();
        if(!$cek) return $this->fail('Data wilayah tidak ditemukan', 400);
        $data = [
            'keterangan' => $json->keterangan,
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
