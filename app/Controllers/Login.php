<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

use App\Models\ModelAnggota;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

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

        if (!$this->validate($rules)) return $this->fail($this->validator->getErrors());

        $ma = new ModelAnggota();

        $json = $this->request->getJSON();
        $nia = $json->nia;
        $password = $json->password;

        $user = $ma->where('nia', $nia)->first();

        if (is_null($user)) {
            return $this->respond(['error' => 'NIA yang Anda masukkan salah.'], 401);
        }

        if($user['aktif'] == 'nonaktif') {
            return $this->respond(['error' => 'NIA Anda telah dinonaktifkan. Silahkan hubungi admin.'], 401);
        }

        $pwd_verify = password_verify($password, $user['password']);

        if (!$pwd_verify) {
            return $this->respond(['error' => 'Password yang Anda masukkan salah.'], 401);
        }

        $key = getenv('JWT_KEY');
        $iat = time(); // current timestamp value
        $exp = $iat + (60*getenv('JWT_EXP'));

        $payload = array(
            "sub" => $nia,
            "iat" => $iat, //Time the JWT issued at
            "exp" => $exp, // Expiration time of token
            "level" => $user['level'],
        );

        $token = JWT::encode($payload, $key, 'HS256');
        $cek = WRITEPATH . 'uploads/profile/' . $user['foto'];
        $foto = file_exists($cek) ? base_url().'api/render/foto/'.$user['foto'] : base_url(). 'api/render/foto/no_photo';
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
        $exp = $iat + (60*getenv('JWT_EXP'));

        if($iat > $decoded->exp) return $this->fail('Token sudah kadaluarsa', 401);

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

        if (!$this->validate($rules)) return $this->fail($this->validator->getErrors());

        $ma = new ModelAnggota();

        $json = $this->request->getJSON();
        $nia = $json->nia;
        $password = $json->password;

        $user = $ma->where('nia', $nia)->first();

        if (is_null($user)) {
            return $this->respond(['error' => 'NIA yang Anda masukkan belum terdaftar.'], 401);
        }

        if($user['aktif'] == 'nonaktif') {
            return $this->respond(['error' => 'NIA Anda telah dinonaktifkan. Silahkan hubungi admin.'], 401);
        }

        if ($user['level'] != 'admin') {
            return $this->respond(['error' => 'Anda tidak berhak mengakses Halaman Admin.'], 401);
        }

        $pwd_verify = password_verify($password, $user['password']);

        if (!$pwd_verify) {
            return $this->respond(['error' => 'Password yang Anda masukkan salah.'], 401);
        }

        $key = getenv('ADMIN_KEY');
        $iat = time(); // current timestamp value
        $exp = $iat + (60*getenv('JWT_EXP'));

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
        $exp = $iat + (60*getenv('JWT_EXP'));

        if($iat > $decoded->exp) return $this->fail('Token sudah kadaluarsa', 401);

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

}
