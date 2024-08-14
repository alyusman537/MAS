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
        $header = $this->request->getServer('HTTP_AUTHORIZATION');
        $decoder = new JwtDecode();
        $user = $decoder->decoder($header);
        $nia = $user->sub; //dari token

        helper(['form']);
        $rules = [
            'bayar'          => [
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                ],
            ],
            'tanggal_bayar'          => [
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                ],
            ],
        ];
        if (!$this->validate($rules)) return $this->fail($this->validator->getErrors());

        $mi = new ModelInfaq();
        $mp = new ModelPembayaran();
        $json = $this->request->getJSON();
        $tanggal_bayar = $json->tanggal_bayar;
        $nominal_pembayaran = (int) $json->bayar;

        if ($tanggal_bayar > date('Y-m-d')) return $this->fail('Tanggal pembayaran yagn anda pilih tidak boleh melebihi tanggal sekarang.', 400);

        $bayar = $mp->select('*')->where(['nomor_pembayaran' => $nomor_pembayaran])->first();
        if (!$bayar) return $this->fail('Kode pembayaran infaq ' . $nomor_pembayaran . ' anda tidak ditemukan.', 400);
        if ($bayar['nia'] != $nia) return $this->fail('Anda tidak berhak membayar nomor pembayaran infaq ' . $nomor_pembayaran . ' ini.', 400);

        $infaq = $mi->select('*')->where('kode', $bayar['kode_infaq'])->first();
        if (!$infaq) return $this->fail('Kode infaq ' . $bayar['kode_infaq'] . ' tidak ada.', 400);
        if ($infaq['aktif']  != '1') return $this->fail('Kode infaq ' . $bayar['kode_infaq'] . ' telah dihapus oleh admin.', 400);
        // return $this->respond($infaq);
        $nominal_infaq = (int) $infaq['nominal'];
        if ($nominal_pembayaran < $nominal_infaq) return $this->fail('Nominal pembayaran anda kurang dari tagihan iuran', 400);
        if ((int) $bayar['bayar'] >= $nominal_infaq && $bayar['validator'] == null) return $this->fail('Kode infaq Anda sudah terbayar namun belum diterima oleh admin. silahkan hubungi admin untuk konfirmasi.', 400);

        $data = [
            'bayar' => $nominal_pembayaran,
            'tanggal_bayar' => $tanggal_bayar,
            // 'bukti_bayar' => $json->bukti_bayar
        ];

        try {
            $mp->set($data);
            $mp->where(['nomor_pembayaran' => $nomor_pembayaran]);
            $mp->update();
            return $this->respond(['pesan' => 'Pembayaran infaq Anda berhasil dilakukan.']);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage(), $th->getCode());
        }
    }

    public function buktiBayar($nomor_pembayaran)
    {
        helper(['form', 'url']);
        $validationRule = [
            'bukti' => [
                // 'label' => 'Image File',
                'rules' => [
                    'uploaded[bukti]',
                    'is_image[bukti]',
                    'mime_in[bukti,image/jpg,image/jpeg,image/png]',
                    'max_size[bukti,4096]',
                    // 'max_dims[userfile,1024,768]',
                ],
                'errors' => [
                    'uploaded' => 'tidak ada gambar yagn diupload',
                    'is_image' => 'file harus berupa gambar',
                    'mime_in' => 'gambar harus berupa jpg atau jpeg',
                    'max_size' => 'ukurang gambar harus kurang dari 4mb'
                ]
            ],
        ];
        if (! $this->validateData([], $validationRule)) {
            return $this->fail($this->validator->getErrors());

        }
        $mm = new ModelPembayaran();

        $fotoLama = $mm->select('*')->where('nomor_pembayaran', $nomor_pembayaran)->first();

        $foto = isset($fotoLama['bukti_bayar']) ? $fotoLama['bukti_bayar'] : false;
        // return $this->respond(['res' => $foto]);

        $path_ori = WRITEPATH . 'uploads/bukti/' . $foto;
        // $path_thumb = WRITEPATH . 'uploads/thumbnail/' . $foto;

        $x_file = $this->request->getFile('bukti');
        $namaFoto = $x_file->getRandomName();

        $x_file->move(WRITEPATH . 'uploads/bukti', $namaFoto);

        $mm->set(['bukti_bayar' => $namaFoto]);
        $mm->where('nomor_pembayaran', $nomor_pembayaran);
        $mm->update();

        if ($foto) {
            if (file_exists($path_ori)) {
                unlink($path_ori);
            }
        }

        return $this->respond(['image' => $namaFoto]);
    }

    public function terima($nomor_pembayaran)
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
}
