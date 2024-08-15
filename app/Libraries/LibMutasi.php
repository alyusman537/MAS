<?php

namespace App\Libraries;

use App\Models\ModelMutasi;

class LibMutasi {
    public function transaksi($nomor_mutasi, $tanggal, $jenis, $nominal, $keterangan, $admin)
    {
        $mm = new ModelMutasi();
        $data = [
            'tanggal' => $tanggal,
            'nomor_mutasi' => $nomor_mutasi,
            'jenis' => $jenis,
            'nominal' => $nominal,
            'admin' => $admin,
            'keterangan' => $keterangan
        ];
        $simpan = $mm->insert($data);
        return $simpan;
    }
}