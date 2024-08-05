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
            return $this->respond(['error' => 'Invalid username or password.'], 401);
        }

        $pwd_verify = password_verify($password, $user['password']);

        if (!$pwd_verify) {
            return $this->respond(['error' => 'Invalid username or password.'], 401);
        }

        $key = getenv('JWT_SECRET');
        $iat = time(); // current timestamp value
        $exp = $iat + 3600;

        $payload = array(
            "iss" => "Issuer of the JWT",
            "aud" => "Audience that the JWT",
            "sub" => "Subject of the JWT",
            "iat" => $iat, //Time the JWT issued at
            "exp" => $exp, // Expiration time of token
            "email" => $user['email'],
        );

        $token = JWT::encode($payload, $key, 'HS256');

        $response = [
            'message' => 'Login Succesful',
            'token' => $token
        ];

        return $this->respond($response, 200);
    }
}
