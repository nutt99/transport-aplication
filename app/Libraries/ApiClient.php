<?php

namespace App\Libraries;

use Throwable;

class ApiClient
{
    private string $transportBaseUrl;
    private string $wisataBaseUrl;
    private string $publicKey;
    private string $secretKey;

    public function __construct()
    {
        $this->transportBaseUrl = rtrim(
            env('TRANSPORT_API_URL', 'http://127.0.0.1:8081/api'),
            '/'
        );
        $this->wisataBaseUrl = rtrim(
            env('JEJAKSUMUT_API_URL', 'http://70.153.136.38:8080/api/v1'),
            '/'
        );
        $this->publicKey = (string) env('API_KEY_PUBLIC', '');
        $this->secretKey = (string) env('API_KEY_SECRET', '');
    }

    /**
     * Request HTTP ke REST API Transportasi internal.
     * Set $useSecret = true nanti saat dipakai di Dashboard Admin (POST/PUT/DELETE).
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

        return $this->send(
            strtoupper($method),
            $url,
            $headers,
            $query,
            $jsonBody,
            'Gagal terhubung ke API Transportasi. Pastikan server API jalan di ' . $this->transportBaseUrl
        );
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
            'timeout'     => 8,
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
}