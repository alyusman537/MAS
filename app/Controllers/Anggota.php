<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;

use App\Models\ModelAnggota;

class Anggota extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        //
    }

    public function allAnggota()
    {
        $ma = new ModelAnggota;
        return $this->respond($ma->allAnggota());
    }

    public function anggotaById($id = null)
    {
        $ma = new ModelAnggota();
        $data = $ma->select('id, nia, nama, alamat, wa, wilayah, level, aktif')->where('id', $id)->first();
        if(!$data) return $this->fail('ID anggota '.$id.' tidak ditemukan.',400);
        return $this->respond($data);
    }

    public function addAnggota()
    {
        $ma = new ModelAnggota();
        $json = $this->request->getJSON();
        $nia = $json->nia;

        $cek = $ma->select('*')->where('nia', $nia)->first();
        if($cek) return $this->fail('Nomor induk anggota '.$nia.' sudah terpakai.', 400);
        $data = [
            'nia' => $nia,
            'nama' => $json->nama,
            'alamat' => $json->alamat,
            'wa' => $json->wa,
            'wilayah' => $json->wilayah,
            'level' => $json->level,
            'aktif' => true
        ];

        try {
            $ma->insert($data);
            return $this->respondCreated($data);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage(), $th->getCode());
        }
    }
}
