<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

use App\Models\ModelInfaq;
use App\Models\ModelPembayaran;
use App\Models\ModelAnggota;

use App\Libraries\JwtDecode;
use App\Libraries\LibFonnte;

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
        if (!$this->validate($rules)) return $this->fail($this->validator->getErrors(), 409);

        $mi = new ModelInfaq();
        $mp = new ModelPembayaran();
        $json = $this->request->getJSON();
        $tanggal_bayar = $json->tanggal_bayar;
        $nominal_pembayaran = (int) $json->bayar;

        if ($tanggal_bayar > date('Y-m-d')) return $this->fail('Tanggal pembayaran yagn anda pilih tidak boleh melebihi tanggal sekarang.', 402);

        $bayar = $mp->select('*')->where(['nomor_pembayaran' => $nomor_pembayaran])->first();
        if (!$bayar) return $this->fail('Kode pembayaran infaq ' . $nomor_pembayaran . ' anda tidak ditemukan.', 402);
        if ($bayar['nia'] != $nia) return $this->fail('Anda tidak berhak membayar nomor pembayaran infaq ' . $nomor_pembayaran . ' ini.', 402);
        if ($bayar['bukti_bayar'] == null) return $this->fail('Silahkan masukkan bukti pembayaran Anda', 402);

        $infaq = $mi->select('*')->where('kode', $bayar['kode_infaq'])->first();
        if (!$infaq) return $this->fail('Kode infaq ' . $bayar['kode_infaq'] . ' tidak ada.', 402);
        if ($infaq['aktif']  != '1') return $this->fail('Kode infaq ' . $bayar['kode_infaq'] . ' telah dihapus oleh admin.', 402);
        // return $this->respond($infaq);
        $nominal_infaq = (int) $infaq['nominal'];
        if ($nominal_pembayaran < $nominal_infaq) return $this->fail('Nominal pembayaran anda kurang dari tagihan iuran', 402);
        if ((int) $bayar['bayar'] >= $nominal_infaq && $bayar['validator'] == null) return $this->fail('Kode infaq Anda sudah terbayar namun belum diterima oleh admin. silahkan hubungi admin untuk konfirmasi.', 402);

        $data = [
            'bayar' => $nominal_pembayaran,
            'tanggal_bayar' => $tanggal_bayar,
            // 'bukti_bayar' => $json->bukti_bayar
        ];

        try {
            $mp->set($data);
            $mp->where(['nomor_pembayaran' => $nomor_pembayaran]);
            $mp->update();

            $fonnte = new LibFonnte();
            $ma = new ModelAnggota();
            $admin = $ma->select('wa')->where(['level' => 'admin'])->findAll();
            $nomoradmin = [];
            foreach ($admin as $key => $val) {
                $nomoradmin [] = $val['wa'];
            }
            $nomor = implode(",", $nomoradmin);
            $pesan = 'Mohon segera terima pembayaran infaq *'.$infaq['acara'].'* dari Nomor anggoa *'.$bayar['nia'].'*'."

Al-wafa Bi'ahdillah.";
            $kirim = $fonnte::kirimPesan($nomor, $pesan);
            // return print_r($kirim);

            return $this->respond(['pesan' => 'Pembayaran infaq Anda berhasil dilakukan.']);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage(), 500);
        }
    }

    public function buktiBayar($nomor_pembayaran)
    {
        $header = $this->request->getServer('HTTP_AUTHORIZATION');
        $decoder = new JwtDecode();
        $user = $decoder->decoder($header);
        $nia = $user->sub; //dari token

        helper(['form', 'url']);
        $validationRule = [
            'bukti' => [
                // 'label' => 'Image File',
                'rules' => [
                    'uploaded[bukti]',
                    'is_image[bukti]',
                    'mime_in[bukti,image/jpg,image/jpeg,image/png]',
                    'max_size[bukti,2048]',
                    // 'max_dims[userfile,1024,768]',
                ],
                'errors' => [
                    'uploaded' => 'tidak ada gambar yagn diupload',
                    'is_image' => 'file harus berupa gambar',
                    'mime_in' => 'gambar harus berupa jpg atau jpeg',
                    'max_size' => 'ukurang gambar harus kurang dari 2mb'
                ]
            ],
        ];
        if (! $this->validateData([], $validationRule)) {
            return $this->fail($this->validator->getErrors(), 402);
        }
        $mm = new ModelPembayaran();

        $fotoLama = $mm->select('*')->where('nomor_pembayaran', $nomor_pembayaran)->first();
        if($fotoLama['nia'] != $nia) return $this->fail('Anda tidak berhak upload bukti bayar orang lain.', 402);

        $foto = isset($fotoLama['bukti_bayar']) ? $fotoLama['bukti_bayar'] : false;
        $path_ori = WRITEPATH . 'uploads/bukti/' . $foto;

        $x_file = $this->request->getFile('bukti');
        $namaFoto = $x_file->getRandomName();

        // $x_file->move(WRITEPATH . 'uploads/bukti', $namaFoto);
        $image = service('image');
        $image->withFile($x_file)
            ->resize(500, 500, true, 'height')
            ->save(WRITEPATH . '/uploads/bukti/' . $namaFoto);

        unlink($x_file);

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

    
}
