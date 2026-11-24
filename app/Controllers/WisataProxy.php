<?php


// consider to delete this cause this file is not used anymore



namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

class WisataProxy extends BaseController
{
    private string $apiBase;

    public function __construct()
    {
        $this->apiBase = rtrim(
            env('JEJAKSUMUT_API_URL', 'http://70.153.136.38:8080/api/v1'),
            '/'
        );
    }

    /**
     * GET /wisata-proxy/destinasi
     */
    public function destinasi(): ResponseInterface
    {
        $allowedParams = [
            'kota',
            'kategori',
            'kode_kabupaten',
            'harga_maksimal',
            'kata_kunci',
            'urutan',
            'halaman',
            'batas',
        ];

        $query = [];
        foreach ($allowedParams as $key) {
            $value = $this->request->getGet($key);
            if ($value !== null && $value !== '') {
                $query[$key] = $value;
            }
        }

        return $this->forwardGet('/destinasi', $query);
    }

    /**
     * GET /wisata-proxy/destinasi/(:segment)
     */
    public function detail(string $slug): ResponseInterface
    {
        return $this->forwardGet('/destinasi/' . rawurlencode($slug));
    }

    /**
     * GET /wisata-proxy/kategori
     */
    public function kategori(): ResponseInterface
    {
        return $this->forwardGet('/kategori');
    }

    /**
     * GET /wisata-proxy/kabupaten-kota
     */
    public function kabupatenKota(): ResponseInterface
    {
        return $this->forwardGet('/kabupaten-kota');
    }

    private function forwardGet(string $path, array $query = []): ResponseInterface
    {
        $client = \Config\Services::curlrequest([
            'timeout'     => 8,
            'http_errors' => false,
            'headers'     => [
                'Accept' => 'application/json',
            ],
        ]);

        try {
            $url      = $this->apiBase . $path;
            $response = $client->get($url, ['query' => $query]);
            $body     = $response->getBody();
            $decoded  = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return $this->response
                    ->setStatusCode(502)
                    ->setJSON([
                        'status'  => 'error',
                        'message' => 'Respons API JejakSumut bukan JSON yang valid.',
                    ]);
            }

            return $this->response
                ->setStatusCode($response->getStatusCode())
                ->setJSON($decoded);
        } catch (Throwable $e) {
            return $this->response
                ->setStatusCode(502)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Gagal menghubungi server API JejakSumut.',
                    'detail'  => ENVIRONMENT === 'development' ? $e->getMessage() : null,
                ]);
        }
    }
}