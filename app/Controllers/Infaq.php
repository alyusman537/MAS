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
        $data = $mi->select('*')->where('aktif', '1')->orderBy(['kode' => 'DESC'])->findAll();
        return $this->respond($data);
    }
    public function byId($id = null)
    {
        $mi = new ModelInfaq();
        $data = $mi->select('*')->where('id', $id)->first();
        if (!$data) return $this->fail('Data infaq tidak ditemukan.', 400);
        return $this->respond($data);
    }

    public function new()
    {
        $data = [
            'acara' => null,
            'tanggal_acara' => null,
            'keterangan' => null,
            'nominal' => 0,
            'rutin' => true,
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

        if (!$this->validate($rules)) return $this->fail($this->validator->getErrors());
        $mi = new ModelInfaq();
        $json = $this->request->getJSON();
        $kode = time();
        $data = [
            'tanggal' => date('Y-m-d', $kode),
            'kode' => $kode,
            'acara' => $json->acara,
            'tanggl_acara' => $json->tanggal_acara,
            'keterangan' => $json->keterangan,
            'nominal' => $json->nominal,
            'rutin' => $json->rutin,
            'aktif' => true,
        ];
        try {
            $mi->insert($data);
            return $this->respondCreated($data);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage(), $th->getCode());
        }
    }

    public function edit($id)
    {
        $mi = new ModelInfaq();
        $infaq = $mi->select('*')->where('id', $id)->first();
        if (!$infaq) return $this->fail('Data infaq tidak ditemukan', 400);
        $data = [
            'data' => [
                'tanggal' => $infaq['tanggal'],
                'kode' => $infaq['kode'],
                'acara' => $infaq['acara'],
                'tanggl_acara' => $infaq['tanggal_acara'],
                'keterangan' => $infaq['keterangan'],
                'nominal' => $infaq['nominal'],
                'rutin' => $infaq['rutin'] == '1' ? true : false,
                'aktif' => $infaq['aktif'] == '1' ? true : false
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

        if (!$this->validate($rules)) return $this->fail($this->validator->getErrors());
        $mi = new ModelInfaq();
        $json = $this->request->getJSON();
        $data = [
            'acara' => $json->acara,
            'tanggl_acara' => $json->tanggal_acara,
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
            return $this->fail($th->getMessage(), $th->getCode());
        }
    }

    public function delete($id)
    {
        $mi = new ModelInfaq();
        $mp = new ModelPembayaran();
        $infaq = $mi->select('*')->find($id);
        if (!$infaq) return $this->fail('Data infaq tidak ditemukan.', 400);
        if ($infaq['aktif'] == '0') return $this->fail('Anda tidak bisa menonaktifkan data yang sudh dihapus.', 400);
        $data = [
            'aktif' => false
        ];
        try {
            $mi->set($data);
            $mi->where('id', $id);
            $mi->update();

            try {
                $mp->set(['aktif' => '0']);
                $mp->where('kode_infaq', $infaq['kode']);
                $mp->update();
                return $this->respond(['pesan' => 'Data infaq berhasil dihapus.']);
            } catch (\Throwable $th) {
                return $this->fail($th->getMessage(), $th->getCode());
            }
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage(), $th->getCode());
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

        if (!$this->validate($rules)) return $this->fail($this->validator->getErrors());
        $mi = new ModelInfaq();
        $mw = new ModelWilayah();
        $ma = new ModelAnggota();
        $mp = new ModelPembayaran();
        $json = $this->request->getJSON();

        $infaq = $mi->select('*')->where('kode', $json->kode_infaq)->first();
        if (!$infaq) return $this->fail('Kode Acara ' . $json->kode . ' belum dibuat.', 400);
        if ($infaq['status'] == '0') return $this->fail('Acara ' . $infaq['acara'] . ' sudah dinonaktifkan', 400);
        if ($infaq['tanggal_acara'] > date('Y-m-d')) return $this->fail('Tanggal acara ' . $infaq['acara'] . ' sudah terlewat', 400);
        $wilayah = $mw->select('*')->where('kode', $json->wilayah)->first();
        if (!$wilayah) return $this->fail('Kode wilayah' . $json->wilayah . ' tidak ditemukan', 400);
        if ($wilayah['aktif'] == '0') return $this->fail('Kode wilayah ' . $json->wilayah . ' sudah dinonaktifkan', 400);

        $anggota = $ma->select('*')->where(['wilayah' => $json->wilayah, 'aktif' => 'aktif'])->findAll();
        $iuran = [];
        $tanggal = date('Y-m-d');
        foreach ($anggota as $key => $val) {
            $dorong = [
                'tanggal' => $tanggal,
                'nomor_pembayaran' => time() . '-' . $val['nia'],
                'kode_infaq' => $json->kode_infaq,
                'nia' => $val['nia'],
            ];
            $iuran[] = $dorong;
            $mp->ignore(true)->insert($dorong);
        }
        return $this->respond('Kode infaq ' . $json->kode . ' berhasil digenerate ke wilayah ' . $wilayah['keterangan']);
    }
}
