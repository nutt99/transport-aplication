<?php

namespace App\Controllers;

use App\Libraries\ApiClient;
use CodeIgniter\HTTP\ResponseInterface;

class FrontendApi extends BaseController
{
    private ApiClient $api;

    public function __construct()
    {
        $this->api = new ApiClient();
    }

    // ==================== WISATA (JEJAKSUMUT) ====================

    public function destinasi(): ResponseInterface
    {
        $query = $this->pickQuery([
            'kota',
            'kategori',
            'kode_kabupaten',
            'harga_maksimal',
            'kata_kunci',
            'urutan',
            'halaman',
            'batas',
        ]);

        return $this->respondFromClient($this->api->wisataGet('/destinasi', $query));
    }

    public function destinasiDetail(string $slug): ResponseInterface
    {
        return $this->respondFromClient(
            $this->api->wisataGet('/destinasi/' . rawurlencode($slug))
        );
    }

    public function kategori(): ResponseInterface
    {
        return $this->respondFromClient($this->api->wisataGet('/kategori'));
    }

    public function kabupatenKota(): ResponseInterface
    {
        return $this->respondFromClient($this->api->wisataGet('/kabupaten-kota'));
    }

    // ==================== TRANSPORTASI (INTERNAL API) ====================

    public function transports(): ResponseInterface
    {
        return $this->respondFromClient(
            $this->api->transport('GET', '/transport/transports')
        );
    }

    public function lokasi(): ResponseInterface
    {
        return $this->respondFromClient(
            $this->api->transport('GET', '/transport/lokasi')
        );
    }

    public function tarif(): ResponseInterface
    {
        $query = $this->pickQuery([
            'id_asal',
            'id_tujuan',
            'asal',
            'tujuan',
            'jam',
        ]);

        return $this->respondFromClient(
            $this->api->transport('GET', '/transport/tarif', $query)
        );
    }

    public function rekomendasiWisata(): ResponseInterface
    {
        $query = $this->pickQuery([
            'user_lat',
            'user_lng',
            'wisata_lat',
            'wisata_lng',
            'nama_wisata',
            'trip_type',
        ]);

        return $this->respondFromClient(
            $this->api->transport('GET', '/transport/rekomendasi-wisata', $query)
        );
    }

    // ==================== HELPER ====================

    private function pickQuery(array $allowedKeys): array
    {
        $result = [];
        foreach ($allowedKeys as $key) {
            $value = $this->request->getGet($key);
            if ($value !== null && $value !== '') {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    private function respondFromClient(array $res): ResponseInterface
    {
        return $this->response
            ->setStatusCode($res['status'])
            ->setJSON($res['body']);
    }
}