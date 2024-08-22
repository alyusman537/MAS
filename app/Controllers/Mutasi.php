<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

use App\Models\ModelMutasi;

use App\Libraries\JwtDecode;
use App\Libraries\LibMutasi;

class Mutasi extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        //
    }

    public function saldoAkhir()
    {
        $mm = new ModelMutasi();
        $date = date('Y-m-d');
        $tglAwal = date('Y-m-d', strtotime($date . ' + 1 days')); 
        $plus = $mm->selectSum('nominal')->where('tanggal < "' . $tglAwal . '" ')->where('jenis', 'D')->first();
        $minus = $mm->selectSum('nominal')->where('tanggal < "' . $tglAwal . '" ')->where('jenis', 'K')->first();
        $plus = !$plus ? 0 : (int) $plus['nominal'];
        $minus = !$minus ? 0 : (int) $minus['nominal'];
        $saldoAkhir = $plus - $minus;
        $data = [
            'pemasukan' => $plus,
            'pengeluaran' => $minus,
            'saldo_akhir' => $saldoAkhir,
            'tanggal' => $date
        ];
        return $this->respond($data);
    }

    public function saldoBulan()
    {
        $mm = new ModelMutasi();
        $date = date('m');
        $bulanSekarang = date('m');
        $bulanLalu= date("Y-n-j", strtotime("last day of previous month"));

        $saldoAwalPlus = $mm->selectSum('nominal')->where('substring(tanggal, 5, 2) = "' . $bulanSekarang . '" ')->where('jenis', 'D')->first();
        $saldoAwalMinus = $mm->selectSum('nominal')->where('substring(tanggal, 5, 2) = "' . $bulanSekarang . '" ')->where('jenis', 'K')->first();

        $saldoAwalJadi = (int) $saldoAwalPlus['nominal'] - (int) $saldoAwalMinus['nominal'];
        $plus = !$saldoAwalPlus ? 0 : (int) $saldoAwalPlus['nominal'];
        $minus = !$saldoAwalMinus ? 0 : (int) $saldoAwalMinus['nominal'];
        $saldoAkhir = $plus - $minus;
        $data = [
            'saldo_awal' => $saldoAwalJadi,
            'pemasukan' => $plus,
            'pengeluaran' => $minus,
            'saldo_akhir' => $saldoAkhir,
            'tanggal' => $date
        ];
        return $this->respond($data);
    }

    public function list($tglAwal, $tglAkhir)
    {
        $mm = new ModelMutasi();
        // $date = $tglAwal;
        // $newDate = date('Y-m-d', strtotime($date . ' - 1 days')); 
        $plus = $mm->selectSum('nominal')->where('tanggal < "' . $tglAwal . '" ')->where('jenis', 'D')->first();
        $minus = $mm->selectSum('nominal')->where('tanggal < "' . $tglAwal . '" ')->where('jenis', 'K')->first();
        $plus = !$plus ? 0 : (int) $plus['nominal'];
        $minus = !$minus ? 0 : (int) $minus['nominal'];
        $saldoAwal = $plus - $minus;
        $saldo = $saldoAwal;
        // return print_r($saldoAwal);
        $list = $mm->list($tglAwal, $tglAkhir);
        $mutasi = [];
        foreach ($list as $key => $val) {
            $debet = 0;
            $kredit = 0;
            if($val->jenis == 'D') {
                $debet = (int) $val->nominal;
                $saldo = $saldo + $debet;
            } else {
                $kredit = (int) $val->nominal;
                $saldo = $saldo - $kredit;
            }
            $dorong = [
                'tanggal'  => $val->tanggal,
                'nomor' => $val->nomor_mutasi,
                'debet' => $debet,
                'kredit' => $kredit,
                'saldo' => $saldo,
                'keterangan' => $val->keterangan,
                'admin' => $val->admin,
            ];
            $mutasi [] = $dorong;
        }
        return $this->respond(['saldo_awal' => $saldoAwal, 'mutasi' => $mutasi,
    'saldo_akhir' => $saldo]);
    }

    public function detail($nomor_mutasi)
    {
        $mm = new ModelMutasi();
        $data = $mm->detail($nomor_mutasi);
        if (!$data) return $this->fail('Data mutasi nomor ' . $nomor_mutasi . ' tidak ada.');
        return $this->respond($data);
    }

    public function new()
    {
        $data = [
            'tanggal' => date('Y-m-d'),
            'jenis' => null,
            'nominal' => 0,
            'keterangan' => null
        ];
        return $this->respond($data);
    }

    public function add()
    {
        $header = $this->request->getServer('HTTP_AUTHORIZATION');
        $decoder = new JwtDecode();
        $admin = $decoder->admin($header);

        $json = $this->request->getJSON();
        // $mm = new ModelMutasi();

        helper(['form']);
        $rules = [
            'tanggal'         => [
                'rules' =>  'required',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                ]
            ],
            'jenis'          => [
                'rules'  => 'required',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                    'min_length' => '{field} tidak boleh kurang dari 4 karakter',
                    'max_length' => '{field} tidak boleh lebih dari 20 karakter'
                ]
            ],
            'nominal'         => [
                'rules' =>  'required',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                ]
            ],
            'keterangan'         => [
                'rules' =>  'required|min_length[4]|max_length[300]',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                    'min_length' => '{field} tidak boleh kurang dari 4 karakter',
                    'max_length' => '{field} tidak boleh lebih dari 300 karakter'
                ]
            ],
        ];

        if (!$this->validate($rules)) return $this->fail($this->validator->getErrors());

        $jenis = $json->jenis;
        // if ($jenis != 'D' || $jenis != 'K') return $this->fail('Nilai jenis mutasi harus berupa D atau K', 400);
        $prefix = $jenis == 'D' ? 'MD' : 'MK';
        $nominal = $json->nominal;
        if (!is_numeric($nominal)) return $this->fail('Nilai nominal harus berupa angka.', 400);
        $nomor = $prefix . '-' . time();

        $libMutasi = new LibMutasi();
        $simpan = $libMutasi->transaksi($nomor, $json->tanggal, $jenis, $nominal, $json->keterangan, $admin->sub);

        if (!$simpan) return $this->fail('Gagal simpan mutasi', 400);
        return $this->respondCreated(['pesan' => 'Mutasi transaksi berhasil disimpan.']);
    }

}
