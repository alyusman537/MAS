<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

use App\Models\ModelInfaq;
use App\Models\ModelPembayaran;
use App\Models\ModelUmum;
use App\Models\ModelAnggota;

use App\Libraries\JwtDecode;
use App\Libraries\LibMutasi;

class Penerimaan extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        //
    }

    public function daftarTunggu($status)
    {
        $data = [];
        $mp = new ModelPembayaran();
        if ($status == 'lunas') {
            $data = $mp->daftarTungguLunas();
        } else if ($status == 'pending') {
            $data = $mp->daftarTungguPending();
        } elseif ($status == 'baru') {
            $data = $mp->daftarTungguBaru();
        } else {
            return $this->fail('Status pembayaran tidak ditemukan.', 402);
        }
        return $this->respond($data);
    }

    public function daftarUmum($status)
    {
        $is_lunas = $status == 'lunas' ? 'NOT NULL' : 'NULL';
        $mp = new ModelUmum();
        $data = $mp->daftarUmum($is_lunas);
        return $this->respond($data);
    }

    public function pembayaranInfaqDetail($nomor_pembayaran)
    {
        $header = $this->request->getServer('HTTP_AUTHORIZATION');
        $decoder = new JwtDecode();
        $user = $decoder->admin($header);

        $mi = new ModelInfaq();
        $mp = new ModelPembayaran();
        $ma = new ModelAnggota();

        $bayar = $mp->select('*')->where(['nomor_pembayaran' => $nomor_pembayaran])->first();
        if (!$bayar) return $this->fail('Kode pembayaran infaq ' . $nomor_pembayaran . ' anda tidak ditemukan.', 400);
        $infaq = $mi->select('*')->where('kode', $bayar['kode_infaq'])->first();
        if (!$infaq) return $this->fail('Kode infaq ' . $bayar['kode_infaq'] . ' tidak ditemukan.', 400);
        $anggota = $ma->select('nama')->where(['nia' => $bayar['nia']])->first();
        
        $data = [
            'pembayaran' => $bayar,
            'infaq' => $infaq,
            'anggota' => $anggota
        ];

        return $this->respond($data);
    }

    public function terimaInfaq($nomor_pembayaran)
    {
        $header = $this->request->getServer('HTTP_AUTHORIZATION');
        $decoder = new JwtDecode();
        $user = $decoder->admin($header);

        $mi = new ModelInfaq();
        $mp = new ModelPembayaran();
        $tgl_sekarang = date('Y-m-d H:i:s');
        $validator = $user->sub; //dari token

        $bayar = $mp->select('*')->where(['nomor_pembayaran' => $nomor_pembayaran])->first();
        if (!$bayar) return $this->fail('Kode pembayaran infaq ' . $nomor_pembayaran . ' anda tidak ditemukan.', 400);
        $infaq = $mi->select('*')->where('kode', $bayar['kode_infaq'])->first();
        if (!$infaq) return $this->fail('Kode infaq ' . $bayar['kode_infaq'] . ' tidak ditemukan.', 400);
        if ($bayar['validator'] != null) return $this->fail('Pembayaran iuran sudah diterima oleh ' . $bayar['validator'] . '.', 400);
        $terbayar = (int) $bayar['bayar'] - (int) $infaq['nominal'];
        if ($terbayar < 0) {
            return $this->fail('Nominal iuran kurang dari ketentuan infaq ' . $infaq['acara'] . '. Kurang bayar Rp. ' . number_format($terbayar), 400);
        }

        $data = [
            'validator' => $validator,
            'tanggal_validasi' => $tgl_sekarang,
        ];

        $mp->set($data);
        $mp->where('nomor_pembayaran', $nomor_pembayaran);
        $update = $mp->update();
        if (!$update) return $this->fail('Gagal terima pembayaran infaq kode ' . $nomor_pembayaran . ' .', 400);

        $libMutasi = new LibMutasi();
        $mutasi = $libMutasi->transaksi('PI-' . time(), date('Y-m-d'), 'D', $bayar['bayar'], 'Penerimaan infaq nomor ' . $nomor_pembayaran, $validator);
        if (!$mutasi) return $this->fail('Infaq berhasil diterima namun gagal simpan pada mutasi.');
        return $this->respond(['pesan' => 'Pembayaran infaq Anda berhasil diterima oleh ' . $validator . '.']);
    }

    public function terimaUmum($kode)
    {
        $header = $this->request->getServer('HTTP_AUTHORIZATION');
        $decoder = new JwtDecode();
        $user = $decoder->admin($header);

        $mp = new ModelUmum();
        $tgl_sekarang = date('Y-m-d H:i:s');
        $validator = $user->sub; //dari token

        $bayar = $mp->select('*')->where(['kode' => $kode])->first();

        if (!$bayar) return $this->fail('Kode infaq umum ' . $kode . ' tidak ditemukan.', 400);
        if ($bayar['bukti'] == null) return $this->fail('Bukti pembayaran masih belum diupload oleh anggota.', 400);
        if ($bayar['validator'] != null) return $this->fail('Pembayran infq umum kode ' . $kode . ' sudah diterima oleh ' . $bayar['validator'] . '.', 400);

        $data = [
            'validator' => $validator,
            'tanggal_validasi' => $tgl_sekarang,
        ];

        $mp->set($data);
        $mp->where('kode', $kode);
        $update = $mp->update();
        if (!$update) return $this->fail('Gagal terima infaq umum nomor ' . $kode, 400);

        $libMutasi = new LibMutasi();
        $mutasi = $libMutasi->transaksi('PI-' . time(), date('Y-m-d'), 'D', $bayar['nominal'], 'Penerimaan infaq umum nomor ' . $kode, $validator);
        if (!$mutasi) return $this->fail('Infaq berhasil diterima namun gagal simpan pada mutasi.');
        return $this->respond(['pesan' => 'Pembayaran infaq umum kode ' . $kode . ' berhasil diterima oleh ' . $validator . '.']);
    }
}
