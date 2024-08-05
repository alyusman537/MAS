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
        //
    }
    public function byId($id = null)
    {
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
}
