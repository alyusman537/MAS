<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

use App\Models\ModelAnggota;
use App\Models\ModelWilayah;
use App\Models\ModelInfaq;

use App\Libraries\JwtDecode;

class Infaq extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        $mi = new ModelInfaq();
        $data = $mi->select('*')->orderBy(['tanggal' => 'DESC'])->findAll();
        return $this->respond($data);
    }
    public function byId($id = null)
    {
        $mi = new ModelInfaq();
        $data = $mi->select('*')->where('id', $id)->first();
        if(!$data) return $this->fail('Data infaq tidak ditemukan.', 400);
        return $this->respond($data);
    }

    public function new ()
    {
        $data = [
            'tanggal' => null, 
            'kode' => null,
            'header' => null,
            'keterangan' => null,
            'nominal' => 0,
            'rutin' => true,
        ];
        return $this->respond($data);
    }

    public function add ()
    {
        $mi = new ModelInfaq();
        $tglSekarang = date('Y-m-d');
        $infaq = $mi->select(' left(tanggal, 10) as tanggal')->where('tanggal', $tglSekarang)->first();
        $kode;
        if(!$infaq) {
            $kode = $tglSekarang.'-001';
        } elseif ($infaq['tanggal'] == $tglSekarang) {
            # code...
        }
        $data = [
            'tanggal' => null, 
            'kode' => null,
            'header' => null,
            'keterangan' => null,
            'nominal' => 0,
            'rutin' => true,
        ];
        return $this->respond($data);
    }
}
