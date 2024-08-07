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
    public function bayar($nomor_pembayaran)
    {
        $mi = new ModelInfaq();
        $mp = new ModelPembayaran();
        $json = $this->request->getJSON();
        $tgl_sekarang = date('Y-m-d H:i:s');
        // $kode_infaq = $json->kode_infaq;
        $nia = $json->nia; //diambil dari token
        $nominal_pembayaran = (int) $json->bayar;

        $bayar = $mp->select('*')->where(['nomor_pembayaran' => $nomor_pembayaran ])->first();
        if(!$bayar) return $this->fail('Kode pembayaran infaq '.$bayar['kode_infaq'].' anda tidak ditemukan.', 400);
        if($bayar['nia'] != $nia) return $this->fail('Anda tidak berhak membayar nomor pembayaran infaq '.$nomor_pembayaran.' ini.', 400);

        $infaq = $mi->select('*')->where('kode_infaq', $bayar['kode_infaq'])->first();
        if($infaq['aktif' == '0']) return $this->fail('Kode infaq '.$bayar['kode_infaq'].' telah dihapus oleh admin.', 400);
        $nominal_infaq = (int) $infaq['nominal'];
        if((int) $bayar['bayar'] >= $nominal_infaq && $bayar['validator'] == null) return $this->fail('Kode infaq Anda sudah terbayar namun belum diterima oleh admin. silahkan hubungi admin untuk konfirmasi.', 400);

        $data = [
            'bayar' => $json->bayar,
            'tanggal_bayar' => $tgl_sekarang,
            'bukti_bayar' => $json->bukti_bayar
        ];

        try {
            $mp->set($data);
            $mp->where(['nomor_pembayaran' => $nomor_pembayaran]);
            $mp->update();
            $this->respond(['pesan' => 'Pembayaran infaq Anda berhasil dilakukan.']);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage(), $th->getCode());
        }
    }

    public function terima($nomor_pembayaran)
    {
        $mi = new ModelInfaq();
        $mp = new ModelPembayaran();
        $json = $this->request->getJSON();
        $tgl_sekarang = date('Y-m-d H:i:s');
        $validator = $json->validator; //diambil dari token
        $nominal_infaq = (int) $infaq['nominal'];
        $infaq = $mi->select('*')->where('kode_infaq', $kode_infaq)->first();
        if(!$infaq) return $this->fail('Kode infaq '.$kode_infaq.' tidak ditemukan.', 400);

        $bayar = $mp->select('*')->where(['kode_infaq' => $kode_infaq, 'nia' => $nia])->first();
        if(!$bayar) return $this->fail('Kode pembayaran infaq '.$kode_infaq.' anda tidak ditemukan.', 400);
        if((int) $bayar['bayar'] >= $nominal_infaq && $bayar['validator'] == null) return $this->fail('Kode infaq Anda sudah terbayar namun belum diterima oleh admin. silahkan hubungi admin untuk konfirmasi.', 400);

        $data = [
            'bayar' => $json->bayar,
            'tanggal_bayar' => $tgl_sekarang,
            'bukti_bayar' => $json->bukti_bayar
        ];

        try {
            $mp->set($data);
            $mp->where(['kode_infaq' => $kode_infaq, 'nia' => $nia]);
            $mp->update();
            $this->respond(['pesan' => 'Pembayaran infaq Anda berhasil dilakukan.']);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage(), $th->getCode());
        }
    }
}
