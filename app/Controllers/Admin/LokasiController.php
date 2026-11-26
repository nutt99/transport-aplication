<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\ApiClient;

class LokasiController extends BaseController
{
    protected ApiClient $apiClient;

    public function __construct()
    {
        $this->apiClient = new ApiClient();
    }

    /**
     * GET /admin/lokasi
     * Mengambil daftar lokasi melalui REST API (/api/lokasi)
     */
    public function index()
    {
        $q    = trim((string) $this->request->getGet('q'));
        $tipe = trim((string) $this->request->getGet('tipe'));

        $query = [];
        if (!empty($q)) {
            $query['q'] = $q;
        }
        if (!empty($tipe)) {
            $query['tipe'] = $tipe;
        }

        // 1. Panggil REST API: GET /api/lokasi (dengan parameter pencarian & filter)
        $res = $this->apiClient->transport('GET', '/lokasi', $query);
        $lokasiList = [];

        if (isset($res['body']['data']) && is_array($res['body']['data'])) {
            $lokasiList = $res['body']['data'];
        }

        // Auto-assign tipe_lokasi untuk tampilan jika belum terisi di database
        foreach ($lokasiList as &$item) {
            if (empty($item['tipe_lokasi'])) {
                $nama = strtolower($item['nama_lokasi'] ?? '');
                if (str_contains($nama, 'bandara') || str_contains($nama, 'airport')) {
                    $item['tipe_lokasi'] = 'bandara';
                } elseif (str_contains($nama, 'stasiun')) {
                    $item['tipe_lokasi'] = 'stasiun';
                } elseif (str_contains($nama, 'terminal')) {
                    $item['tipe_lokasi'] = 'terminal';
                } elseif (str_contains($nama, 'pelabuhan') || str_contains($nama, 'dermaga')) {
                    $item['tipe_lokasi'] = 'pelabuhan';
                } elseif (str_contains($nama, 'halte')) {
                    $item['tipe_lokasi'] = 'halte';
                } else {
                    $item['tipe_lokasi'] = 'terminal';
                }
            }
        }
        unset($item);

        // 2. Ambil seluruh data lokasi via API untuk statistik lengkap
        $allRes = $this->apiClient->transport('GET', '/lokasi');
        $semua = (isset($allRes['body']['data']) && is_array($allRes['body']['data']))
            ? $allRes['body']['data']
            : $lokasiList;

        $stats = [
            'total'     => count($semua),
            'bandara'   => 0,
            'stasiun'   => 0,
            'terminal'  => 0,
            'halte'     => 0,
            'pelabuhan' => 0,
        ];

        foreach ($semua as $s) {
            $t = $s['tipe_lokasi'] ?? '';
            if (empty($t)) {
                $nama = strtolower($s['nama_lokasi'] ?? '');
                if (str_contains($nama, 'bandara')) $t = 'bandara';
                elseif (str_contains($nama, 'stasiun')) $t = 'stasiun';
                elseif (str_contains($nama, 'terminal')) $t = 'terminal';
                elseif (str_contains($nama, 'halte')) $t = 'halte';
                elseif (str_contains($nama, 'pelabuhan') || str_contains($nama, 'dermaga')) $t = 'pelabuhan';
                else $t = 'terminal';
            }
            if (isset($stats[$t])) {
                $stats[$t]++;
            } else {
                $stats['terminal']++;
            }
        }

        return view('admin/lokasi/index', [
            'lokasiList' => $lokasiList,
            'stats'      => $stats,
            'q'          => $q,
            'tipe'       => $tipe,
        ]);
    }

    /**
     * POST /admin/lokasi/simpan
     * Menambahkan lokasi baru melalui REST API: POST /api/lokasi
     */
    public function simpan()
    {
        $namaLokasi = trim((string) $this->request->getPost('nama_lokasi'));
        $tipeLokasi = trim((string) $this->request->getPost('tipe_lokasi'));
        $latitude   = trim((string) $this->request->getPost('latitude'));
        $longitude  = trim((string) $this->request->getPost('longitude'));

        if (empty($namaLokasi) || empty($latitude) || empty($longitude)) {
            return redirect()->back()->withInput()->with('error', 'Nama lokasi, latitude, dan longitude wajib diisi.');
        }

        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            return redirect()->back()->withInput()->with('error', 'Format latitude dan longitude harus berupa angka desimal.');
        }

