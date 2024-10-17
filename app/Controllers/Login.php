<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

use App\Models\ModelAnggota;
use App\Models\ModelOtp;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use App\Libraries\LibFonnte;

class Login extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        //
    }

    public function auth()
    {
        helper(['form']);
        $rules = [
            'nia'          => [
                'rules'  => 'required|min_length[4]|max_length[20]',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                    'min_length' => '{field} tidak boleh kurang dari 4 karakter',
                    'max_length' => '{field} tidak boleh lebih dari 20 karakter'
                ]
            ],
            'password'         => [
                'rules' =>  'required|min_length[4]',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                    'min_length' => '{field} tidak boleh kurang dari 4 karakter'
                ]
            ],
        ];

        if (!$this->validate($rules)) return $this->fail($this->validator->getErrors(), 409);

        $ma = new ModelAnggota();

        $json = $this->request->getJSON();
        $nia = $json->nia;
        $password = $json->password;

        $user = $ma->where('nia', $nia)->first();

        if (is_null($user)) {
            return $this->respond(['error' => 'NIA yang Anda masukkan salah.'], 402);
        }

        if ($user['aktif'] == 'nonaktif') {
            return $this->respond(['error' => 'NIA Anda telah dinonaktifkan. Silahkan hubungi admin.'], 402);
        }

        $pwd_verify = password_verify($password, $user['password']);

        if (!$pwd_verify) {
            return $this->respond(['error' => 'Password yang Anda masukkan salah.'], 402);
        }

        $key = getenv('JWT_KEY');
        $iat = time(); // current timestamp value
        $exp = $iat + (60 * getenv('JWT_EXP'));

        $payload = array(
            "sub" => $nia,
            "iat" => $iat, //Time the JWT issued at
            "exp" => $exp, // Expiration time of token
            "level" => $user['level'],
        );

        $token = JWT::encode($payload, $key, 'HS256');
        $cek = WRITEPATH . 'uploads/profile/' . $user['foto'];
        $foto = file_exists($cek) ? base_url() . 'api/render/foto/' . $user['foto'] : base_url() . 'api/render/foto/no_photo.jpg';
        $data = [
            'nia' => $nia,
            'nama' => $user['nama'],
            'level' => $user['level'],
            'foto' => $foto,
        ];

        $response = [
            'pesan' => 'Login Succesful',
            'user' => $data,
            'token' => $token
        ];

        return $this->respond($response, 200);
    }

    public function refreshToken()
    {
        $header = $this->request->getServer('HTTP_AUTHORIZATION');
        $key = getenv('JWT_KEY');
        $jwt = explode(' ', $header)[1];
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

        $iat = time();
        $exp = $iat + (60 * getenv('JWT_EXP'));

        if ($iat > $decoded->exp) return $this->fail('Token sudah kadaluarsa', 401);

        // return $this->respond($decoded);
        $payload = array(
            "sub" => $decoded->sub,
            "iat" => $iat,
            "exp" => $exp,
            "level" => $decoded->level,
        );

        $newToken = JWT::encode($payload, $key, 'HS256');
        return $this->respond(['new_token' => $newToken]);
    }

    public function adminAuth()
    {
        helper(['form']);
        $rules = [
            'nia'          => [
                'rules'  => 'required|min_length[4]|max_length[20]',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                    'min_length' => '{field} tidak boleh kurang dari 4 karakter',
                    'max_length' => '{field} tidak boleh lebih dari 20 karakter'
                ]
            ],
            'password'         => [
                'rules' =>  'required|min_length[4]',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                    'min_length' => '{field} tidak boleh kurang dari 4 karakter'
                ]
            ],
        ];

        if (!$this->validate($rules)) return $this->fail($this->validator->getErrors(), 409);

        $ma = new ModelAnggota();

        $json = $this->request->getJSON();
        $nia = strtoupper($json->nia);
        $password = strtoupper($json->password);

        $user = $ma->where('nia', $nia)->first();

        if (is_null($user)) {
            return $this->respond(['error' => 'NIA yang Anda masukkan belum terdaftar.'], 402);
        }

        if ($user['aktif'] == 'nonaktif') {
            return $this->respond(['error' => 'NIA Anda telah dinonaktifkan. Silahkan hubungi admin.'], 402);
        }

        if ($user['level'] != 'admin') {
            return $this->respond(['error' => 'Anda tidak berhak mengakses Halaman Admin.'], 402);
        }

        $pwd_verify = password_verify($password, $user['password']);

        if (!$pwd_verify) {
            return $this->respond(['error' => 'Password yang Anda masukkan salah.'], 402);
        }

        $key = getenv('ADMIN_KEY');
        $iat = time(); // current timestamp value
        $exp = $iat + (60 * getenv('JWT_EXP'));

        $payload = array(
            "sub" => $nia,
            "iat" => $iat, //Time the JWT issued at
            "exp" => $exp, // Expiration time of token
            "level" => $user['level'],
        );

        $token = JWT::encode($payload, $key, 'HS256');
        $data = [
            'nia' => $nia,
            'nama' => $user['nama'],
            'level' => $user['level']
        ];

        $response = [
            'pesan' => 'Login Succesful',
            'user' => $data,
            'token' => $token
        ];

        return $this->respond($response, 200);
    }

    public function adminRefresh()
    {
        $header = $this->request->getServer('HTTP_AUTHORIZATION');
        $key = getenv('ADMIN_KEY');
        $jwt = explode(' ', $header)[1];
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

        $iat = time();
        $exp = $iat + (60 * getenv('JWT_EXP'));

        if ($iat > $decoded->exp) return $this->fail('Token sudah kadaluarsa', 401);

        // return $this->respond($decoded);
        $payload = array(
            "sub" => $decoded->sub,
            "iat" => $iat,
            "exp" => $exp,
            "level" => $decoded->level,
        );

        $newToken = JWT::encode($payload, $key, 'HS256');
        return $this->respond(['new_token' => $newToken]);
    }

    public function permintaanOtp()
    {
        $ma = new ModelAnggota();
        helper(['form']);
        $rules = [
            'nia'          => [
                'rules'  => 'required|min_length[4]|max_length[20]',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                    'min_length' => '{field} tidak boleh kurang dari 4 karakter',
                    'max_length' => '{field} tidak boleh lebih dari 20 karakter'
                ]
            ],
            'wa'         => [
                'rules' =>  'required',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                ]
            ],
        ];

        if (!$this->validate($rules)) return $this->fail($this->validator->getErrors(), 409);
        $json = $this->request->getJSON();
        $nia = $json->nia;
        $wa = $json->wa;
        $anggota = $ma->select('*')->where(['nia' => $nia])->first();
        if (!$anggota) return $this->fail('ID Anggota Anda tidak valid.', 402);
        if ($anggota['aktif'] == '0') return $this->fail('ID Anggota Anda telah dinonaktifkan oleh Admin.', 402);
        if ($anggota['wa'] != $wa) return $this->fail('Nomor WA yang anda masukkan salah.', 402);

        $rand = (string) rand(10000000, 10000000000000);
        $otp = substr($rand, 1, 5);
        $token_otp = password_hash($anggota['nia'].'ditambah'.$anggota['wa'].'ditambah'.$otp, PASSWORD_DEFAULT);
        try {
            $fonnte = new LibFonnte;
            $pesan = "Al-wafa Bi'ahdillah 
OTP reset password Anda adalah *". $otp."*. Segera kirim OPT anda dalam waktu maksimal 3 menit.";
            $waAnggota = $fonnte->kirimPesan($anggota['wa'], $pesan);
            if ($waAnggota) {
                $mot = new ModelOtp();
                $data = [
                    'tanggal' => date('Y-m-d'),
                    'nia' => $nia,
                    'otp' => $otp,
                    'token_otp' => $token_otp
                ];
                $mot->insert($data);
                $text = 'OTP reset password berhasil dikirim. Silahkan periksa WA Anda dan segera kirim OPT dalam waktu 3 menit.';
                return $this->respond(['pesan' => $text, 'token_otp' => $token_otp]);
            }
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage(), 500);
        }
    }
    public function kirimOtp()
    {
        $ma = new ModelAnggota();
        helper(['form']);
        $rules = [
            'token_otp'         => [
                'rules' =>  'required',
                'errors' => [
                    'required' => 'Token OTP tidak boleh kosong.',
                ]
            ],
            'otp'         => [
                'rules' =>  'required',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                ]
            ],
        ];

        if (!$this->validate($rules)) return $this->fail($this->validator->getErrors(), 409);
        $json = $this->request->getJSON();
        $token_otp = $json->token_otp;
        $otp = $json->otp;

        $motp = new ModelOtp();
        $data_otp = $motp->select('*')->where(['otp' => $otp, 'token_otp' => $token_otp])->first();
        if (!$data_otp) return $this->fail('OTP yang anda masukkan tidak valid.', 402);
        $waktu_otp = strtotime($data_otp['created_at']);
        $waktu_sekarang = time();
        $batas_waktu = 60*3; //lima menit
        if(($waktu_sekarang - $waktu_otp) > $batas_waktu) return $this->fail('Masa berlaku OPT anda sudah kadaluarsa.', 402);

        $anggota = $ma->select('*')->where(['nia' => $data_otp['nia']])->first();
        if (!$anggota || !password_verify($anggota['nia'].'ditambah'.$anggota['wa'].'ditambah'.$otp, $token_otp )) return $this->fail('Perminataan reset password tidak dapat dilakukan. mohon hubungi admin.', 402);
        $fonnte = new LibFonnte;
        $ma = new ModelAnggota();
        $admin = $ma->select('wa')->where(['level' => 'admin'])->findAll();
        $nomoradmin = [];
        foreach ($admin as $key => $val) {
            $nomoradmin[] = $val['wa'];
        }
        $waAdmin = implode(",", $nomoradmin);
        $pesan = 'Pengajuan reset password ID Anggota ' . $anggota['nia'] . ' AN. ' . strtoupper($anggota['nama']) . '. Mohon segera diproses';
        
        try {
            $fonnte::kirimPesan($waAdmin, $pesan);
            return $this->respond(['pesan' => 'Perminataan reset password Anda terlah terkirim']);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage(), 500);
        }
    }
}
