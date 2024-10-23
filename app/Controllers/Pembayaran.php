<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

use App\Models\ModelInfaq;
use App\Models\ModelPembayaran;
use App\Models\ModelAnggota;

use App\Libraries\JwtDecode;
use App\Libraries\LibFonnte;
use App\Libraries\PdfGenerator;
use CodeIgniter\Database\BaseBuilder;

class Pembayaran extends BaseController
{
    use ResponseTrait;
    private $db;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
    public function index()
    {
        //
    }

    public function bayar($nomor_pembayaran)
    {
        $header = $this->request->getServer('HTTP_AUTHORIZATION');
        $decoder = new JwtDecode();
        $user = $decoder->decoder($header);
        $nia = strtoupper($user->sub); //dari token

        helper(['form', 'url']);
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
        if (!$this->validate($rules)) return $this->fail($this->validator->getErrors(), 409);
        // if (!$this->validate($rules)) return $this->fail($this->validator->getErrors(), 409);

        $mi = new ModelInfaq();
        $mp = new ModelPembayaran();
        // $json = $this->request->getJSON();
        $tanggal_bayar = $this->request->getVar("tanggal_bayar");
        // return print_r($tanggal_bayar);
        $nominal_pembayaran = (int) $this->request->getVar("bayar");
        $x_file = $this->request->getFile('bukti');

        if ($tanggal_bayar > date('Y-m-d')) return $this->fail('Tanggal pembayaran yagn anda pilih tidak boleh melebihi tanggal sekarang.', 402);

        $bayar = $mp->select('*')->where(['nomor_pembayaran' => $nomor_pembayaran])->first();
        // return print_r($bayar);
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

        

        try {
            $fotoLama = $mp->select('*')->where('nomor_pembayaran', $nomor_pembayaran)->first();
            if ($fotoLama['nia'] != $nia) return $this->fail('Anda tidak berhak upload bukti bayar orang lain.', 402);

            $foto = isset($fotoLama['bukti_bayar']) ? $fotoLama['bukti_bayar'] : false;
            $path_ori = WRITEPATH . 'uploads/bukti/' . $foto;

            
            $namaFoto = $x_file->getRandomName();

            // $x_file->move(WRITEPATH . 'uploads/bukti', $namaFoto);
            $image = service('image');
            $image->withFile($x_file)
                ->resize(500, 500, true, 'height')
                ->save(WRITEPATH . '/uploads/bukti/' . $namaFoto);

            unlink($x_file);

            $data_bayar = [
                'bayar' => $nominal_pembayaran,
                'tanggal_bayar' => $tanggal_bayar,
                'bukti_bayar' => $namaFoto
            ];

            // $mp->set(['bukti_bayar' => $namaFoto]);
            // $mp->where('nomor_pembayaran', $nomor_pembayaran);
            // $mp->update();

            if ($foto) {
                if (file_exists($path_ori)) {
                    unlink($path_ori);
                }
            }
            $mp->set($data_bayar);
            $mp->where(['nomor_pembayaran' => $nomor_pembayaran]);
            $mp->update();

            $fonnte = new LibFonnte();
            $ma = new ModelAnggota();
            $admin = $ma->select('wa')->where(['level' => 'admin'])->findAll();
            $nomoradmin = [];
            foreach ($admin as $key => $val) {
                $nomoradmin[] = $val['wa'];
            }
            $nomor = implode(",", $nomoradmin);
            $pesan = 'Mohon segera terima pembayaran infaq *' . $infaq['acara'] . '* dari Nomor anggoa *' . $bayar['nia'] . '*' . "

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
        if ($fotoLama['nia'] != $nia) return $this->fail('Anda tidak berhak upload bukti bayar orang lain.', 402);

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

    public function listInfaq()
    {
        $mi = new ModelInfaq();
        try {
            $data = $mi->whereIn('kode', function (BaseBuilder $builder) {
                $builder->select('kode_infaq')->distinct()->from('pembayaran')->where('validator IS NULL');
            });
            $dataInfaq = $data->get()->getResult();
            return $this->respond($dataInfaq);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage(), 500);
        }
    }

    public function listInfaqBelumLunas($kode_infaq)
    {
        $mi = new ModelInfaq();
        $mp = new ModelPembayaran();
        $infaq = $mi->select('*')->where(['kode' => $kode_infaq])->first();
        $bayar = $mp->listBayar($kode_infaq);
        $data_bayar = [];
        foreach ($bayar as $key => $val) {
            $is_lunas = (int) $val->bayar > 0 ? 'Pending' : ((int) $val->bayar > 0 && $val->validator != null ? 'Lunas' : 'Belum Bayar');
            $dorong = [
                'nia' => $val->nia,
                'nama' => $val->nama,
                'wilayah' => $val->wilayah,
                'bayar' => $val->bayar,
                'tanggal_bayar' => $val->tanggal_bayar,
                'validator' => $val->validator,
                'tanggal_validasi' => $val->tanggal_validasi,
                'is_lunas' => $is_lunas
            ];
            $data_bayar[] = $dorong;
        }
        $data = [
            'infaq' => $infaq,
            'data_bayar' => $data_bayar
        ];
        return $this->respond($data);
    }

    public function pdfKartuInfaq($kode_infaq)
    {
        $mi = new ModelInfaq();
        $mp = new ModelPembayaran();
        $infaq = $mi->select('*')->where(['kode' => $kode_infaq])->first();
        $bayar = $mp->listBayar($kode_infaq);
        $data_bayar = [];
        foreach ($bayar as $key => $val) {
            $is_lunas = (int) $val->bayar > 0 ? 'Pending' : ((int) $val->bayar > 0 && $val->validator != null ? 'Lunas' : 'Belum');
            $dorong = [
                'nia' => $val->nia,
                'nama' => $val->nama,
                'wilayah' => $val->wilayah,
                'bayar' => $val->bayar,
                'tanggal_bayar' => $val->tanggal_bayar,
                'validator' => $val->validator,
                'tanggal_validasi' => $val->tanggal_validasi,
                'is_lunas' => $is_lunas
            ];
            $data_bayar[] = $dorong;
        }
        $data = [
            'infaq' => $infaq,
            'data_bayar' => $data_bayar
        ];

        $Pdfgenerator = new PdfGenerator();
        // filename dari pdf ketika didownload
        $file_pdf = 'Data-anggota';
        // setting paper
        $paper = 'A4';
        //orientasi paper potrait / landscape
        $orientation = "landscape";

        $html = view('pdf/pdfKartuInfaq', $data);

        // run dompdf
        $Pdfgenerator->generate($html, $file_pdf, $paper, $orientation);
    }
}
