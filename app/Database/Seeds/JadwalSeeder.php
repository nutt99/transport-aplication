<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class JadwalSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // Mapping nama_penyedia ke id
        $penyediaRows = $this->db->table('penyedia')->get()->getResultArray();
        $penyediaMap = [];
        foreach ($penyediaRows as $p) {
            $penyediaMap[$p['nama_penyedia']] = $p['id'];
        }

        // Mapping nama_kota ke id
        $kotaRows = $this->db->table('kota')->get()->getResultArray();
        $kotaMap = [];
        foreach ($kotaRows as $k) {
            $kotaMap[$k['nama_kota']] = $k['id'];
        }

        $jadwalList = [
            // ==========================================
            // KERETA API SUMATRA (PT KAI)
            // ==========================================
            // Medan <-> Kualanamu (Tarif pagi/siang vs malam)
            [
                'penyedia'     => 'PT Kereta Api Indonesia (Persero)',
                'kota_asal'    => 'Medan - Stasiun Medan (MDN)',
                'kota_tujuan'  => 'Deli Serdang - Stasiun Kualanamu (KNM)',
                'start_hours'  => '05:00:00',
                'ended_hours'  => '17:00:00',
                'tarif_per_km' => 1800.00,
            ],
            [
                'penyedia'     => 'PT Kereta Api Indonesia (Persero)',
                'kota_asal'    => 'Medan - Stasiun Medan (MDN)',
                'kota_tujuan'  => 'Deli Serdang - Stasiun Kualanamu (KNM)',
                'start_hours'  => '17:01:00',
                'ended_hours'  => '22:00:00',
                'tarif_per_km' => 2100.00,
            ],
            [
                'penyedia'     => 'PT Kereta Api Indonesia (Persero)',
                'kota_asal'    => 'Deli Serdang - Stasiun Kualanamu (KNM)',
                'kota_tujuan'  => 'Medan - Stasiun Medan (MDN)',
                'start_hours'  => '06:00:00',
                'ended_hours'  => '23:00:00',
                'tarif_per_km' => 1800.00,
            ],
            // Medan <-> Binjai
            [
                'penyedia'     => 'PT Kereta Api Indonesia (Persero)',
                'kota_asal'    => 'Medan - Stasiun Medan (MDN)',
                'kota_tujuan'  => 'Binjai - Stasiun Binjai (BIJ)',
                'start_hours'  => '05:00:00',
                'ended_hours'  => '21:00:00',
                'tarif_per_km' => 450.00,
            ],
            [
                'penyedia'     => 'PT Kereta Api Indonesia (Persero)',
                'kota_asal'    => 'Binjai - Stasiun Binjai (BIJ)',
                'kota_tujuan'  => 'Medan - Stasiun Medan (MDN)',
                'start_hours'  => '05:30:00',
                'ended_hours'  => '21:30:00',
                'tarif_per_km' => 450.00,
            ],
            // Medan <-> Pematangsiantar
            [
                'penyedia'     => 'PT Kereta Api Indonesia (Persero)',
                'kota_asal'    => 'Medan - Stasiun Medan (MDN)',
                'kota_tujuan'  => 'Pematangsiantar - Stasiun Siantar (SIR)',
                'start_hours'  => '07:00:00',
                'ended_hours'  => '19:00:00',
                'tarif_per_km' => 400.00,
            ],
            [
                'penyedia'     => 'PT Kereta Api Indonesia (Persero)',
                'kota_asal'    => 'Pematangsiantar - Stasiun Siantar (SIR)',
                'kota_tujuan'  => 'Medan - Stasiun Medan (MDN)',
                'start_hours'  => '06:30:00',
                'ended_hours'  => '18:30:00',
                'tarif_per_km' => 400.00,
            ],
            // Padang <-> Bandara Minangkabau
            [
                'penyedia'     => 'PT Kereta Api Indonesia (Persero)',
                'kota_asal'    => 'Padang - Stasiun Padang (PD)',
                'kota_tujuan'  => 'Padang Pariaman - Stasiun Bandara Internasional Minangkabau (BIM)',
                'start_hours'  => '06:00:00',
                'ended_hours'  => '20:00:00',
                'tarif_per_km' => 500.00,
            ],
            // Palembang <-> Bandar Lampung
            [
                'penyedia'     => 'PT Kereta Api Indonesia (Persero)',
                'kota_asal'    => 'Palembang - Stasiun Kertapati (KPT)',
                'kota_tujuan'  => 'Bandar Lampung - Stasiun Tanjungkarang (TNK)',
                'start_hours'  => '08:00:00',
                'ended_hours'  => '15:00:00',
                'tarif_per_km' => 550.00,
            ],
            [
                'penyedia'     => 'PT Kereta Api Indonesia (Persero)',
                'kota_asal'    => 'Palembang - Stasiun Kertapati (KPT)',
                'kota_tujuan'  => 'Bandar Lampung - Stasiun Tanjungkarang (TNK)',
                'start_hours'  => '20:00:00',
                'ended_hours'  => '23:59:00',
                'tarif_per_km' => 650.00,
            ],

            // ==========================================
            // BUS SUMATRA (DAMRI)
            // ==========================================
            // Terminal Amplas <-> Bandara Kualanamu
            [
                'penyedia'     => 'DAMRI',
                'kota_asal'    => 'Medan - Terminal Amplas',
                'kota_tujuan'  => 'Medan - Bandara Kualanamu (KNO)',
                'start_hours'  => '05:00:00',
                'ended_hours'  => '18:00:00',
                'tarif_per_km' => 1100.00,
            ],
            [
                'penyedia'     => 'DAMRI',
                'kota_asal'    => 'Medan - Terminal Amplas',
                'kota_tujuan'  => 'Medan - Bandara Kualanamu (KNO)',
                'start_hours'  => '18:01:00',
                'ended_hours'  => '23:00:00',
                'tarif_per_km' => 1300.00,
            ],
            [
                'penyedia'     => 'DAMRI',
                'kota_asal'    => 'Medan - Bandara Kualanamu (KNO)',
                'kota_tujuan'  => 'Medan - Terminal Amplas',
                'start_hours'  => '06:00:00',
                'ended_hours'  => '23:30:00',
                'tarif_per_km' => 1100.00,
            ],
            // Medan <-> Banda Aceh
            [
                'penyedia'     => 'DAMRI',
                'kota_asal'    => 'Medan - Terminal Pinang Baris',
                'kota_tujuan'  => 'Banda Aceh - Terminal Batoh',
                'start_hours'  => '17:00:00',
                'ended_hours'  => '23:00:00',
                'tarif_per_km' => 420.00,
            ],
            [
                'penyedia'     => 'DAMRI',
                'kota_asal'    => 'Banda Aceh - Terminal Batoh',
                'kota_tujuan'  => 'Medan - Terminal Pinang Baris',
                'start_hours'  => '17:00:00',
                'ended_hours'  => '23:00:00',
                'tarif_per_km' => 420.00,
            ],
            // Medan <-> Pekanbaru
            [
                'penyedia'     => 'DAMRI',
                'kota_asal'    => 'Medan - Terminal Amplas',
                'kota_tujuan'  => 'Pekanbaru - Terminal Bandar Raya Payung Sekaki',
                'start_hours'  => '08:00:00',
                'ended_hours'  => '16:00:00',
                'tarif_per_km' => 380.00,
            ],
            [
                'penyedia'     => 'DAMRI',
                'kota_asal'    => 'Medan - Terminal Amplas',
                'kota_tujuan'  => 'Pekanbaru - Terminal Bandar Raya Payung Sekaki',
                'start_hours'  => '16:01:00',
                'ended_hours'  => '23:00:00',
                'tarif_per_km' => 450.00,
            ],
            // Medan <-> Padang
            [
                'penyedia'     => 'DAMRI',
                'kota_asal'    => 'Medan - Terminal Amplas',
                'kota_tujuan'  => 'Padang - Terminal Anak Air',
                'start_hours'  => '14:00:00',
                'ended_hours'  => '21:00:00',
                'tarif_per_km' => 400.00,
            ],
            // Palembang <-> Bandar Lampung
            [
                'penyedia'     => 'DAMRI',
                'kota_asal'    => 'Palembang - Terminal Alang-Alang Lebar',
                'kota_tujuan'  => 'Bandar Lampung - Terminal Rajabasa',
                'start_hours'  => '07:00:00',
                'ended_hours'  => '21:00:00',
                'tarif_per_km' => 390.00,
            ],

            // ==========================================
            // PESAWAT ANTAR-BANDARA INDONESIA
            // ==========================================
            // Kualanamu (KNO) <-> Soekarno-Hatta (CGK) - Multi Maskapai & Rentang Jam
            [
                'penyedia'     => 'Garuda Indonesia',
                'kota_asal'    => 'Medan - Bandara Kualanamu (KNO)',
                'kota_tujuan'  => 'Jakarta / Tangerang - Bandara Soekarno-Hatta (CGK)',
                'start_hours'  => '06:00:00',
                'ended_hours'  => '15:00:00',
                'tarif_per_km' => 1700.00,
            ],
            [
                'penyedia'     => 'Garuda Indonesia',
                'kota_asal'    => 'Medan - Bandara Kualanamu (KNO)',
                'kota_tujuan'  => 'Jakarta / Tangerang - Bandara Soekarno-Hatta (CGK)',
                'start_hours'  => '15:01:00',
                'ended_hours'  => '22:00:00',
                'tarif_per_km' => 1950.00,
            ],
            [
                'penyedia'     => 'Citilink',
                'kota_asal'    => 'Medan - Bandara Kualanamu (KNO)',
                'kota_tujuan'  => 'Jakarta / Tangerang - Bandara Soekarno-Hatta (CGK)',
                'start_hours'  => '06:00:00',
                'ended_hours'  => '21:00:00',
                'tarif_per_km' => 1250.00,
            ],
            [
                'penyedia'     => 'Lion Air',
                'kota_asal'    => 'Medan - Bandara Kualanamu (KNO)',
                'kota_tujuan'  => 'Jakarta / Tangerang - Bandara Soekarno-Hatta (CGK)',
                'start_hours'  => '05:00:00',
                'ended_hours'  => '22:00:00',
                'tarif_per_km' => 1100.00,
            ],
            [
                'penyedia'     => 'Super Air Jet',
                'kota_asal'    => 'Medan - Bandara Kualanamu (KNO)',
                'kota_tujuan'  => 'Jakarta / Tangerang - Bandara Soekarno-Hatta (CGK)',
                'start_hours'  => '07:00:00',
                'ended_hours'  => '21:00:00',
                'tarif_per_km' => 1150.00,
            ],
            [
                'penyedia'     => 'Garuda Indonesia',
                'kota_asal'    => 'Jakarta / Tangerang - Bandara Soekarno-Hatta (CGK)',
                'kota_tujuan'  => 'Medan - Bandara Kualanamu (KNO)',
                'start_hours'  => '06:00:00',
                'ended_hours'  => '21:00:00',
                'tarif_per_km' => 1700.00,
            ],
            [
                'penyedia'     => 'Lion Air',
                'kota_asal'    => 'Jakarta / Tangerang - Bandara Soekarno-Hatta (CGK)',
                'kota_tujuan'  => 'Medan - Bandara Kualanamu (KNO)',
                'start_hours'  => '06:00:00',
                'ended_hours'  => '22:00:00',
                'tarif_per_km' => 1100.00,
            ],

            // Kualanamu (KNO) <-> Batam (BTH) & Padang (PDG) & Palembang (PLM)
            [
                'penyedia'     => 'Lion Air',
                'kota_asal'    => 'Medan - Bandara Kualanamu (KNO)',
                'kota_tujuan'  => 'Batam - Bandara Hang Nadim (BTH)',
                'start_hours'  => '07:00:00',
                'ended_hours'  => '20:00:00',
                'tarif_per_km' => 1150.00,
            ],
            [
                'penyedia'     => 'Super Air Jet',
                'kota_asal'    => 'Medan - Bandara Kualanamu (KNO)',
                'kota_tujuan'  => 'Padang - Bandara Minangkabau (PDG)',
                'start_hours'  => '08:00:00',
                'ended_hours'  => '19:00:00',
                'tarif_per_km' => 1200.00,
            ],
            [
                'penyedia'     => 'Citilink',
                'kota_asal'    => 'Medan - Bandara Kualanamu (KNO)',
                'kota_tujuan'  => 'Palembang - Bandara Sultan Mahmud Badaruddin II (PLM)',
                'start_hours'  => '07:00:00',
                'ended_hours'  => '18:00:00',
                'tarif_per_km' => 1250.00,
            ],

            // Rute Populer Nasional (CGK <-> DPS, SUB, YIA, UPG)
            [
                'penyedia'     => 'Garuda Indonesia',
                'kota_asal'    => 'Jakarta / Tangerang - Bandara Soekarno-Hatta (CGK)',
                'kota_tujuan'  => 'Denpasar / Bali - Bandara I Gusti Ngurah Rai (DPS)',
                'start_hours'  => '06:00:00',
                'ended_hours'  => '22:00:00',
                'tarif_per_km' => 1850.00,
            ],
            [
                'penyedia'     => 'Citilink',
                'kota_asal'    => 'Jakarta / Tangerang - Bandara Soekarno-Hatta (CGK)',
                'kota_tujuan'  => 'Denpasar / Bali - Bandara I Gusti Ngurah Rai (DPS)',
                'start_hours'  => '06:00:00',
                'ended_hours'  => '22:00:00',
                'tarif_per_km' => 1300.00,
            ],
            [
                'penyedia'     => 'Citilink',
                'kota_asal'    => 'Jakarta / Tangerang - Bandara Soekarno-Hatta (CGK)',
                'kota_tujuan'  => 'Surabaya - Bandara Juanda (SUB)',
                'start_hours'  => '06:00:00',
                'ended_hours'  => '21:00:00',
                'tarif_per_km' => 1200.00,
            ],
            [
                'penyedia'     => 'Super Air Jet',
                'kota_asal'    => 'Jakarta / Tangerang - Bandara Soekarno-Hatta (CGK)',
                'kota_tujuan'  => 'Yogyakarta - Bandara Internasional Yogyakarta (YIA)',
                'start_hours'  => '06:00:00',
                'ended_hours'  => '20:00:00',
                'tarif_per_km' => 1150.00,
            ],
            [
                'penyedia'     => 'Lion Air',
                'kota_asal'    => 'Jakarta / Tangerang - Bandara Soekarno-Hatta (CGK)',
                'kota_tujuan'  => 'Makassar - Bandara Sultan Hasanuddin (UPG)',
                'start_hours'  => '06:00:00',
                'ended_hours'  => '22:00:00',
                'tarif_per_km' => 1100.00,
            ],
        ];

        $data = [];
        foreach ($jadwalList as $item) {
            $idPenyedia = $penyediaMap[$item['penyedia']] ?? null;
            $idAsal     = $kotaMap[$item['kota_asal']] ?? null;
            $idTujuan   = $kotaMap[$item['kota_tujuan']] ?? null;

            // Lewati jika ada nama yang tidak cocok agar seeding tidak gagal
            if (!$idPenyedia || !$idAsal || !$idTujuan) {
                continue;
            }

            $data[] = [
                'id_penyedia'    => $idPenyedia,
                'id_kota_asal'   => $idAsal,
                'id_kota_tujuan' => $idTujuan,
                'start_hours'    => $item['start_hours'],
                'ended_hours'    => $item['ended_hours'],
                'tarif_per_km'   => $item['tarif_per_km'],
                'created_at'     => $now,
                'updated_at'     => $now,
            ];
        }

        if (!empty($data)) {
            $this->db->table('jadwal')->insertBatch($data);
        }
    }
}