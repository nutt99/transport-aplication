<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class ApiKeyFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $method = strtoupper($request->getMethod());

        // Wajib loloskan preflight request dari browser (CORS)
        if ($method === 'OPTIONS') {
            return null;
        }

        $apiKey    = trim($request->getHeaderLine('X-API-KEY'));
        $publicKey = (string) env('API_KEY_PUBLIC', '');
        $secretKey = (string) env('API_KEY_SECRET', '');

        if ($secretKey === '') {
            return Services::response()
                ->setStatusCode(500)
                ->setJSON([
                    'status'  => 500,
                    'error'   => 'Server Misconfiguration',
                    'message' => 'API_KEY_SECRET belum dikonfigurasi di server.',
                ]);
        }

        if ($apiKey === '') {
            return Services::response()
                ->setStatusCode(401)
                ->setJSON([
                    'status'  => 401,
                    'error'   => 'Unauthorized',
                    'message' => 'Header X-API-KEY wajib disertakan.',
                ]);
        }

        $isSecret = hash_equals($secretKey, $apiKey);
        $isPublic = $publicKey !== '' && hash_equals($publicKey, $apiKey);

        // Request GET: boleh pakai public key maupun secret key
        if ($method === 'GET') {
            if ($isSecret || $isPublic) {
                return null;
            }

            return Services::response()
                ->setStatusCode(401)
                ->setJSON([
                    'status'  => 401,
                    'error'   => 'Unauthorized',
                    'message' => 'API Key tidak valid.',
                ]);
        }

        // Request POST, PUT, PATCH, DELETE: wajib secret key
        if ($isSecret) {
            return null;
        }

        if ($isPublic) {
            return Services::response()
                ->setStatusCode(403)
                ->setJSON([
                    'status'  => 403,
                    'error'   => 'Forbidden',
                    'message' => 'API Key publik hanya memiliki akses baca (GET).',
                ]);
        }

        return Services::response()
            ->setStatusCode(401)
            ->setJSON([
                'status'  => 401,
                'error'   => 'Unauthorized',
                'message' => 'API Key tidak valid.',
            ]);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak perlu aksi tambahan setelah response
    }
}