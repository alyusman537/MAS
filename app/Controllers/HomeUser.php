<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

use App\Models\ModelInfaq;
use App\Models\ModelPembayaran;
use App\Models\ModelUmum;

use App\Libraries\JwtDecode;

class HomeUser extends BaseController
{
use ResponseTrait;
    public function daftarInfaq($is_lunas)
    {
        $status = $is_lunas == 'lunas'?  'NOT NULL' :'NULL';
        $header = $this->request->getServer('HTTP_AUTHORIZATION');
        $decoder = new JwtDecode();
        $user = $decoder->decoder($header);

        $nia = $user->sub; //dari token
        $mp = new ModelPembayaran();
        
        $data = $mp->daftarInfaq($nia, $status);

        return $this->respond($data);
    }
    public function infaqLunas()
    {
        $header = $this->request->getServer('HTTP_AUTHORIZATION');
        $decoder = new JwtDecode();
        $user = $decoder->decoder($header);

        $nia = $user->sub; //dari token
        $mp = new ModelPembayaran();
        
        $data = $mp->lunas($nia);

        return $this->respond($data);
    }
    public function infaqUmum()
    {
        $header = $this->request->getServer('HTTP_AUTHORIZATION');
        $decoder = new JwtDecode();
        $user = $decoder->decoder($header);

        $nia = $user->sub; //dari token
        $mu = new ModelUmum();
        $data = $mu->select('*')->where('nia', $nia)->orderBy('tanggal', 'DESC')->limit(50)->findAll();
        return $this->respond($data);
    }
}
