<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class JadwalTerminalSumutSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $penyediaRows = $this->db->table('penyedia')
            ->select('penyedia.id, penyedia.nama_penyedia, transport.jenis')
            ->join('transport', 'transport.id = penyedia.id_transport')
            ->get()
            ->getResultArray();

        $sumutKeywords = [
            'DAMRI', 'ALS', 'Sempati Star', 'Intra', 'Sinabung Jaya', 'Makmur',
            'Paradep', 'Nice Trans', 'Railink', 'Kereta Api Indonesia', 'ASDP', 'PELNI',
        ];

        $busList     = [];
        $shuttleList = [];
        $keretaList  = [];
        $kapalList   = [];

        foreach ($penyediaRows as $p) {
            $isSumut = false;
            foreach ($sumutKeywords as $kw) {
                if (stripos($p['nama_penyedia'], $kw) !== false) {
                    $isSumut = true;
                    break;
                }
            }
            if (! $isSumut) {
                continue;
            }

            $jenis = strtolower($p['jenis']);
            $id    = (int) $p['id'];

            if (str_contains($jenis, 'bus')) {
                $busList[] = $id;
            } elseif (str_contains($jenis, 'shuttle') || str_contains($jenis, 'travel')) {
                $shuttleList[] = $id;
            } elseif (str_contains($jenis, 'kereta')) {
                $keretaList[] = $id;
            } elseif (str_contains($jenis, 'kapal')) {
                $kapalList[] = $id;
            }
        }

        // Hub sesuai persis dengan nama di LokasiSeeder.php
        $hubNames = [
            'Medan - Bandara Kualanamu (KNO)',
            'Danau Toba / Siborong-Borong - Bandara Silangit (DTB)',
            'Medan - Stasiun Medan (MDN)',
            'Medan - Terminal Amplas',
            'Medan - Terminal Pinang Baris',
        ];

        $hubRows = $this->db->table('lokasi')
            ->whereIn('nama_lokasi', $hubNames)
            ->get()
            ->getResultArray();

        if (empty($hubRows)) {
            echo "Hub utama tidak ditemukan. Pastikan LokasiSeeder sudah dijalankan.\n";
            return;
        }

        // Ambil semua terminal dengan pola "<Wilayah> - Terminal <Nama>"
        $terminals = $this->db->table('lokasi')
            ->like('nama_lokasi', ' - Terminal ')
            ->get()
            ->getResultArray();

        $existingRows = $this->db->table('jadwal')
            ->select('id_penyedia, id_lokasi_asal, id_lokasi_tujuan')
            ->get()
            ->getResultArray();

        $existingMap = [];
        foreach ($existingRows as $ex) {
            $existingMap[$ex['id_penyedia'] . '-' . $ex['id_lokasi_asal'] . '-' . $ex['id_lokasi_tujuan']] = true;
        }

        $toInsert = [];
        $addPair  = function (int $penyediaId, int $asalId, int $tujuanId, string $start, string $end, float $tarif) use (&$toInsert, &$existingMap, $now) {
            if ($asalId === $tujuanId) {
                return;
            }
            foreach ([[$asalId, $tujuanId], [$tujuanId, $asalId]] as [$a, $b]) {
                $key = "{$penyediaId}-{$a}-{$b}";
                if (! isset($existingMap[$key])) {
                    $existingMap[$key] = true;
                    $toInsert[] = [
                        'id_penyedia'      => $penyediaId,
                        'id_lokasi_asal'   => $a,
                        'id_lokasi_tujuan' => $b,
                        'start_hours'      => $start,
                        'ended_hours'      => $end,
                        'tarif_per_km'     => $tarif,
                        'created_at'       => $now,
                        'updated_at'       => $now,
                    ];
                }
            }
        };

        foreach ($hubRows as $hub) {
            $hubId = (int) $hub['id'];

            foreach ($terminals as $term) {
                $termId   = (int) $term['id'];
                $termName = $term['nama_lokasi'];

                // Semua operator Bus Sumut tersedia sebagai opsi
                foreach ($busList as $k => $busId) {
                    $addPair($busId, $hubId, $termId, '05:00:00', '22:00:00', 750.00 + ($k * 120.00));
                }

                // Semua operator Travel / Shuttle Sumut tersedia sebagai opsi
                foreach ($shuttleList as $k => $shuttleId) {
                    $addPair($shuttleId, $hubId, $termId, '06:00:00', '21:30:00', 1450.00 + ($k * 200.00));
                }

                // Kereta Api untuk wilayah jalur rel Sumut
                if (! empty($keretaList) && preg_match('/^(Medan|Binjai|Langkat|Deli Serdang|Serdang Bedagai|Tebing Tinggi|Simalungun|Pematangsiantar|Batu Bara|Asahan|Tanjungbalai|Labuhanbatu)/i', $termName)) {
                    foreach ($keretaList as $kaId) {
                        $addPair($kaId, $hubId, $termId, '05:30:00', '21:00:00', 950.00);
                    }
                }

                // Kapal Laut / Feri untuk wilayah Danau Toba, Nias & pesisir
                if (! empty($kapalList) && preg_match('/^(Samosir|Simalungun|Toba|Nias|Gunungsitoli|Sibolga|Tapanuli Tengah|Tanjungbalai|Medan - Terminal Belawan)/i', $termName)) {
                    foreach ($kapalList as $kapalId) {
                        $addPair($kapalId, $hubId, $termId, '07:00:00', '18:00:00', 1200.00);
                    }
                }
            }
        }

        if (! empty($toInsert)) {
            foreach (array_chunk($toInsert, 500) as $chunk) {
                $this->db->table('jadwal')->insertBatch($chunk);
            }
        }

        echo 'Berhasil menambahkan ' . count($toInsert) . " jadwal multi-penyedia Sumut.\n";
    }
}