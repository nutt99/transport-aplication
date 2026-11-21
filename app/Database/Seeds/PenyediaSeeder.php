<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PenyediaSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // Mapping jenis transport ke id
        $transports = $this->db->table('transport')->get()->getResultArray();
        $transportMap = [];
        foreach ($transports as $t) {
            $transportMap[$t['jenis']] = $t['id'];
        }

        $data = [
            // Bus
            [
                'id_transport'  => $transportMap['Bus'],
                'nama_penyedia' => 'PO Sinar Jaya',
                'latitude'      => -6.22501400,
                'longitude'     => 106.90044700,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id_transport'  => $transportMap['Bus'],
                'nama_penyedia' => 'PO Rosalia Indah',
                'latitude'      => -7.56841200,
                'longitude'     => 110.85210400,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id_transport'  => $transportMap['Bus'],
                'nama_penyedia' => 'PO Primajasa',
                'latitude'      => -6.23049100,
                'longitude'     => 106.88210300,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id_transport'  => $transportMap['Bus'],
                'nama_penyedia' => 'DAMRI',
                'latitude'      => -6.21528900,
                'longitude'     => 106.87023100,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id_transport'  => $transportMap['Bus'],
                'nama_penyedia' => 'PO Gunung Harta',
                'latitude'      => -8.65012300,
                'longitude'     => 115.21045200,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],

            // Kereta Api
            [
                'id_transport'  => $transportMap['Kereta Api'],
                'nama_penyedia' => 'PT Kereta Api Indonesia (Persero)',
                'latitude'      => -6.91474400,
                'longitude'     => 107.60981000,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id_transport'  => $transportMap['Kereta Api'],
                'nama_penyedia' => 'KCIC Whoosh',
                'latitude'      => -6.24431200,
                'longitude'     => 106.87789400,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id_transport'  => $transportMap['Kereta Api'],
                'nama_penyedia' => 'KAI Commuter',
                'latitude'      => -6.17539200,
                'longitude'     => 106.82715300,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],

            // Pesawat
            [
                'id_transport'  => $transportMap['Pesawat'],
                'nama_penyedia' => 'Garuda Indonesia',
                'latitude'      => -6.12555600,
                'longitude'     => 106.65583300,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id_transport'  => $transportMap['Pesawat'],
                'nama_penyedia' => 'Citilink',
                'latitude'      => -6.12720000,
                'longitude'     => 106.65340000,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id_transport'  => $transportMap['Pesawat'],
                'nama_penyedia' => 'Lion Air',
                'latitude'      => -6.12610000,
                'longitude'     => 106.65420000,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id_transport'  => $transportMap['Pesawat'],
                'nama_penyedia' => 'Batik Air',
                'latitude'      => -6.12650000,
                'longitude'     => 106.65480000,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id_transport'  => $transportMap['Pesawat'],
                'nama_penyedia' => 'Super Air Jet',
                'latitude'      => -6.12800000,
                'longitude'     => 106.65210000,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],

            // Kapal Laut
            [
                'id_transport'  => $transportMap['Kapal Laut'],
                'nama_penyedia' => 'PT Pelayaran Nasional Indonesia (PELNI)',
                'latitude'      => -6.10520000,
                'longitude'     => 106.88360000,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id_transport'  => $transportMap['Kapal Laut'],
                'nama_penyedia' => 'PT ASDP Indonesia Ferry',
                'latitude'      => -5.92980000,
                'longitude'     => 105.99610000,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],

            // Travel / Shuttle
            [
                'id_transport'  => $transportMap['Travel / Shuttle'],
                'nama_penyedia' => 'Baraya Travel',
                'latitude'      => -6.21150000,
                'longitude'     => 106.84380000,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id_transport'  => $transportMap['Travel / Shuttle'],
                'nama_penyedia' => 'DayTrans',
                'latitude'      => -6.22010000,
                'longitude'     => 106.82050000,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id_transport'  => $transportMap['Travel / Shuttle'],
                'nama_penyedia' => 'Cititrans',
                'latitude'      => -6.22340000,
                'longitude'     => 106.81120000,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],

            // Tambahan Bus Sumatra
            [
                'id_transport'  => $transportMap['Bus'],
                'nama_penyedia' => 'PO ALS (Antar Lintas Sumatera)',
                'latitude'      => 3.54194400,
                'longitude'     => 98.70444400,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id_transport'  => $transportMap['Bus'],
                'nama_penyedia' => 'PO Sempati Star',
                'latitude'      => 3.57527800,
                'longitude'     => 98.62583300,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id_transport'  => $transportMap['Bus'],
                'nama_penyedia' => 'PO NPM',
                'latitude'      => -0.46194400,
                'longitude'     => 100.40222200,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],

            // Tambahan Kereta Bandara Sumatra
            [
                'id_transport'  => $transportMap['Kereta Api'],
                'nama_penyedia' => 'KAI Bandara (Railink)',
                'latitude'      => 3.59138900,
                'longitude'     => 98.67861100,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
        ];

        $this->db->table('penyedia')->insertBatch($data);
    }
}