        $payload = [
            'nama_lokasi' => $namaLokasi,
            'tipe_lokasi' => !empty($tipeLokasi) ? $tipeLokasi : 'terminal',
            'latitude'    => (float) $latitude,
            'longitude'   => (float) $longitude,
        ];

        // Panggil REST API dengan Secret Key (useSecret = true)
        $res = $this->apiClient->transport('POST', '/lokasi', [], $payload, true);

        if ($res['status'] >= 200 && $res['status'] < 300) {
            return redirect()->to(base_url('admin/lokasi'))->with('success', "Titik lokasi \"{$namaLokasi}\" berhasil ditambahkan via API.");
        }

        $errorMsg = $res['body']['messages']['error'] 
            ?? $res['body']['message'] 
            ?? 'Gagal menambahkan lokasi melalui API.';

        if (is_array($errorMsg)) {
            $errorMsg = implode(', ', $errorMsg);
        }

        return redirect()->back()->withInput()->with('error', $errorMsg);
    }

    /**
     * POST /admin/lokasi/update/{id}
     * Memperbarui lokasi melalui REST API: PUT /api/lokasi/{id}
     */
    public function update($id = null)
    {
        $id = (int) $id;
        $namaLokasi = trim((string) $this->request->getPost('nama_lokasi'));
        $tipeLokasi = trim((string) $this->request->getPost('tipe_lokasi'));
        $latitude   = trim((string) $this->request->getPost('latitude'));
        $longitude  = trim((string) $this->request->getPost('longitude'));

        if (empty($namaLokasi) || empty($latitude) || empty($longitude)) {
            return redirect()->back()->withInput()->with('error', 'Nama lokasi, latitude, dan longitude wajib diisi.');
        }

        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            return redirect()->back()->withInput()->with('error', 'Format latitude dan longitude harus berupa angka desimal.');
        }

        $payload = [
            'nama_lokasi' => $namaLokasi,
            'tipe_lokasi' => !empty($tipeLokasi) ? $tipeLokasi : 'terminal',
            'latitude'    => (float) $latitude,
            'longitude'   => (float) $longitude,
        ];

        // Panggil REST API dengan Secret Key (useSecret = true)
        $res = $this->apiClient->transport('PUT', '/lokasi/' . $id, [], $payload, true);

        if ($res['status'] >= 200 && $res['status'] < 300) {
            return redirect()->to(base_url('admin/lokasi'))->with('success', "Data lokasi \"{$namaLokasi}\" berhasil diperbarui via API.");
        }

        $errorMsg = $res['body']['messages']['error'] 
            ?? $res['body']['message'] 
            ?? 'Gagal memperbarui data lokasi melalui API.';

        if (is_array($errorMsg)) {
            $errorMsg = implode(', ', $errorMsg);
        }

        return redirect()->back()->withInput()->with('error', $errorMsg);
    }

    /**
     * POST /admin/lokasi/hapus/{id}
     * Menghapus lokasi melalui REST API: DELETE /api/lokasi/{id}
     */
    public function hapus($id = null)
    {
        $id = (int) $id;

        // Panggil REST API dengan Secret Key (useSecret = true)
        $res = $this->apiClient->transport('DELETE', '/lokasi/' . $id, [], null, true);

        if ($res['status'] >= 200 && $res['status'] < 300) {
            return redirect()->to(base_url('admin/lokasi'))->with('success', "Titik lokasi berhasil dihapus via API.");
        }

        $errorMsg = $res['body']['messages']['error'] 
            ?? $res['body']['message'] 
            ?? 'Gagal menghapus lokasi melalui API.';

        if (is_array($errorMsg)) {
            $errorMsg = implode(', ', $errorMsg);
        }

        return redirect()->back()->withInput()->with('error', $errorMsg);
    }
}
