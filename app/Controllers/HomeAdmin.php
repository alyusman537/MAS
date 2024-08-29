<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

use App\Models\ModelAnggota;
use App\Models\ModelMutasi;
use App\Models\ModelPembayaran;
use App\Models\ModelUmum;

class HomeAdmin extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        $mm = new ModelMutasi();
        $date = date('m');
        $akhirBulanLalu= date("Y-n-j", strtotime("last day of previous month"));
        
        $plusBulanLalu = $mm->selectSum('nominal')->where('tanggal <= "' . $akhirBulanLalu . '" ')->where('jenis', 'D')->first();
        $minusBulanLalu = $mm->selectSum('nominal')->where('tanggal <= "' . $akhirBulanLalu . '" ')->where('jenis', 'K')->first();
        
        $jmlPlus = !$plusBulanLalu ? 0 : (int) $plusBulanLalu['nominal'];
        $jmlMinus = !$minusBulanLalu ? 0 : (int) $minusBulanLalu['nominal'];
        $saldoBulanLalu = $jmlPlus - $jmlMinus;
        
        $bulanSekarang = date('m');
        $saldoPlus = $mm->selectSum('nominal')->where('month(tanggal) = "' . $bulanSekarang . '" ')->where('jenis', 'D')->first();
        $saldoMinus = $mm->selectSum('nominal')->where('month(tanggal) = "' . $bulanSekarang . '" ')->where('jenis', 'K')->first();

        $plus = !$saldoPlus ? 0 : (int) $saldoPlus['nominal'];
        $minus = !$saldoMinus ? 0 : (int) $saldoMinus['nominal'];
        $saldoAkhir = $plus - $minus;

        $saldo = [
            'saldo_bulan_lalu' => $saldoBulanLalu,
            'pemasukan_bulan_ini' => $plus,
            'pengeluaran_bulan_ini' => $minus,
            'saldo_akhir_bulan_ini' => $saldoAkhir,
        ];

        ////////////
        $ma = new ModelAnggota();
        $anggota = $ma->selectCount('nia')->where(['aktif' => 'aktif'])->first();

        ///////////////
        $mp = new ModelPembayaran();
        $belum = $mp->selectCount('nia')->where(['bayar' => 0, 'aktif' => 1])->where('validator IS NULL')->first();
        $pending = $mp->selectCount('nia')->where('bayar > 0')->where('validator IS NULL')->first();
        ///////////////////////
        $mu = new Modelumum();
        $umum = $mu->selectCount('kode')->where('validator IS NULL')->first();
        ///////////////////////
        $data = [
            'saldo' => $saldo,
            'anggota' => !$anggota ? 0 : (int) $anggota['nia'],
            'belum' => !$belum ? '0' : (int) $belum ['nia'],
            'pending' => !$pending ? '0' : (int) $pending ['nia'],
            'umum' => !$umum ? '0' : (int) $umum ['kode'],
        ];
        return $this->respond($data);
    }
}
