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
                    'min_length' => '{field} tidak boleh kurang dari 4 karakter'
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

        $pwd_verify = password_verify($password, $user['password']);

        if (!$pwd_verify) {
            return $this->respond(['error' => 'Password yang Anda masukkan salah.'], 401);
        }

        $key = getenv('JWT_KEY');
        $iat = time(); // current timestamp value
        $exp = $iat + (60*getenv('JWT_EXP'));

        $payload = array(
            // "iss" => "Issuer of the JWT",
            // "aud" => "Audience that the JWT",
            "sub" => $nia,
            "iat" => $iat, //Time the JWT issued at
            "exp" => $exp, // Expiration time of token
            "level" => $user['level'],
            "email" => $user['email'],
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

    public function refreshToken($token)
    {
        $key = getenv('JWT_KEY');
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        $iat = time();
        $exp = $iat + (60*5);

        if($iat > $decoded->exp) return $this->fail('Token sudah kadaluarsa', 401);

        // return $this->respond($decoded);
        $payload = array(
            "sub" => $decoded->sub,
            "iat" => $iat,
            "exp" => $exp,
            "level" => $decoded->level,
            "email" => $decoded->email,
        );

        $newToken = JWT::encode($payload, $key, 'HS256');
        return $this->respond(['new_token' => $newToken]);
    }
}
