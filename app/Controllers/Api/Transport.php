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
     * POST/GET /api/transport/rekomendasi-wisata
     * Rekomendasi rute dan estimasi biaya ke destinasi wisata:
     * - Mendeteksi apakah user di luar Sumut -> dicarikan bandara keberangkatan terdekat dari GPS user.
     * - Menyesuaikan bandara kedatangan gerbang Sumut (Kualanamu / Silangit) yang paling dekat ke lokasi wisata.
     * - Mencarikan stasiun KA / terminal bus terdekat dari koordinat objek wisata.
     * - Menyediakan opsi tarif Sekali Jalan (one-way) maupun Pulang-Pergi (roundtrip).
     *
     * Parameter:
     * - user_lat, user_lng (wajib)
     * - wisata_lat, wisata_lng (wajib)
     * - nama_wisata (opsional, string)
     * - trip_type (opsional, 'roundtrip' [default] atau 'oneway')
     */
    public function rekomendasiWisata()
    {
        $input = $this->request->getJSON(true) ?? $this->request->getVar();

        $userLat    = $input['user_lat'] ?? $this->request->getGet('user_lat');
        $userLng    = $input['user_lng'] ?? $this->request->getGet('user_lng');
        $wisataLat  = $input['wisata_lat'] ?? $this->request->getGet('wisata_lat');
        $wisataLng  = $input['wisata_lng'] ?? $this->request->getGet('wisata_lng');
        $namaWisata = $input['nama_wisata'] ?? $this->request->getGet('nama_wisata') ?? 'Destinasi Wisata';
        $tripType   = strtolower($input['trip_type'] ?? $this->request->getGet('trip_type') ?? 'roundtrip');

        if (!in_array($tripType, ['roundtrip', 'oneway'])) {
            $tripType = 'roundtrip';
        }

        if ($userLat === null || $userLng === null || $wisataLat === null || $wisataLng === null) {
            return $this->failValidationErrors([
                'error' => 'Parameter "user_lat", "user_lng", "wisata_lat", dan "wisata_lng" wajib diisi.',
            ]);
        }

        $userLat   = (float)$userLat;
        $userLng   = (float)$userLng;
        $wisataLat = (float)$wisataLat;
        $wisataLng = (float)$wisataLng;

        $kotaModel   = new KotaModel();
        $jadwalModel = new JadwalModel();
        $semuaKota   = $kotaModel->findAll();

        if (empty($semuaKota)) {
            return $this->failNotFound('Data lokasi transportasi belum tersedia di database.');
        }

        // 1. Klasifikasi Lokasi: Bandara vs Titik Transportasi Darat Sumut
        $bandaraList = [];
        $lokasiSumut = [];
        $bandaraKno  = null;
        $bandaraDtb  = null;

        foreach ($semuaKota as $k) {
            $nama = $k['nama_kota'];
            $isBandara = (stripos($nama, 'Bandara') !== false);

            if ($isBandara) {
                $bandaraList[] = $k;
                if (stripos($nama, '(KNO)') !== false) {
                    $bandaraKno = $k;
                }
                if (stripos($nama, '(DTB)') !== false) {
                    $bandaraDtb = $k;
                }
            }

            // Lokasi di Sumatera Utara: semua stasiun KA, terminal Sumut, & bandara Sumut
            $isSumut = (
                stripos($nama, 'Stasiun') !== false ||
                stripos($nama, 'Terminal Amplas') !== false ||
                stripos($nama, 'Terminal Pinang Baris') !== false ||
                stripos($nama, 'Terminal Tanjung Pinggir') !== false ||
                stripos($nama, 'Terminal Palopat Pijor Koling') !== false ||
                stripos($nama, '(KNO)') !== false ||
                stripos($nama, '(DTB)') !== false
            );

            if ($isSumut) {
                $lokasiSumut[] = $k;
            }
        }

        // 2. Cari Titik Pemberhentian Transportasi Terdekat ke Objek Wisata
        $titikWisataTerdekat = null;
        $jarakTerdekatWisata = PHP_FLOAT_MAX;
        foreach ($lokasiSumut as $lok) {
            $dist = $this->hitungJarakKm($wisataLat, $wisataLng, $lok['latitude'], $lok['longitude']);
            if ($dist < $jarakTerdekatWisata) {
                $jarakTerdekatWisata = $dist;
                $titikWisataTerdekat = $lok;
            }
        }

        // 3. Deteksi Posisi User: Luar Sumut vs Dalam Sumut
        // Batas koordinat geografis perkiraan Provinsi Sumatera Utara (Lat: 0.5 s/d 4.3, Lng: 97.0 s/d 100.5)
        $isLuarSumut = ($userLat < 0.5 || $userLat > 4.3 || $userLng < 97.0 || $userLng > 100.5);

        $perjalananPergi = [];
        $totalTarifPergiMin = 0;

        if ($isLuarSumut) {
            // ==========================================
            // KASUS A: USER BERADA DI LUAR SUMATERA UTARA
            // ==========================================
            // A1. Cari Bandara Keberangkatan Terdekat dari User
            $bandaraAsal = null;
            $jarakKeBandaraAsal = PHP_FLOAT_MAX;

            foreach ($bandaraList as $b) {
                // Kecualikan bandara di Sumut
                if (stripos($b['nama_kota'], '(KNO)') !== false || stripos($b['nama_kota'], '(DTB)') !== false) {
                    continue;
                }
                $dist = $this->hitungJarakKm($userLat, $userLng, $b['latitude'], $b['longitude']);
                if ($dist < $jarakKeBandaraAsal) {
                    $jarakKeBandaraAsal = $dist;
                    $bandaraAsal = $b;
                }
            }

            // A2. Tentukan Bandara Gerbang Kedatangan di Sumut (KNO vs DTB)
            // Cek apakah lokasi wisata lebih dekat ke Silangit (DTB) dan terdapat penerbangan langsung
            $bandaraTujuanSumut = $bandaraKno; // default ke Kualanamu sebagai hub utama
            if ($bandaraDtb && $bandaraAsal) {
                $distWisataKeDtb = $this->hitungJarakKm($wisataLat, $wisataLng, $bandaraDtb['latitude'], $bandaraDtb['longitude']);
                $distWisataKeKno = $this->hitungJarakKm($wisataLat, $wisataLng, $bandaraKno['latitude'], $bandaraKno['longitude']);

                if ($distWisataKeDtb < $distWisataKeKno) {
                    // Cek apakah ada jadwal dari bandara asal ke DTB
                    $cekJadwalDtb = $jadwalModel->cariJadwal($bandaraAsal['id'], $bandaraDtb['id']);
                    if (!empty($cekJadwalDtb)) {
                        $bandaraTujuanSumut = $bandaraDtb;
                    }
                }
            }

            // A3. Rute Leg 1: Pesawat (Bandara Asal -> Bandara Sumut)
            $jadwalPesawat = $jadwalModel->cariJadwal($bandaraAsal['id'], $bandaraTujuanSumut['id']);
            $jarakPenerbangan = $this->hitungJarakKm(
                $bandaraAsal['latitude'], $bandaraAsal['longitude'],
                $bandaraTujuanSumut['latitude'], $bandaraTujuanSumut['longitude']
            );

            $pilihanPesawat = [];
            foreach ($jadwalPesawat as $jp) {
                $tarifKm = (float)$jp['tarif_per_km'];
                $estimasi = round($jarakPenerbangan * $tarifKm, -2);
                $pilihanPesawat[] = [
                    'id_jadwal'             => (int)$jp['id'],
                    'penyedia'              => $jp['nama_penyedia'],
                    'jenis_transport'       => $jp['jenis_transport'],
                    'jam_berlaku'           => substr($jp['start_hours'], 0, 5) . ' - ' . substr($jp['ended_hours'], 0, 5),
                    'estimasi_tarif'        => (int)$estimasi,
                    'estimasi_tarif_format' => 'Rp ' . number_format($estimasi, 0, ',', '.'),
                ];
            }
            usort($pilihanPesawat, fn($a, $b) => $a['estimasi_tarif'] <=> $b['estimasi_tarif']);
            $tarifPesawatMin = !empty($pilihanPesawat) ? $pilihanPesawat[0]['estimasi_tarif'] : 0;
            $totalTarifPergiMin += $tarifPesawatMin;

            $perjalananPergi[] = [
                'segmen'              => 1,
                'jenis_segmen'        => 'Penerbangan',
                'deskripsi'           => "Penerbangan dari bandara terdekat user ({$bandaraAsal['nama_kota']}) menuju gerbang Sumatera Utara ({$bandaraTujuanSumut['nama_kota']})",
                'titik_berangkat'     => [
                    'id'        => (int)$bandaraAsal['id'],
                    'nama_kota' => $bandaraAsal['nama_kota'],
                    'latitude'  => (float)$bandaraAsal['latitude'],
                    'longitude' => (float)$bandaraAsal['longitude'],
                    'jarak_dari_user_km' => $jarakKeBandaraAsal,
                ],
                'titik_tiba'          => [
                    'id'        => (int)$bandaraTujuanSumut['id'],
                    'nama_kota' => $bandaraTujuanSumut['nama_kota'],
                    'latitude'  => (float)$bandaraTujuanSumut['latitude'],
                    'longitude' => (float)$bandaraTujuanSumut['longitude'],
                ],
                'jarak_km'            => $jarakPenerbangan,
                'tarif_termurah'      => $tarifPesawatMin,
                'tarif_termurah_format' => 'Rp ' . number_format($tarifPesawatMin, 0, ',', '.'),
                'opsi_armada'         => $pilihanPesawat,
            ];

            // A4. Rute Leg 2: Transportasi Darat Lanjutan (Bandara Sumut -> Titik Terdekat Wisata)
            if ($bandaraTujuanSumut['id'] !== $titikWisataTerdekat['id']) {
                $jarakDarat = $this->hitungJarakKm(
                    $bandaraTujuanSumut['latitude'], $bandaraTujuanSumut['longitude'],
                    $titikWisataTerdekat['latitude'], $titikWisataTerdekat['longitude']
                );

                $jadwalDarat = $jadwalModel->cariJadwal($bandaraTujuanSumut['id'], $titikWisataTerdekat['id']);
                $pilihanDarat = [];

                if (!empty($jadwalDarat)) {
                    foreach ($jadwalDarat as $jd) {
                        $tarifKm = (float)$jd['tarif_per_km'];
                        $estimasi = round($jarakDarat * $tarifKm, -2);
                        $pilihanDarat[] = [
                            'id_jadwal'             => (int)$jd['id'],
                            'penyedia'              => $jd['nama_penyedia'],
                            'jenis_transport'       => $jd['jenis_transport'],
                            'jam_berlaku'           => substr($jd['start_hours'], 0, 5) . ' - ' . substr($jd['ended_hours'], 0, 5),
                            'estimasi_tarif'        => (int)$estimasi,
                            'estimasi_tarif_format' => 'Rp ' . number_format($estimasi, 0, ',', '.'),
                        ];
                    }
                } else {
                    // Estimasi standar moda Bus / Shuttle terdekat (tarif rata-rata bus Sumut Rp 450/km)
                    $estimasiBus = round($jarakDarat * 450.00, -2);
                    $pilihanDarat[] = [
                        'id_jadwal'             => null,
                        'penyedia'              => 'Bus / Shuttle DAMRI Lanjutan',
                        'jenis_transport'       => 'Bus',
                        'jam_berlaku'           => '06:00 - 21:00',
                        'estimasi_tarif'        => (int)$estimasiBus,
                        'estimasi_tarif_format' => 'Rp ' . number_format($estimasiBus, 0, ',', '.'),
                    ];
                }

                usort($pilihanDarat, fn($a, $b) => $a['estimasi_tarif'] <=> $b['estimasi_tarif']);
                $tarifDaratMin = !empty($pilihanDarat) ? $pilihanDarat[0]['estimasi_tarif'] : 0;
                $totalTarifPergiMin += $tarifDaratMin;

                $perjalananPergi[] = [
                    'segmen'              => 2,
                    'jenis_segmen'        => 'Transportasi Darat',
                    'deskripsi'           => "Perjalanan darat dari bandara kedatangan ({$bandaraTujuanSumut['nama_kota']}) menuju titik transportasi terdekat dari objek wisata ({$titikWisataTerdekat['nama_kota']})",
                    'titik_berangkat'     => [
                        'id'        => (int)$bandaraTujuanSumut['id'],
                        'nama_kota' => $bandaraTujuanSumut['nama_kota'],
                        'latitude'  => (float)$bandaraTujuanSumut['latitude'],
                        'longitude' => (float)$bandaraTujuanSumut['longitude'],
                    ],
                    'titik_tiba'          => [
                        'id'        => (int)$titikWisataTerdekat['id'],
                        'nama_kota' => $titikWisataTerdekat['nama_kota'],
                        'latitude'  => (float)$titikWisataTerdekat['latitude'],
                        'longitude' => (float)$titikWisataTerdekat['longitude'],
                    ],
                    'jarak_km'            => $jarakDarat,
                    'jarak_ke_lokasi_wisata_km' => $jarakTerdekatWisata,
                    'tarif_termurah'      => $tarifDaratMin,
                    'tarif_termurah_format' => 'Rp ' . number_format($tarifDaratMin, 0, ',', '.'),
                    'opsi_armada'         => $pilihanDarat,
                ];
            }
        } else {
            // ==========================================
            // KASUS B: USER SUDAH BERADA DI SUMATERA UTARA
            // ==========================================
            // Cari stasiun/terminal terdekat dari koordinat user
            $titikAsalUser = null;
            $jarakUserKeTitik = PHP_FLOAT_MAX;
            foreach ($lokasiSumut as $lok) {
                $dist = $this->hitungJarakKm($userLat, $userLng, $lok['latitude'], $lok['longitude']);
                if ($dist < $jarakUserKeTitik) {
                    $jarakUserKeTitik = $dist;
                    $titikAsalUser = $lok;
                }
            }

            $jarakDarat = $this->hitungJarakKm(
                $titikAsalUser['latitude'], $titikAsalUser['longitude'],
                $titikWisataTerdekat['latitude'], $titikWisataTerdekat['longitude']
            );

            $jadwalDarat = $jadwalModel->cariJadwal($titikAsalUser['id'], $titikWisataTerdekat['id']);
            $pilihanDarat = [];

            if (!empty($jadwalDarat)) {
                foreach ($jadwalDarat as $jd) {
                    $tarifKm = (float)$jd['tarif_per_km'];
                    $estimasi = round($jarakDarat * $tarifKm, -2);
                    $pilihanDarat[] = [
                        'id_jadwal'             => (int)$jd['id'],
                        'penyedia'              => $jd['nama_penyedia'],
                        'jenis_transport'       => $jd['jenis_transport'],
                        'jam_berlaku'           => substr($jd['start_hours'], 0, 5) . ' - ' . substr($jd['ended_hours'], 0, 5),
                        'estimasi_tarif'        => (int)$estimasi,
                        'estimasi_tarif_format' => 'Rp ' . number_format($estimasi, 0, ',', '.'),
                    ];
                }
            } else {
                $estimasiStandar = round($jarakDarat * 400.00, -2);
                $pilihanDarat[] = [
                    'id_jadwal'             => null,
                    'penyedia'              => 'Kereta Api / Bus Antar-Kota Sumut',
                    'jenis_transport'       => 'Bus / Kereta',
                    'jam_berlaku'           => '06:00 - 21:00',
                    'estimasi_tarif'        => (int)$estimasiStandar,
                    'estimasi_tarif_format' => 'Rp ' . number_format($estimasiStandar, 0, ',', '.'),
                ];
            }

            usort($pilihanDarat, fn($a, $b) => $a['estimasi_tarif'] <=> $b['estimasi_tarif']);
            $tarifDaratMin = !empty($pilihanDarat) ? $pilihanDarat[0]['estimasi_tarif'] : 0;
            $totalTarifPergiMin += $tarifDaratMin;

            $perjalananPergi[] = [
                'segmen'              => 1,
                'jenis_segmen'        => 'Transportasi Darat Lokal',
                'deskripsi'           => "Perjalanan darat dari titik terdekat user ({$titikAsalUser['nama_kota']}) menuju titik transportasi objek wisata ({$titikWisataTerdekat['nama_kota']})",
                'titik_berangkat'     => [
                    'id'        => (int)$titikAsalUser['id'],
                    'nama_kota' => $titikAsalUser['nama_kota'],
                    'latitude'  => (float)$titikAsalUser['latitude'],
                    'longitude' => (float)$titikAsalUser['longitude'],
                    'jarak_dari_user_km' => $jarakUserKeTitik,
                ],
                'titik_tiba'          => [
                    'id'        => (int)$titikWisataTerdekat['id'],
                    'nama_kota' => $titikWisataTerdekat['nama_kota'],
                    'latitude'  => (float)$titikWisataTerdekat['latitude'],
                    'longitude' => (float)$titikWisataTerdekat['longitude'],
                ],
                'jarak_km'            => $jarakDarat,
                'jarak_ke_lokasi_wisata_km' => $jarakTerdekatWisata,
                'tarif_termurah'      => $tarifDaratMin,
                'tarif_termurah_format' => 'Rp ' . number_format($tarifDaratMin, 0, ',', '.'),
                'opsi_armada'         => $pilihanDarat,
            ];
        }

        // 4. Kalkulasi Pulang-Pergi (Round-Trip) vs Sekali Jalan (One-Way)
        $totalEstimasiKeseluruhan = ($tripType === 'roundtrip')
            ? ($totalTarifPergiMin * 2)
            : $totalTarifPergiMin;

        $rincianPulang = null;
        if ($tripType === 'roundtrip') {
            // Struktur perjalanan pulang (arah sebaliknya)
            $rincianPulang = [];
            $reversedSegmen = array_reverse($perjalananPergi);
            $segmenIdx = 1;
            foreach ($reversedSegmen as $s) {
                $rincianPulang[] = [
                    'segmen'              => $segmenIdx++,
                    'jenis_segmen'        => $s['jenis_segmen'],
                    'deskripsi'           => "Perjalanan pulang: {$s['titik_tiba']['nama_kota']} kembali menuju {$s['titik_berangkat']['nama_kota']}",
                    'titik_berangkat'     => $s['titik_tiba'],
                    'titik_tiba'          => $s['titik_berangkat'],
                    'jarak_km'            => $s['jarak_km'],
                    'tarif_termurah'      => $s['tarif_termurah'],
                    'tarif_termurah_format' => $s['tarif_termurah_format'],
                    'opsi_armada'         => $s['opsi_armada'],
                ];
            }
        }

        return $this->respond([
            'status' => 'success',
            'data'   => [
                'metadata' => [
                    'nama_wisata'         => $namaWisata,
                    'koordinat_wisata'    => ['latitude' => $wisataLat, 'longitude' => $wisataLng],
                    'koordinat_user'      => ['latitude' => $userLat, 'longitude' => $userLng],
                    'user_di_luar_sumut'  => $isLuarSumut,
                    'trip_type'           => $tripType,
                    'tipe_perjalanan'     => ($tripType === 'roundtrip') ? 'Pulang-Pergi (PP)' : 'Sekali Jalan (One-Way)',
                    'stasiun_terdekat_wisata' => [
                        'id'        => (int)$titikWisataTerdekat['id'],
                        'nama_kota' => $titikWisataTerdekat['nama_kota'],
                        'latitude'  => (float)$titikWisataTerdekat['latitude'],
                        'longitude' => (float)$titikWisataTerdekat['longitude'],
                        'jarak_ke_objek_wisata_km' => $jarakTerdekatWisata,
                    ],
                ],
                'ringkasan_biaya' => [
                    'biaya_pergi'           => $totalTarifPergiMin,
                    'biaya_pergi_format'    => 'Rp ' . number_format($totalTarifPergiMin, 0, ',', '.'),
                    'biaya_pulang'          => ($tripType === 'roundtrip') ? $totalTarifPergiMin : 0,
                    'biaya_pulang_format'   => ($tripType === 'roundtrip') ? ('Rp ' . number_format($totalTarifPergiMin, 0, ',', '.')) : 'Rp 0',
                    'total_biaya_estimasi'  => $totalEstimasiKeseluruhan,
                    'total_biaya_format'    => 'Rp ' . number_format($totalEstimasiKeseluruhan, 0, ',', '.'),
                    'keterangan'            => 'Estimasi menggunakan opsi armada termurah untuk setiap segmen rute.',
                ],
                'rute_perjalanan' => [
                    'pergi'  => $perjalananPergi,
                    'pulang' => $rincianPulang,
                ],
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