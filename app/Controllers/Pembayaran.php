<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

use App\Models\ModelAnggota;
use App\Models\ModelWilayah;
use App\Models\ModelInfaq;
use App\Models\ModelPembayaran;

use App\Libraries\JwtDecode;

class Pembayaran extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        //
    }
    public function bayar()
    {
        $mi = new ModelInfaq();
        $mp = new ModelPembayaran();
        $json = $this->request->getJSON();
        $tgl_sekarang = date('Y-m-d');
        $kode_infaq = $json->kode_infaq;
        $nominal_infaq = (int) $infaq['nominal'];
        $infaq = $mi->select('*')->where('kode_infaq', $kode_infaq)->first();
        if(!$infaq) return $this->fail('Kode infaq '.$kode_infaq.' tidak ditemukan.', 400);

        $bayar = $mp->select('*')->where(['kode_infaq' => $kode_infaq, 'nia' => $json->nia])->first();
        if(!$bayar) return $this->fail('Kode pembayaran infaq '.$kode_infaq.' anda tidak ditemukan.', 400);
        if((int) $bayar['bayar'] >= $nominal_infaq && $bayar['validator'] == null) return $this->fail('Kode infaq Anda sudah terbayar namun belum diterima oleh admin. silahkan hubungi admin untuk konfirmasi.', 400);

        $data = [
            'bayar' => $json->bayar,
            'tanggal_bayar' => $tgl_sekarang,
            'bukti_bayar' => $json->bukti_bayar
        ];
    }
}
