<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\TransportModel;
use App\Models\KotaModel;
use App\Models\JadwalModel;

class Transport extends ResourceController
{
    protected $format = 'json';

    /**
     * GET /api/transport/transports
     * Mengambil seluruh data jenis moda transportasi
     */
    public function index()
    {
        $transportModel = new TransportModel();
        $data = $transportModel->findAll();

        return $this->respond([
            'status' => 'success',
            'data'   => $data,
        ]);
    }

    /**
     * GET /api/transport/kota
     * Mengambil daftar seluruh kota untuk dropdown asal / tujuan
     */
    public function kota()
    {
        $kotaModel = new KotaModel();
        $data = $kotaModel->orderBy('nama_kota', 'ASC')->findAll();

        return $this->respond([
            'status' => 'success',
            'data'   => $data,
        ]);
    }

    /**
     * GET /api/transport/tarif
     * Menghitung jarak dan rincian tarif transportasi antar kota
     * Parameter (query string):
     * - id_asal & id_tujuan (integer ID kota)
     * - ATAU asal & tujuan (nama kota, misal: ?asal=Jakarta&tujuan=Bandung)
     */
    public function tarif()
    {
        $kotaModel   = new KotaModel();
        $jadwalModel = new JadwalModel();

        $idAsal   = $this->request->getGet('id_asal');
        $idTujuan = $this->request->getGet('id_tujuan');
        $asal     = $this->request->getGet('asal');
        $tujuan   = $this->request->getGet('tujuan');

        $kotaAsal = null;
        if (!empty($idAsal)) {
            $kotaAsal = $kotaModel->find($idAsal);
        } elseif (!empty($asal)) {
            $kotaAsal = $kotaModel->like('nama_kota', trim($asal), 'both', null, true)->first();
        }

        $kotaTujuan = null;
        if (!empty($idTujuan)) {
            $kotaTujuan = $kotaModel->find($idTujuan);
        } elseif (!empty($tujuan)) {
            $kotaTujuan = $kotaModel->like('nama_kota', trim($tujuan), 'both', null, true)->first();
        }

        if (!$kotaAsal) {
            return $this->failNotFound('Kota asal tidak ditemukan. Masukkan parameter "id_asal" atau "asal" yang valid.');
        }

        if (!$kotaTujuan) {
            return $this->failNotFound('Kota tujuan tidak ditemukan. Masukkan parameter "id_tujuan" atau "tujuan" yang valid.');
        }

        if ($kotaAsal['id'] === $kotaTujuan['id']) {
            return $this->failValidationErrors('Kota asal dan kota tujuan tidak boleh sama.');
        }

        $jarakKm = $this->hitungJarakKm(
            $kotaAsal['latitude'],
            $kotaAsal['longitude'],
            $kotaTujuan['latitude'],
            $kotaTujuan['longitude']
        );

        $jadwalList = $jadwalModel->cariJadwal($kotaAsal['id'], $kotaTujuan['id']);

        $hasilPilihan = [];
        foreach ($jadwalList as $j) {
            $tarifPerKm    = (float)$j['tarif_per_km'];
            $estimasiTarif = round($jarakKm * $tarifPerKm, -2);
            $durasi        = $this->hitungDurasi($j['start_hours'], $j['ended_hours']);

            $hasilPilihan[] = [
                'id_jadwal'             => (int)$j['id'],
                'penyedia'              => $j['nama_penyedia'],
                'jenis_transport'       => $j['jenis_transport'],
                'jam_berlaku'           => $j['start_hours'] . ' - ' . $j['ended_hours'],
                'tarif_per_km'          => $tarifPerKm,
                'estimasi_tarif'        => (int)$estimasiTarif,
                'estimasi_tarif_format' => 'Rp ' . number_format($estimasiTarif, 0, ',', '.'),
            ];
        }

        return $this->respond([
            'status' => 'success',
            'data'   => [
                'rute' => [
                    'asal' => [
                        'id'        => (int)$kotaAsal['id'],
                        'nama_kota' => $kotaAsal['nama_kota'],
                        'latitude'  => (float)$kotaAsal['latitude'],
                        'longitude' => (float)$kotaAsal['longitude'],
                    ],
                    'tujuan' => [
                        'id'        => (int)$kotaTujuan['id'],
                        'nama_kota' => $kotaTujuan['nama_kota'],
                        'latitude'  => (float)$kotaTujuan['latitude'],
                        'longitude' => (float)$kotaTujuan['longitude'],
                    ],
                    'jarak_km' => $jarakKm,
                ],
                'total_jadwal_tersedia' => count($hasilPilihan),
                'pilihan_transportasi'  => $hasilPilihan,
            ],
        ]);
    }

    /**
     * Hitung jarak dua titik koordinat bumi (Haversine Formula) dalam Kilometer
     */
    private function hitungJarakKm($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371;

        $latFrom = deg2rad((float)$lat1);
        $lonFrom = deg2rad((float)$lon1);
        $latTo   = deg2rad((float)$lat2);
        $lonTo   = deg2rad((float)$lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return round($angle * $earthRadius, 2);
    }

    private function hitungDurasi(string $start, string $ended): string
    {
        $t1 = strtotime($start);
        $t2 = strtotime($ended);

        if ($t2 < $t1) {
            $t2 += 24 * 3600;
        }

        $diffMinutes = (int)round(($t2 - $t1) / 60);
        $hours       = floor($diffMinutes / 60);
        $minutes     = $diffMinutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours} Jam {$minutes} Menit";
        } elseif ($hours > 0) {
            return "{$hours} Jam";
        }

        return "{$minutes} Menit";
    }
}
