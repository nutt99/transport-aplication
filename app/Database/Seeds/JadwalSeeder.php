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
            // Kereta Api - PT Kereta Api Indonesia (Persero)
            [
                'penyedia'    => 'PT Kereta Api Indonesia (Persero)',
                'kota_asal'   => 'Jakarta',
                'kota_tujuan' => 'Bandung',
                'start_hours' => '06:30:00',
                'ended_hours' => '09:15:00',
                'tarif_per_km'=> 750.00,
            ],
            [
                'penyedia'    => 'PT Kereta Api Indonesia (Persero)',
                'kota_asal'   => 'Bandung',
                'kota_tujuan' => 'Jakarta',
                'start_hours' => '10:00:00',
                'ended_hours' => '12:45:00',
                'tarif_per_km'=> 750.00,
            ],
            [
                'penyedia'    => 'PT Kereta Api Indonesia (Persero)',
                'kota_asal'   => 'Jakarta',
                'kota_tujuan' => 'Yogyakarta',
                'start_hours' => '08:00:00',
                'ended_hours' => '15:30:00',
                'tarif_per_km'=> 650.00,
            ],
            [
                'penyedia'    => 'PT Kereta Api Indonesia (Persero)',
                'kota_asal'   => 'Yogyakarta',
                'kota_tujuan' => 'Jakarta',
                'start_hours' => '16:30:00',
                'ended_hours' => '23:55:00',
                'tarif_per_km'=> 650.00,
            ],
            [
                'penyedia'    => 'PT Kereta Api Indonesia (Persero)',
                'kota_asal'   => 'Jakarta',
                'kota_tujuan' => 'Surabaya',
                'start_hours' => '09:00:00',
                'ended_hours' => '19:30:00',
                'tarif_per_km'=> 700.00,
            ],
            [
                'penyedia'    => 'PT Kereta Api Indonesia (Persero)',
                'kota_asal'   => 'Surabaya',
                'kota_tujuan' => 'Jakarta',
                'start_hours' => '20:00:00',
                'ended_hours' => '06:30:00',
                'tarif_per_km'=> 700.00,
            ],
            [
                'penyedia'    => 'PT Kereta Api Indonesia (Persero)',
                'kota_asal'   => 'Bandung',
                'kota_tujuan' => 'Surabaya',
                'start_hours' => '07:00:00',
                'ended_hours' => '18:30:00',
                'tarif_per_km'=> 680.00,
            ],

            // Kereta Cepat - KCIC Whoosh
            [
                'penyedia'    => 'KCIC Whoosh',
                'kota_asal'   => 'Jakarta',
                'kota_tujuan' => 'Bandung',
                'start_hours' => '07:00:00',
                'ended_hours' => '07:45:00',
                'tarif_per_km'=> 1750.00,
            ],
            [
                'penyedia'    => 'KCIC Whoosh',
                'kota_asal'   => 'Bandung',
                'kota_tujuan' => 'Jakarta',
                'start_hours' => '08:30:00',
                'ended_hours' => '09:15:00',
                'tarif_per_km'=> 1750.00,
            ],
            [
                'penyedia'    => 'KCIC Whoosh',
                'kota_asal'   => 'Jakarta',
                'kota_tujuan' => 'Bandung',
                'start_hours' => '17:00:00',
                'ended_hours' => '17:45:00',
                'tarif_per_km'=> 1750.00,
            ],

            // Bus - PO Sinar Jaya
            [
                'penyedia'    => 'PO Sinar Jaya',
                'kota_asal'   => 'Jakarta',
                'kota_tujuan' => 'Semarang',
                'start_hours' => '07:30:00',
                'ended_hours' => '14:30:00',
                'tarif_per_km'=> 350.00,
            ],
            [
                'penyedia'    => 'PO Sinar Jaya',
                'kota_asal'   => 'Semarang',
                'kota_tujuan' => 'Jakarta',
                'start_hours' => '19:00:00',
                'ended_hours' => '02:00:00',
                'tarif_per_km'=> 350.00,
            ],

            // Bus - PO Rosalia Indah
            [
                'penyedia'    => 'PO Rosalia Indah',
                'kota_asal'   => 'Jakarta',
                'kota_tujuan' => 'Surakarta',
                'start_hours' => '08:00:00',
                'ended_hours' => '16:30:00',
                'tarif_per_km'=> 400.00,
            ],
            [
                'penyedia'    => 'PO Rosalia Indah',
                'kota_asal'   => 'Surakarta',
                'kota_tujuan' => 'Jakarta',
                'start_hours' => '18:30:00',
                'ended_hours' => '03:00:00',
                'tarif_per_km'=> 400.00,
            ],

            // Bus - DAMRI
            [
                'penyedia'    => 'DAMRI',
                'kota_asal'   => 'Jakarta',
                'kota_tujuan' => 'Bandung',
                'start_hours' => '06:00:00',
                'ended_hours' => '09:30:00',
                'tarif_per_km'=> 380.00,
            ],

            // Bus - PO Gunung Harta
            [
                'penyedia'    => 'PO Gunung Harta',
                'kota_asal'   => 'Surabaya',
                'kota_tujuan' => 'Denpasar',
                'start_hours' => '16:00:00',
                'ended_hours' => '05:00:00',
                'tarif_per_km'=> 420.00,
            ],
            [
                'penyedia'    => 'PO Gunung Harta',
                'kota_asal'   => 'Denpasar',
                'kota_tujuan' => 'Malang',
                'start_hours' => '17:00:00',
                'ended_hours' => '06:30:00',
                'tarif_per_km'=> 430.00,
            ],

            // Pesawat - Garuda Indonesia
            [
                'penyedia'    => 'Garuda Indonesia',
                'kota_asal'   => 'Jakarta',
                'kota_tujuan' => 'Denpasar',
                'start_hours' => '07:00:00',
                'ended_hours' => '09:50:00',
                'tarif_per_km'=> 1850.00,
            ],
            [
                'penyedia'    => 'Garuda Indonesia',
                'kota_asal'   => 'Denpasar',
                'kota_tujuan' => 'Jakarta',
                'start_hours' => '11:00:00',
                'ended_hours' => '11:50:00',
                'tarif_per_km'=> 1850.00,
            ],
            [
                'penyedia'    => 'Garuda Indonesia',
                'kota_asal'   => 'Jakarta',
                'kota_tujuan' => 'Medan',
                'start_hours' => '08:30:00',
                'ended_hours' => '10:50:00',
                'tarif_per_km'=> 1700.00,
            ],

            // Pesawat - Citilink
            [
                'penyedia'    => 'Citilink',
                'kota_asal'   => 'Jakarta',
                'kota_tujuan' => 'Surabaya',
                'start_hours' => '06:00:00',
                'ended_hours' => '07:30:00',
                'tarif_per_km'=> 1200.00,
            ],
            [
                'penyedia'    => 'Citilink',
                'kota_asal'   => 'Surabaya',
                'kota_tujuan' => 'Jakarta',
                'start_hours' => '08:30:00',
                'ended_hours' => '10:00:00',
                'tarif_per_km'=> 1200.00,
            ],

            // Pesawat - Lion Air
            [
                'penyedia'    => 'Lion Air',
                'kota_asal'   => 'Jakarta',
                'kota_tujuan' => 'Makassar',
                'start_hours' => '09:00:00',
                'ended_hours' => '12:25:00',
                'tarif_per_km'=> 1100.00,
            ],

            // Pesawat - Super Air Jet
            [
                'penyedia'    => 'Super Air Jet',
                'kota_asal'   => 'Jakarta',
                'kota_tujuan' => 'Yogyakarta',
                'start_hours' => '10:00:00',
                'ended_hours' => '11:15:00',
                'tarif_per_km'=> 1150.00,
            ],

            // Travel / Shuttle - Baraya Travel
            [
                'penyedia'    => 'Baraya Travel',
                'kota_asal'   => 'Jakarta',
                'kota_tujuan' => 'Bandung',
                'start_hours' => '06:00:00',
                'ended_hours' => '09:00:00',
                'tarif_per_km'=> 650.00,
            ],
            [
                'penyedia'    => 'Baraya Travel',
                'kota_asal'   => 'Jakarta',
                'kota_tujuan' => 'Bandung',
                'start_hours' => '13:00:00',
                'ended_hours' => '16:00:00',
                'tarif_per_km'=> 650.00,
            ],

            // Travel / Shuttle - DayTrans
            [
                'penyedia'    => 'DayTrans',
                'kota_asal'   => 'Jakarta',
                'kota_tujuan' => 'Bandung',
                'start_hours' => '07:30:00',
                'ended_hours' => '10:30:00',
                'tarif_per_km'=> 680.00,
            ],
            [
                'penyedia'    => 'DayTrans',
                'kota_asal'   => 'Bandung',
                'kota_tujuan' => 'Jakarta',
                'start_hours' => '15:00:00',
                'ended_hours' => '18:00:00',
                'tarif_per_km'=> 680.00,
            ],

            // Travel / Shuttle - Cititrans
            [
                'penyedia'    => 'Cititrans',
                'kota_asal'   => 'Jakarta',
                'kota_tujuan' => 'Bandung',
                'start_hours' => '08:00:00',
                'ended_hours' => '11:00:00',
                'tarif_per_km'=> 750.00,
            ],

            // Kapal Laut - PT PELNI
            [
                'penyedia'    => 'PT Pelayaran Nasional Indonesia (PELNI)',
                'kota_asal'   => 'Surabaya',
                'kota_tujuan' => 'Makassar',
                'start_hours' => '10:00:00',
                'ended_hours' => '06:00:00',
                'tarif_per_km'=> 250.00,
            ],

            // Kapal Laut - ASDP
            [
                'penyedia'    => 'PT ASDP Indonesia Ferry',
                'kota_asal'   => 'Denpasar',
                'kota_tujuan' => 'Surabaya',
                'start_hours' => '08:00:00',
                'ended_hours' => '18:00:00',
                'tarif_per_km'=> 280.00,
            ],
        ];

        $data = [];
        foreach ($jadwalList as $item) {
            $data[] = [
                'id_penyedia'    => $penyediaMap[$item['penyedia']] ?? null,
                'id_kota_asal'   => $kotaMap[$item['kota_asal']] ?? null,
                'id_kota_tujuan' => $kotaMap[$item['kota_tujuan']] ?? null,
                'start_hours'    => $item['start_hours'],
                'ended_hours'    => $item['ended_hours'],
                'tarif_per_km'   => $item['tarif_per_km'],
                'created_at'     => $now,
                'updated_at'     => $now,
            ];
        }

        $this->db->table('jadwal')->insertBatch($data);
    }
}
