<?php

namespace App\Libraries;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtDecode {
    public function decoder($header)
    {
        $key = getenv('JWT_KEY');
        $jwt = explode(' ', $header)[1];
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

        return $decoded;
    }

    public function refresh($header) 
    {
        $key = getenv('JWT_KEY');
        $jwt = explode(' ', $header)[1];
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        $iat = time();
        $exp = $iat + (60*getenv('JWT_EXP'));

        // return $this->respond($decoded);
        $payload = array(
            "sub" => $decoded->sub,
            "iat" => $iat,
            "exp" => $exp,
            "level" => $decoded->level,
            "email" => $decoded->email,
        );

        $newToken = JWT::encode($payload, $key, 'HS256');
        return $newToken;
    }
}