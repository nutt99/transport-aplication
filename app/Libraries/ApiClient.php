<?php

namespace App\Libraries;

use Throwable;
use CodeIgniter\HTTP\ResponseInterface;

class ApiClient
{
    private string $transportBaseUrl;
    private string $wisataBaseUrl;
    private string $publicKey;
    private string $secretKey;

    public function __construct()
    {
        $envUrl = env('TRANSPORT_API_URL');
        $this->transportBaseUrl = !empty($envUrl)
            ? rtrim($envUrl, '/')
            : rtrim(base_url('api'), '/');

        $this->wisataBaseUrl = rtrim(
            env('JEJAKSUMUT_API_URL', 'https://api-jejaksumut.vulnera.web.id/api/v1'),
            '/'
        );
        $this->publicKey = (string) env('API_KEY_PUBLIC', '');
        $this->secretKey = (string) env('API_KEY_SECRET', '');
    }

    /**
     * Request HTTP ke REST API Transportasi internal.
     * Set $useSecret = true saat dipakai di Dashboard Admin untuk mutasi data (POST/PUT/DELETE).
     */
    public function transport(
        string $method,
        string $path,
        array $query = [],
        ?array $jsonBody = null,
        bool $useSecret = false
    ): array {
        $url     = $this->transportBaseUrl . '/' . ltrim($path, '/');
        $apiKey  = $useSecret ? $this->secretKey : $this->publicKey;
        $headers = [
            'Accept'    => 'application/json',
            'X-API-KEY' => $apiKey,
        ];

        $res = $this->send(
            strtoupper($method),
            $url,
            $headers,
            $query,
            $jsonBody,
            'Gagal terhubung ke API Transportasi. Pastikan server API jalan di ' . $this->transportBaseUrl
        );

        // Fallback eksekusi internal controller API jika jaringan cURL gagal
        // (sangat berguna untuk local development dengan server single-thread atau port terpisah belum aktif)
        if ($res['status'] === 502 && isset($res['body']['message']) && str_contains($res['body']['message'], 'Gagal terhubung')) {
            return $this->internalTransportDispatch($method, $path, $query, $jsonBody, $useSecret);
        }

        return $res;
    }

    /**
     * Request HTTP GET ke REST API JejakSumut.
     */
    public function wisataGet(string $path, array $query = []): array
    {
        $url = $this->wisataBaseUrl . '/' . ltrim($path, '/');

        return $this->send(
            'GET',
            $url,
            ['Accept' => 'application/json'],
            $query,
            null,
            'Gagal terhubung ke server API JejakSumut.'
        );
    }

    private function send(
        string $method,
        string $url,
        array $headers,
        array $query,
        ?array $jsonBody,
        string $connectionErrorMsg
    ): array {
        $client = \Config\Services::curlrequest([
            'timeout'     => 5,
            'http_errors' => false,
        ]);

        $options = [
            'headers' => $headers,
        ];

        if (! empty($query)) {
            $options['query'] = $query;
        }

        if ($jsonBody !== null) {
            $options['json'] = $jsonBody;
        }

        try {
            $response = $client->request($method, $url, $options);
            $decoded  = json_decode((string) $response->getBody(), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return [
                    'status' => 502,
                    'body'   => [
                        'status'  => 'error',
                        'message' => 'Respons API bukan JSON yang valid.',
                    ],
                ];
            }

            return [
                'status' => $response->getStatusCode(),
                'body'   => $decoded,
            ];
        } catch (Throwable $e) {
            return [
                'status' => 502,
                'body'   => [
                    'status'  => 'error',
                    'message' => $connectionErrorMsg,
                    'detail'  => ENVIRONMENT === 'development' ? $e->getMessage() : null,
                ],
            ];
        }
    }

    /**
     * Fallback eksekusi internal REST API controller tanpa melalui cURL network
     */
    private function internalTransportDispatch(
        string $method,
        string $path,
        array $query = [],
        ?array $jsonBody = null,
        bool $useSecret = false
    ): array {
        $cleanPath = trim($path, '/');

        // Pastikan API Key tervalidasi
        $keyUsed = $useSecret ? $this->secretKey : $this->publicKey;
        if (strtoupper($method) !== 'GET' && $keyUsed !== $this->secretKey) {
            return [
                'status' => 403,
                'body'   => ['status' => 403, 'error' => 'Forbidden', 'message' => 'API Key publik hanya memiliki akses baca (GET).'],
            ];
        }

        // Sinkronisasi query param
        if (!empty($query)) {
            $_GET = array_merge($_GET, $query);
        }

        // Endpoint /lokasi
        if ($cleanPath === 'lokasi' || str_starts_with($cleanPath, 'lokasi/')) {
            $controller = new \App\Controllers\Api\Lokasi();
            $id = str_contains($cleanPath, '/') ? (int) substr($cleanPath, strrpos($cleanPath, '/') + 1) : null;

            try {
                if ($method === 'GET') {
                    $response = $id ? $controller->show($id) : $controller->index();
                } elseif ($method === 'POST') {
                    if ($jsonBody !== null) {
                        \Config\Services::request()->setBody(json_encode($jsonBody));
                    }
                    $response = $controller->create();
                } elseif ($method === 'PUT' || $method === 'PATCH') {
                    if ($jsonBody !== null) {
                        \Config\Services::request()->setBody(json_encode($jsonBody));
                    }
                    $response = $controller->update($id);
                } elseif ($method === 'DELETE') {
                    $response = $controller->delete($id);
                } else {
                    return ['status' => 405, 'body' => ['message' => 'Method Not Allowed']];
                }

                if ($response instanceof ResponseInterface) {
                    return [
                        'status' => $response->getStatusCode(),
                        'body'   => json_decode((string) $response->getBody(), true) ?? [],
                    ];
                }
            } catch (Throwable $e) {
                return [
                    'status' => 500,
                    'body'   => ['status' => 'error', 'message' => $e->getMessage()],
                ];
            }
        }

        return [
            'status' => 404,
            'body'   => ['status' => 'error', 'message' => 'Endpoint internal API tidak ditemukan.'],
        ];
    }
}
