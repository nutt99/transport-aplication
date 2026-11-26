<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\ApiClient;

class TransportController extends BaseController
{
    protected ApiClient $apiClient;

    public function __construct()
    {
        $this->apiClient = new ApiClient();
    }

    /**
     * GET /admin/transport
     * Mengambil daftar moda transportasi dan statistik penyedia melalui REST API (/api/moda & /api/penyedia)
     */
    public function index()
    {
        $q = trim((string) $this->request->getGet('q'));

        // 1. Panggil REST API: GET /api/moda
        $res = $this->apiClient->transport('GET', '/moda');
        $semuaModa = (isset($res['body']['data']) && is_array($res['body']['data']))
            ? $res['body']['data']
            : [];

        // 2. Panggil REST API: GET /api/penyedia untuk menghitung jumlah penyedia per moda
        $penyediaRes = $this->apiClient->transport('GET', '/penyedia');
        $semuaPenyedia = (isset($penyediaRes['body']['data']) && is_array($penyediaRes['body']['data']))
            ? $penyediaRes['body']['data']
            : [];

        $penyediaCounts = [];
        foreach ($semuaPenyedia as $p) {
            $tid = (int) ($p['id_transport'] ?? 0);
            if ($tid > 0) {
                $penyediaCounts[$tid] = ($penyediaCounts[$tid] ?? 0) + 1;
            }
        }

        // Filter berdasarkan kata kunci pencarian jika ada
        $transportList = $semuaModa;
        if ($q !== '') {
            $needle = strtolower($q);
            $transportList = array_values(array_filter(
                $semuaModa,
                static fn (array $item): bool => str_contains(strtolower((string) ($item['jenis'] ?? '')), $needle)
            ));
        }

        foreach ($transportList as &$item) {
            $item['jumlah_penyedia'] = $penyediaCounts[(int) ($item['id'] ?? 0)] ?? 0;
        }
        unset($item);

        $stats = [
            'total_transport' => count($semuaModa),
            'total_penyedia'  => count($semuaPenyedia),
        ];

        return view('admin/transport/index', [
            'transportList' => $transportList,
            'stats'         => $stats,
            'q'             => $q,
        ]);
    }

    /**
     * POST /admin/transport/simpan
     * Menambahkan moda transportasi baru melalui REST API: POST /api/moda
     */
    public function simpan()
    {
        $jenis = trim((string) $this->request->getPost('jenis'));

        if ($jenis === '') {
            return redirect()->back()->withInput()->with('error', 'Jenis transportasi wajib diisi.');
        }

        $res = $this->apiClient->transport('POST', '/moda', [], ['jenis' => $jenis], true);

        if ($res['status'] >= 200 && $res['status'] < 300) {
            return redirect()->to(base_url('admin/transport'))->with('success', "Moda transportasi \"{$jenis}\" berhasil ditambahkan via API.");
        }

        return redirect()->back()->withInput()->with(
            'error',
            $this->extractErrorMessage($res, 'Gagal menambahkan moda transportasi melalui API.')
        );
    }

    /**
     * POST /admin/transport/update/{id}
     * Memperbarui moda transportasi melalui REST API: PUT /api/moda/{id}
     */
    public function update($id = null)
    {
        $id    = (int) $id;
        $jenis = trim((string) $this->request->getPost('jenis'));

        if ($jenis === '') {
            return redirect()->back()->withInput()->with('error', 'Jenis transportasi wajib diisi.');
        }

        $res = $this->apiClient->transport('PUT', '/moda/' . $id, [], ['jenis' => $jenis], true);

        if ($res['status'] >= 200 && $res['status'] < 300) {
            return redirect()->to(base_url('admin/transport'))->with('success', "Moda transportasi \"{$jenis}\" berhasil diperbarui via API.");
        }

        return redirect()->back()->withInput()->with(
            'error',
            $this->extractErrorMessage($res, 'Gagal memperbarui moda transportasi melalui API.')
        );
    }

    /**
     * POST /admin/transport/hapus/{id}
     * Menghapus moda transportasi melalui REST API: DELETE /api/moda/{id}
     */
    public function hapus($id = null)
    {
        $id  = (int) $id;
        $res = $this->apiClient->transport('DELETE', '/moda/' . $id, [], null, true);

        if ($res['status'] >= 200 && $res['status'] < 300) {
            return redirect()->to(base_url('admin/transport'))->with('success', 'Moda transportasi berhasil dihapus via API.');
        }

        return redirect()->back()->withInput()->with(
            'error',
            $this->extractErrorMessage($res, 'Gagal menghapus moda transportasi melalui API.')
        );
    }

    private function extractErrorMessage(array $res, string $default): string
    {
        $messages = $res['body']['messages'] ?? null;

        if (is_array($messages)) {
            if (isset($messages['error'])) {
                return is_array($messages['error']) ? implode(', ', $messages['error']) : (string) $messages['error'];
            }

            return implode(', ', array_map('strval', $messages));
        }

        if (isset($res['body']['message'])) {
            return (string) $res['body']['message'];
        }

        return $default;
    }
}
