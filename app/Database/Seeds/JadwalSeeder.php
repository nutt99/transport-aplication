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

        $jadwalList = [];

        // Helper: Tambah rute bolak-balik (PP) secara otomatis
        $addPP = function (
            string $penyedia,
            string $asal,
            string $tujuan,
            string $start,
            string $end,
            float $tarifPerKm
        ) use (&$jadwalList) {
            $jadwalList[] = [
                'penyedia'     => $penyedia,
                'kota_asal'    => $asal,
                'kota_tujuan'  => $tujuan,
                'start_hours'  => $start,
                'ended_hours'  => $end,
                'tarif_per_km' => $tarifPerKm,
            ];
            $jadwalList[] = [
                'penyedia'     => $penyedia,
                'kota_asal'    => $tujuan,
                'kota_tujuan'  => $asal,
                'start_hours'  => $start,
                'ended_hours'  => $end,
                'tarif_per_km' => $tarifPerKm,
            ];
        };

        // Helper: Sambungkan rangkaian stasiun berurutan (PP) untuk pathfinding
        $addRailChain = function (
            string $penyedia,
            array $stations,
            string $start,
            string $end,
            float $tarifPerKm
        ) use ($addPP) {
            for ($i = 0; $i < count($stations) - 1; $i++) {
                $addPP($penyedia, $stations[$i], $stations[$i + 1], $start, $end, $tarifPerKm);
            }
        };

        // ==========================================
        // 1. KERETA API SUMATERA UTARA (28 STASIUN)
        // ==========================================
        // Jalur KA Bandara (Railink): MDN <-> KNM (~Rp 40.000 / 28 km)
        $addRailChain('KAI Bandara (Railink)', [
            'Medan - Stasiun Medan (MDN)',
            'Deli Serdang - Stasiun Bandar Khalipah (BKP)',
            'Deli Serdang - Stasiun Batang Kuis (BTK)',
            'Deli Serdang - Stasiun Araskabu (ARB)',
            'Deli Serdang - Stasiun Kualanamu (KNM)',
        ], '05:00:00', '22:30:00', 1500.00);

        // Jalur KA Srilelawangsa (PSO): Medan <-> Binjai <-> Kuala Bingai
        $addRailChain('PT Kereta Api Indonesia (Persero)', [
            'Medan - Stasiun Medan (MDN)',
            'Binjai - Stasiun Binjai (BIJ)',
            'Langkat - Stasiun Kuala Bingai (KBG)',
        ], '05:00:00', '21:30:00', 250.00);

        // Jalur Utama Timur (PSO): Araskabu <-> Tebing Tinggi
        $addRailChain('PT Kereta Api Indonesia (Persero)', [
            'Deli Serdang - Stasiun Araskabu (ARB)',
            'Deli Serdang - Stasiun Lubuk Pakam (LBP)',
            'Serdang Bedagai - Stasiun Perbaungan (PBA)',
            'Serdang Bedagai - Stasiun Lidah Tanah (LDT)',
            'Serdang Bedagai - Stasiun Teluk Mengkudu (TKD)',
            'Serdang Bedagai - Stasiun Rampah (RPH)',
            'Serdang Bedagai - Stasiun Bamban (BMB)',
            'Tebing Tinggi - Stasiun Tebing Tinggi (TBI)',
        ], '06:00:00', '20:00:00', 220.00);

        // Cabang KA Siantar Ekspres (PSO): Tebing Tinggi <-> Siantar
        $addRailChain('PT Kereta Api Indonesia (Persero)', [
            'Tebing Tinggi - Stasiun Tebing Tinggi (TBI)',
            'Simalungun - Stasiun Bajalinggei (BJL)',
            'Simalungun - Stasiun Dolok Merangir (DMR)',
            'Pematangsiantar - Stasiun Siantar (SIR)',
        ], '06:30:00', '19:30:00', 220.00);

        // Cabang KA Putri Deli (PSO): Tebing Tinggi <-> Kisaran <-> Tanjungbalai
        $addRailChain('PT Kereta Api Indonesia (Persero)', [
            'Tebing Tinggi - Stasiun Tebing Tinggi (TBI)',
            'Simalungun - Stasiun Perlanaan (PRA)',
            'Batu Bara - Stasiun Lima Puluh (LMP)',
            'Batu Bara - Stasiun Sei Bejangkar (SBJ)',
            'Asahan - Stasiun Kisaran (KIS)',
            'Tanjungbalai - Stasiun Tanjungbalai (TNB)',
        ], '07:00:00', '21:00:00', 200.00);

        // Cabang KA Sribilah Utama (Komersial): Kisaran <-> Rantau Prapat
        $addRailChain('PT Kereta Api Indonesia (Persero)', [
            'Asahan - Stasiun Kisaran (KIS)',
            'Asahan - Stasiun Hengelo (HL)',
            'Asahan - Stasiun Pulau Raja (PUR)',
            'Labuhanbatu Utara - Stasiun Mambang Muda (MBM)',
            'Labuhanbatu Utara - Stasiun Padang Halaban (PHA)',
            'Labuhanbatu Utara - Stasiun Marbau (MBU)',
            'Labuhanbatu - Stasiun Rantau Prapat (RAP)',
        ], '07:30:00', '22:00:00', 750.00);

        // ==========================================
        // 2. BUS SUMATRA & KONEKSI DALAM KOTA MEDAN
        // ==========================================
        // Shuttle DAMRI Bandara KNO <-> Terminal & Stasiun Medan
        $addPP('DAMRI', 'Medan - Bandara Kualanamu (KNO)', 'Medan - Terminal Amplas', '05:00:00', '23:00:00', 1100.00);
        $addPP('DAMRI', 'Medan - Bandara Kualanamu (KNO)', 'Medan - Terminal Pinang Baris', '06:00:00', '22:00:00', 1100.00);
        $addPP('DAMRI', 'Medan - Stasiun Medan (MDN)', 'Medan - Terminal Amplas', '06:00:00', '22:00:00', 1000.00);
        $addPP('DAMRI', 'Medan - Stasiun Medan (MDN)', 'Medan - Terminal Pinang Baris', '06:00:00', '22:00:00', 1000.00);

        // Bus AKAP Lintas Sumatra menuju/dari Medan & Sumut
        $addPP('PO Sempati Star', 'Medan - Terminal Pinang Baris', 'Banda Aceh - Terminal Batoh', '18:00:00', '23:00:00', 480.00);
        $addPP('PO Sempati Star', 'Medan - Terminal Pinang Baris', 'Lhokseumawe - Terminal Lhokseumawe', '18:00:00', '23:00:00', 480.00);
        $addPP('PO ALS (Antar Lintas Sumatera)', 'Medan - Terminal Amplas', 'Pematangsiantar - Terminal Tanjung Pinggir', '06:00:00', '21:00:00', 420.00);
        $addPP('PO ALS (Antar Lintas Sumatera)', 'Medan - Terminal Amplas', 'Padang Sidempuan - Terminal Palopat Pijor Koling', '16:00:00', '22:00:00', 450.00);
        $addPP('PO ALS (Antar Lintas Sumatera)', 'Medan - Terminal Amplas', 'Pekanbaru - Terminal Bandar Raya Payung Sekaki', '14:00:00', '21:00:00', 430.00);
        $addPP('PO ALS (Antar Lintas Sumatera)', 'Medan - Terminal Amplas', 'Dumai - Terminal Dumai', '15:00:00', '21:00:00', 430.00);
        $addPP('PO NPM', 'Medan - Terminal Amplas', 'Bukittinggi - Terminal Simpang Aur', '14:00:00', '20:00:00', 440.00);
        $addPP('PO NPM', 'Medan - Terminal Amplas', 'Padang - Terminal Anak Air', '14:00:00', '20:00:00', 440.00);
        $addPP('PO ALS (Antar Lintas Sumatera)', 'Medan - Terminal Amplas', 'Jambi - Terminal Alam Barajo', '12:00:00', '18:00:00', 420.00);
        $addPP('PO ALS (Antar Lintas Sumatera)', 'Medan - Terminal Amplas', 'Palembang - Terminal Alang-Alang Lebar', '10:00:00', '16:00:00', 410.00);
        $addPP('PO ALS (Antar Lintas Sumatera)', 'Medan - Terminal Amplas', 'Bandar Lampung - Terminal Rajabasa', '10:00:00', '15:00:00', 400.00);
        $addPP('DAMRI', 'Palembang - Terminal Alang-Alang Lebar', 'Bengkulu - Terminal Air Sebakul', '08:00:00', '20:00:00', 420.00);
        $addPP('DAMRI', 'Palembang - Terminal Karya Jaya', 'Bandar Lampung - Terminal Rajabasa', '07:00:00', '21:00:00', 390.00);

        // ==========================================
        // 3. PESAWAT DOMESTIK (MODEL HUB-AND-SPOKE REALISTIS)
        // ==========================================
        $KNO = 'Medan - Bandara Kualanamu (KNO)';
        $DTB = 'Danau Toba / Siborong-Borong - Bandara Silangit (DTB)';
        $CGK = 'Jakarta / Tangerang - Bandara Soekarno-Hatta (CGK)';
        $SUB = 'Surabaya - Bandara Juanda (SUB)';
        $DPS = 'Denpasar / Bali - Bandara I Gusti Ngurah Rai (DPS)';
        $UPG = 'Makassar - Bandara Sultan Hasanuddin (UPG)';
        $DJJ = 'Jayapura - Bandara Sentani (DJJ)';

        // --- A. Penerbangan Langsung ke Kualanamu (KNO) ---
        // Koridor Utama KNO <-> CGK (Multi Maskapai)
        $addPP('Garuda Indonesia', $KNO, $CGK, '06:00:00', '21:30:00', 1750.00);
        $addPP('Batik Air',        $KNO, $CGK, '06:00:00', '22:00:00', 1450.00);
        $addPP('Citilink',         $KNO, $CGK, '06:00:00', '21:30:00', 1200.00);
        $addPP('Lion Air',         $KNO, $CGK, '05:30:00', '22:30:00', 1100.00);
        $addPP('Super Air Jet',    $KNO, $CGK, '07:00:00', '21:00:00', 1150.00);

        // Koridor Utama Lain ke KNO (Sumatra & Jawa)
        $directToKno = [
            ['Batik Air',     'Jakarta Timur - Bandara Halim Perdanakusuma (HLP)', 1450.00],
            ['Super Air Jet', 'Bandung / Majalengka - Bandara Kertajati (KJT)',    1200.00],
            ['Lion Air',      'Semarang - Bandara Jenderal Ahmad Yani (SRG)',      1150.00],
            ['Lion Air',      'Yogyakarta - Bandara Internasional Yogyakarta (YIA)', 1150.00],
            ['Lion Air',      'Surabaya - Bandara Juanda (SUB)',                   1100.00],
            ['Citilink',      'Surabaya - Bandara Juanda (SUB)',                   1250.00],
            ['Super Air Jet', 'Banda Aceh - Bandara Sultan Iskandar Muda (BTJ)',   1350.00],
            ['Lion Air',      'Batam - Bandara Hang Nadim (BTH)',                  1200.00],
            ['Citilink',      'Batam - Bandara Hang Nadim (BTH)',                  1300.00],
            ['Super Air Jet', 'Padang - Bandara Minangkabau (PDG)',                1300.00],
            ['Super Air Jet', 'Pekanbaru - Bandara Sultan Syarif Kasim II (PKU)',  1300.00],
            ['Lion Air',      'Palembang - Bandara Sultan Mahmud Badaruddin II (PLM)', 1200.00],
            ['Lion Air',      'Jambi - Bandara Sultan Thaha (DJB)',                1250.00],
            ['Lion Air',      'Bandar Lampung - Bandara Radin Inten II (TKG)',     1200.00],
            ['Citilink',      $DTB,                                                1600.00],
        ];
        foreach ($directToKno as [$maskapai, $asal, $tarif]) {
            $addPP($maskapai, $asal, $KNO, '06:30:00', '20:30:00', $tarif);
        }

        // Penerbangan Langsung ke Silangit (Danau Toba)
        $addPP('Citilink',      $CGK, $DTB, '07:00:00', '16:00:00', 1250.00);
        $addPP('Super Air Jet', $CGK, $DTB, '07:00:00', '16:00:00', 1150.00);
        $addPP('Super Air Jet', 'Batam - Bandara Hang Nadim (BTH)', $DTB, '08:00:00', '15:00:00', 1300.00);

        // --- B. Backbone Antar-Hub Nasional (untuk Transit Otomatis) ---
        $addPP('Garuda Indonesia', $CGK, $SUB, '05:30:00', '22:00:00', 1800.00);
        $addPP('Citilink',         $CGK, $SUB, '06:00:00', '22:00:00', 1200.00);
        $addPP('Garuda Indonesia', $CGK, $DPS, '06:00:00', '22:00:00', 1850.00);
        $addPP('Citilink',         $CGK, $DPS, '06:00:00', '22:00:00', 1250.00);
        $addPP('Lion Air',         $CGK, $UPG, '05:30:00', '22:00:00', 1100.00);
        $addPP('Garuda Indonesia', $CGK, $UPG, '06:00:00', '21:00:00', 1750.00);
        $addPP('Lion Air',         $SUB, $DPS, '06:00:00', '21:00:00', 1350.00);
        $addPP('Lion Air',         $SUB, $UPG, '06:00:00', '21:30:00', 1150.00);
        $addPP('Batik Air',        $UPG, $DJJ, '06:00:00', '18:00:00', 1350.00);
        $addPP('Lion Air',         $UPG, $DJJ, '06:00:00', '18:00:00', 1150.00);

        // --- C. Koneksi Bandara Provinsi Lain ke Hub Terdekatnya ---
        $feederRoutes = [
            // Sumatra (yang umumnya transit via CGK)
            ['Lion Air',      'Pangkalpinang - Bandara Depati Amir (PGK)',            $CGK, 1300.00],
            ['Lion Air',      'Bengkulu - Bandara Fatmawati Soekarno (BKS)',          $CGK, 1300.00],
            // Jawa
            ['Batik Air',     'Solo / Surakarta - Bandara Adi Soemarmo (SOC)',        $CGK, 1450.00],
            // Bali & Nusa Tenggara
            ['Citilink',      'Lombok - Bandara Zainuddin Abdul Madjid (LOP)',        $CGK, 1200.00],
            ['Lion Air',      'Lombok - Bandara Zainuddin Abdul Madjid (LOP)',        $SUB, 1250.00],
            ['Batik Air',     'Labuan Bajo - Bandara Komodo (LBJ)',                   $DPS, 1450.00],
            ['Lion Air',      'Kupang - Bandara El Tari (KOE)',                       $SUB, 1150.00],
            ['Batik Air',     'Kupang - Bandara El Tari (KOE)',                       $CGK, 1350.00],
            // Kalimantan
            ['Super Air Jet', 'Pontianak - Bandara Supadio (PNK)',                    $CGK, 1200.00],
            ['Lion Air',      'Palangka Raya - Bandara Tjilik Riwut (PKY)',           $CGK, 1200.00],
            ['Lion Air',      'Palangka Raya - Bandara Tjilik Riwut (PKY)',           $SUB, 1250.00],
            ['Citilink',      'Banjarmasin - Bandara Syamsudin Noor (BDJ)',           $SUB, 1250.00],
            ['Super Air Jet', 'Banjarmasin - Bandara Syamsudin Noor (BDJ)',           $CGK, 1200.00],
            ['Batik Air',     'Balikpapan - Bandara Sepinggan (BPN)',                 $CGK, 1350.00],
            ['Lion Air',      'Balikpapan - Bandara Sepinggan (BPN)',                 $SUB, 1150.00],
            ['Lion Air',      'Tarakan - Bandara Juwata (TRK)',                       'Balikpapan - Bandara Sepinggan (BPN)', 1400.00],
            // Sulawesi
            ['Batik Air',     'Manado - Bandara Sam Ratulangi (MDC)',                 $CGK, 1350.00],
            ['Lion Air',      'Manado - Bandara Sam Ratulangi (MDC)',                 $UPG, 1250.00],
            ['Lion Air',      'Gorontalo - Bandara Djalaluddin (GTO)',                $UPG, 1300.00],
            ['Lion Air',      'Palu - Bandara Mutiara SIS Al-Jufrie (PLW)',           $UPG, 1300.00],
            ['Lion Air',      'Mamuju - Bandara Tampa Padang (MJU)',                  $UPG, 1450.00],
            ['Lion Air',      'Kendari - Bandara Haluoleo (KDI)',                     $UPG, 1350.00],
            // Maluku & Papua
            ['Lion Air',      'Ambon - Bandara Pattimura (AMQ)',                      $UPG, 1250.00],
            ['Batik Air',     'Ternate - Bandara Sultan Babullah (TTE)',              $UPG, 1350.00],
            ['Lion Air',      'Sorong - Bandara Domine Eduard Osok (SOQ)',            $UPG, 1250.00],
            ['Batik Air',     'Manokwari - Bandara Rendani (MKW)',                    $UPG, 1350.00],
            ['Batik Air',     'Timika - Bandara Mozes Kilangin (TIM)',                $UPG, 1350.00],
            ['Lion Air',      'Merauke - Bandara Mopah (MKQ)',                        $DJJ, 1450.00],
            ['Lion Air',      'Wamena - Bandara Wamena (WMX)',                        $DJJ, 1800.00],
        ];
        foreach ($feederRoutes as [$maskapai, $asal, $hub, $tarif]) {
            $addPP($maskapai, $asal, $hub, '06:00:00', '19:00:00', $tarif);
        }

        // ==========================================
        // INSERT BATCH KE DATABASE
        // ==========================================
        $data = [];
        foreach ($jadwalList as $item) {
            $idPenyedia = $penyediaMap[$item['penyedia']] ?? null;
            $idAsal     = $kotaMap[$item['kota_asal']] ?? null;
            $idTujuan   = $kotaMap[$item['kota_tujuan']] ?? null;

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