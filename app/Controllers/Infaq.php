<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

use App\Models\ModelAnggota;
use App\Models\ModelWilayah;
use App\Models\ModelInfaq;
use App\Models\ModelPembayaran;

use App\Libraries\JwtDecode;

class Infaq extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        $mi = new ModelInfaq();
        $data = $mi->select('*')->where('aktif', '1')->orderBy('tanggal_acara', 'DESC')->limit(50)->findAll();
        // if(!$data) return $this->respond([]);;
        return $this->respond($data);
    }
    public function byId($id = null)
    {
        $mi = new ModelInfaq();
        $data = $mi->select('*')->where('id', $id)->first();
        if (!$data) return $this->fail('Data infaq tidak ditemukan.', 402);
        return $this->respond($data);
    }

    public function new()
    {
        $data = [
            'acara' => null,
            'tanggal_acara' => null,
            'keterangan' => null,
            'nominal' => 0,
            'rutin' => '1',
        ];
        return $this->respond($data);
    }

    public function add()
    {
        helper(['form']);
        $rules = [
            'acara'          => [
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                ],
            ],
            'tanggal_acara'          => [
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                ],
            ],
            'keterangan'      => [
                'rules' => 'required|min_length[4]|max_length[300]',
                'errors' => [
                    'required' => '{field} tidak boleh kosong',
                    'min_length' => '{field} tidak boleh kurang dari 4 karakter.',
                    'max_length' => '{field} tidak boleh lebih dari 300 karakter.',
                ]
            ],
            'nominal'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} tidak boleh kosong',
                ]
            ],
        ];

        if (!$this->validate($rules)) return $this->fail($this->validator->getErrors(), 409);
        $mi = new ModelInfaq();
        $json = $this->request->getJSON();
        if ((int) $json->nominal < 1000) return $this->fail('Nominal infaq tidak boleh kurang dari Rp. 1.000 rupiah.', 402);
        $kode = time();
        $rutin = $json->rutin; // == 'ya' ? true : false;
        $tanggal_acara = $json->tanggal_acara;
        if ($tanggal_acara < date('Y-m-d')) return $this->fail('Tanggal acara tidak boleh lebih kecil dari tanggal sekarang', 402);
        $data = [
            'tanggal' => date('Y-m-d', $kode),
            'kode' => $kode,
            'acara' => $json->acara,
            'tanggal_acara' => $tanggal_acara,
            'keterangan' => $json->keterangan,
            'nominal' => $json->nominal,
            'rutin' => $rutin,
            'aktif' => true,
        ];
        try {
            $mi->insert($data);
            return $this->respondCreated($data);
        } catch (\Throwable $th) {
            return $this->fail(json_encode($th->getMessage()), 500);
        }
    }

    public function edit($id)
    {
        $mi = new ModelInfaq();
        $infaq = $mi->select('*')->where('id', $id)->first();
        if (!$infaq) return $this->fail('Data infaq tidak ditemukan', 402);
        $data = [
            'data' => [
                'id' => $infaq['id'],
                'tanggal' => $infaq['tanggal'],
                'kode' => $infaq['kode'],
                'acara' => $infaq['acara'],
                'tanggal_acara' => $infaq['tanggal_acara'],
                'keterangan' => $infaq['keterangan'],
                'nominal' => $infaq['nominal'],
                'rutin' => $infaq['rutin'],
                'aktif' => $infaq['aktif']
            ],
            'ubah' => [
                'acara' => $infaq['acara'],
                'tanggl_acara' => $infaq['tanggal_acara'],
                'keterangan' => $infaq['keterangan'],
                'nominal' => $infaq['nominal'],
                'rutin' => $infaq['rutin'],
            ],
        ];
        return $this->respond($data);
    }

    public function update($id)
    {
        helper(['form']);
        $rules = [
            'kode'          => [
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                ],
            ],
            'acara'          => [
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                ],
            ],
            'tanggal_acara'          => [
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                ],
            ],
            'keterangan'      => [
                'rules' => 'required|min_length[4]|max_length[300]',
                'errors' => [
                    'required' => '{field} tidak boleh kosong',
                    'min_length' => '{field} tidak boleh kurang dari 4 karakter.',
                    'max_length' => '{field} tidak boleh lebih dari 300 karakter.',
                ]
            ],
            'nominal'  => [
                'rules' => 'required',
                'errors' => [
                    'matches' => '{field} tidak boleh kosong.'
                ]
            ],
            'rutin'  => [
                'rules' => 'required',
                'errors' => [
                    'matches' => '{field} harus berupa true false.'
                ]
            ],
        ];

        if (!$this->validate($rules)) return $this->fail($this->validator->getErrors(), 409);
        $mi = new ModelInfaq();
        $cek = $mi->select('*')->find($id);
        if (!$cek) return $this->fail('ID acara tidak ditemukan.', 402);
        $json = $this->request->getJSON();
        if ($cek['kode'] != $json->kode) return $this->fail('ID dan KODE acara tidak cocok.', 402);
        $data = [
            'acara' => $json->acara,
            'tanggal_acara' => $json->tanggal_acara,
            'keterangan' => $json->keterangan,
            'nominal' => $json->nominal,
            'rutin' => $json->rutin,
        ];
        try {
            $mi->set($data);
            $mi->where('id', $id);
            $mi->update();
            return $this->respond($data);
        } catch (\Throwable $th) {
            return $this->fail(json_encode($th->getMessage()), 500);
        }
    }

    public function delete($id)
    {
        $mi = new ModelInfaq();
        $mp = new ModelPembayaran();
        $infaq = $mi->select('*')->find($id);
        if (!$infaq) return $this->fail('Data infaq tidak ditemukan.', 402);
        if ($infaq['aktif'] == '0') return $this->fail('Anda tidak bisa menonaktifkan data yang sudah dihapus.', 402);
        $data = [
            'aktif' => '0'
        ];
        $mi->set($data);
        $mi->where('id', $id);
        $deleteInfaq = $mi->update();
        if ($deleteInfaq) {
            $bayar = $mp->select('*')->where('kode_infaq', $infaq['kode'])->first();
            if ($bayar) {
                $mp->set(['aktif' => '0']);
                $mp->where('kode_infaq', $infaq['kode']);
                $mp->update();
                return $this->respond(['pesan' => 'Data infaq berhasil dihapus.']);
            }
            return $this->respond(['pesan' => 'Data infaq berhasil dihapus.']);
        } else {
            return $this->fail('Data infaq tidak berhasil dihapus.', 402);
        }
    }

    public function generate()
    {
        helper(['form']);
        $rules = [
            'kode_infaq'          => [
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                ],
            ],
            'wilayah'          => [
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                ],
            ],
        ];

        if (!$this->validate($rules)) return $this->fail($this->validator->getErrors(), 409);
        $mi = new ModelInfaq();
        $mw = new ModelWilayah();
        $ma = new ModelAnggota();
        $mp = new ModelPembayaran();
        $json = $this->request->getJSON();

        $infaq = $mi->select('*')->where('kode', $json->kode_infaq)->first();
        if (!$infaq) return $this->fail('Kode Acara ' . $json->kode_infaq . ' belum dibuat.', 402);
        if ($infaq['aktif'] == '0') return $this->fail('Acara ' . $infaq['acara'] . ' sudah dinonaktifkan', 402);
        if ($infaq['tanggal_acara'] < date('Y-m-d')) return $this->fail('Tanggal acara ' . $infaq['acara'] . ' sudah terlewat', 402);
        $wilayah = $mw->select('*')->where('kode', $json->wilayah)->first();
        if (!$wilayah) return $this->fail('Kode wilayah' . $json->wilayah . ' tidak ditemukan', 402);
        if ($wilayah['aktif'] == '0') return $this->fail('Kode wilayah ' . $json->wilayah . ' sudah dinonaktifkan', 402);

        $anggota = $ma->select('*')->where(['wilayah' => $json->wilayah, 'aktif' => 'aktif'])->findAll();
        $tanggal = date('Y-m-d');
        foreach ($anggota as $key => $val) {
            $cek_tagihan = $mp->select('*')->where(['kode_infaq' => $json->kode_infaq, 'nia' => $val['nia']])->first();
            if (!$cek_tagihan) {
                $dorong = [
                    'tanggal' => $tanggal,
                    'nomor_pembayaran' => $json->kode_infaq . '-' . $val['nia'],
                    'kode_infaq' => $json->kode_infaq,
                    'nia' => $val['nia'],
                ];
                $mp->insert($dorong);
            }
            continue;
        }
        return $this->respond(['pesan' => 'Tagihan infaq ' . $infaq['acara'] . ' berhasil digenerate ke wilayah ' . $wilayah['keterangan']]);
    }

    public function generateSemua($kode_infaq)
    {
        $mi = new ModelInfaq();
        $ma = new ModelAnggota();
        $mp = new ModelPembayaran();

        $infaq = $mi->select('*')->where('kode', $kode_infaq)->first();
        if (!$infaq) return $this->fail('Kode Acara ' . $kode_infaq . ' belum dibuat.', 402);
        if ($infaq['aktif'] == '0') return $this->fail('Acara ' . $infaq['acara'] . ' sudah dinonaktifkan', 402);
        if ($infaq['tanggal_acara'] < date('Y-m-d')) return $this->fail('Tanggal acara ' . $infaq['acara'] . ' sudah terlewat', 402);

        $anggota = $ma->select('*')->where(['aktif' => 'aktif'])->where('nia <> 0537')->findAll();
        $tanggal = date('Y-m-d');
        foreach ($anggota as $key => $val) {
            $cek_tagihan = $mp->select('*')->where(['kode_infaq' => $kode_infaq, 'nia' => $val['nia']])->first();
            if (!$cek_tagihan) {
                $dorong = [
                    'tanggal' => $tanggal,
                    'nomor_pembayaran' => $kode_infaq . '-' . $val['nia'],
                    'kode_infaq' => $kode_infaq,
                    'nia' => $val['nia'],
                ];
                $mp->insert($dorong);
            }
            continue;
        }
        return $this->respond(['pesan' => 'Tagihan infaq ' . $infaq['acara'] . ' berhasil digenerate ke semua anggota.']);
    }
}
