<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

use App\Models\ModelInfaq;
use App\Models\ModelPembayaran;
use App\Models\ModelUmum;

use App\Libraries\JwtDecode;

class Penerimaan extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        //
    }

    public function daftarTunggu($status)
    {
        $is_lunas = $status == 'lunas' ? 'NOT NULL' : 'NULL';
        $mp = new ModelPembayaran();
        $data = $mp->daftarTunggu($is_lunas);
        return $this->respond($data);
    }

    public function daftarUmum($status)
    {
        $is_lunas = $status == 'lunas' ? 'NOT NULL' : 'NULL';
        $mp = new ModelUmum();
        $data = $mp->daftarUmum($is_lunas);
        return $this->respond($data);
    }

    public function terimaInfaq($nomor_pembayaran)
    {
        $header = $this->request->getServer('HTTP_AUTHORIZATION');
        $decoder = new JwtDecode();
        $user = $decoder->decoder($header);

        $mi = new ModelInfaq();
        $mp = new ModelPembayaran();
        $tgl_sekarang = date('Y-m-d H:i:s');
        $validator = $user->sub; //dari token

        $bayar = $mp->select('*')->where(['nomor_pembayaran' => $nomor_pembayaran])->first();
        $infaq = $mi->select('*')->where('kode', $bayar['kode_infaq'])->first();
        if (!$bayar) return $this->fail('Kode pembayaran infaq ' . $nomor_pembayaran . ' anda tidak ditemukan.', 400);
        if ($bayar['bayar'] >= (int) $infaq['nominal'] && $bayar['validator'] != null) return $this->fail('Pembayaran iuran sudah tervalidasi.', 400);
        if ((int) $bayar['bayar'] < (int) $infaq['nominal']) return $this->fail('Nominal iuran kurang dari ketentuan infaq.', 400);

        $data = [
            'validator' => $validator,
            'tanggal_validasi' => $tgl_sekarang,
        ];

        try {
            $mp->set($data);
            $mp->where('nomor_pembayaran', $nomor_pembayaran);
            $mp->update();
            $this->respond(['pesan' => 'Pembayaran infaq Anda berhasil diterima oleh ' . $validator . '.']);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage(), $th->getCode());
        }
    }

    public function terimaUmum($kode)
    {
        $header = $this->request->getServer('HTTP_AUTHORIZATION');
        $decoder = new JwtDecode();
        $user = $decoder->decoder($header);

        $mp = new ModelUmum();
        $tgl_sekarang = date('Y-m-d H:i:s');
        $validator = $user->sub; //dari token

        $bayar = $mp->select('*')->where(['kode' => $kode])->first();
        
        if (!$bayar) return $this->fail('Kode infaq umum ' . $kode . ' tidak ditemukan.', 400);
        if ($bayar['bukti'] == null) return $this->fail('Bukti pembayaran masih belum diupload oleh anggota.', 400);

        $data = [
            'validator' => $validator,
            'tanggal_validasi' => $tgl_sekarang,
        ];

        try {
            $mp->set($data);
            $mp->where('kode', $kode);
            $mp->update();
            $this->respond(['pesan' => 'Pembayaran infaq Anda berhasil diterima oleh ' . $validator . '.']);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage(), $th->getCode());
        }
    }
}
