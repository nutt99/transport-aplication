<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\TransportModel;
use App\Models\LokasiModel;
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
     * GET /api/transport/lokasi
     * Mengambil daftar seluruh titik lokasi (bandara, stasiun, terminal) untuk dropdown asal / tujuan
     */
    public function lokasi()
    {
        $lokasiModel = new LokasiModel();
        $data = $lokasiModel->orderBy('nama_lokasi', 'ASC')->findAll();

        return $this->respond([
            'status' => 'success',
            'data'   => $data,
        ]);
    }

    /**
     * GET /api/transport/tarif
     * Menghitung jarak dan rincian tarif transportasi berdasarkan rute & jam berangkat
     * Parameter (query string):
     * - id_asal & id_tujuan (integer ID lokasi)
     * - ATAU asal & tujuan (nama lokasi, misal: ?asal=Kualanamu&tujuan=Soekarno-Hatta)
     * - jam (opsional, format HH:MM, misal: ?jam=08:30)
     */
    public function tarif()
    {
        $lokasiModel   = new LokasiModel();
        $jadwalModel = new JadwalModel();

        $idAsal       = $this->request->getGet('id_asal');
        $idTujuan     = $this->request->getGet('id_tujuan');
        $asal         = $this->request->getGet('asal');
        $tujuan       = $this->request->getGet('tujuan');
        $jamBerangkat = $this->request->getGet('jam');

        $lokasiAsal = null;
        if (!empty($idAsal)) {
            $lokasiAsal = $lokasiModel->find($idAsal);
        } elseif (!empty($asal)) {
            $lokasiAsal = $lokasiModel->like('nama_lokasi', trim($asal), 'both', null, true)->first();
        }

        $lokasiTujuan = null;
        if (!empty($idTujuan)) {
            $lokasiTujuan = $lokasiModel->find($idTujuan);
        } elseif (!empty($tujuan)) {
            $lokasiTujuan = $lokasiModel->like('nama_lokasi', trim($tujuan), 'both', null, true)->first();
        }

        if (!$lokasiAsal) {
            return $this->failNotFound('Lokasi asal tidak ditemukan. Masukkan parameter "id_asal" atau "asal" yang valid.');
        }

        if (!$lokasiTujuan) {
            return $this->failNotFound('Lokasi tujuan tidak ditemukan. Masukkan parameter "id_tujuan" atau "tujuan" yang valid.');
        }

        if ($lokasiAsal['id'] === $lokasiTujuan['id']) {
            return $this->failValidationErrors('Lokasi asal dan tujuan tidak boleh sama.');
        }

        $jarakKm = $this->hitungJarakKm(
            $lokasiAsal['latitude'],
            $lokasiAsal['longitude'],
            $lokasiTujuan['latitude'],
            $lokasiTujuan['longitude']
        );

        $jadwalList = $jadwalModel->cariJadwal($lokasiAsal['id'], $lokasiTujuan['id']);

        $hasilPilihan = [];
        foreach ($jadwalList as $j) {
            // Filter berdasarkan jam berangkat jika parameter 'jam' dikirim
            if (!empty($jamBerangkat)) {
                $waktuInput = strtotime($jamBerangkat);
                $waktuMulai = strtotime($j['start_hours']);
                $waktuAkhir = strtotime($j['ended_hours']);

                if ($waktuInput !== false) {
                    if ($waktuAkhir < $waktuMulai) {
                        // Rentang lintas tengah malam (misal 22:00 - 04:00)
                        if ($waktuInput < $waktuMulai && $waktuInput > $waktuAkhir) {
                            continue;
                        }
                    } else {
                        if ($waktuInput < $waktuMulai || $waktuInput > $waktuAkhir) {
                            continue;
                        }
                    }
                }
            }

            $tarifPerKm    = (float)$j['tarif_per_km'];
            $estimasiTarif = round($jarakKm * $tarifPerKm, -2);

            $hasilPilihan[] = [
                'id_jadwal'             => (int)$j['id'],
                'penyedia'              => $j['nama_penyedia'],
                'jenis_transport'       => $j['jenis_transport'],
                'jam_berlaku'           => substr($j['start_hours'], 0, 5) . ' - ' . substr($j['ended_hours'], 0, 5),
                'tarif_per_km'          => $tarifPerKm,
                'estimasi_tarif'        => (int)$estimasiTarif,
                'estimasi_tarif_format' => 'Rp ' . number_format($estimasiTarif, 0, ',', '.'),
            ];
        }

        // Urutkan otomatis dari estimasi tarif termurah
        usort($hasilPilihan, fn($a, $b) => $a['estimasi_tarif'] <=> $b['estimasi_tarif']);

        return $this->respond([
            'status' => 'success',
            'data'   => [
                'rute' => [
                    'asal' => [
                        'id'        => (int)$lokasiAsal['id'],
                        'nama_lokasi' => $lokasiAsal['nama_lokasi'],
                        'latitude'  => (float)$lokasiAsal['latitude'],
                        'longitude' => (float)$lokasiAsal['longitude'],
                    ],
                    'tujuan' => [
                        'id'        => (int)$lokasiTujuan['id'],
                        'nama_lokasi' => $lokasiTujuan['nama_lokasi'],
                        'latitude'  => (float)$lokasiTujuan['latitude'],
                        'longitude' => (float)$lokasiTujuan['longitude'],
                    ],
                    'jarak_km'      => $jarakKm,
                    'jam_berangkat' => !empty($jamBerangkat) ? $jamBerangkat : null,
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
}