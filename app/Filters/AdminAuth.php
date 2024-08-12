<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AdminAuth implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $key = getenv('ADMIN_KEY');
        $header = $request->getServer('HTTP_AUTHORIZATION');
        // if(!$header) return Services::response()
        //                     ->setJSON(['msg' => 'Token Required'])
        //                     ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        // $token = explode(' ', $header)[1];

        // try {
        //     JWT::decode($token, new Key($key, 'HS256'));
        // } catch (\Throwable $th) {
        //     return Services::response()
        //                     ->setJSON(['msg' => 'Invalid Token'])
        //                     ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        // }

        $token = null;

        // extract the token from the header
        if (!empty($header)) {
            if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
                $token = $matches[1];
            }
        }

        if (is_null($token) || empty($token)) {
            $response = service('response');
            $response->setBody('Access denied');
            $response->setStatusCode(401);
            return $response;
        }

        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            if ($decoded->level != 'admin') {
                $response = service('response');
                $response->setBody('Admin only');
                $response->setStatusCode(401);
                return $response;
            }
        } catch (\Throwable $th) {
            return Services::response()
                ->setJSON(['msg' => 'Invalid Token'])
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
