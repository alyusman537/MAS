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
}