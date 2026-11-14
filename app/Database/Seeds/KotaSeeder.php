<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KotaSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $data = [
            [
                'nama_kota'  => 'Jakarta',
                'latitude'   => -6.20876340,
                'longitude'  => 106.84559900,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_kota'  => 'Bandung',
                'latitude'   => -6.91746390,
                'longitude'  => 107.61912280,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_kota'  => 'Semarang',
                'latitude'   => -6.96666700,
                'longitude'  => 110.41666400,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_kota'  => 'Yogyakarta',
                'latitude'   => -7.79557980,
                'longitude'  => 110.36948960,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_kota'  => 'Surakarta',
                'latitude'   => -7.57548870,
                'longitude'  => 110.82432630,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_kota'  => 'Surabaya',
                'latitude'   => -7.25747190,
                'longitude'  => 112.75208830,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_kota'  => 'Malang',
                'latitude'   => -7.96662040,
                'longitude'  => 112.63263210,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_kota'  => 'Denpasar',
                'latitude'   => -8.67045820,
                'longitude'  => 115.21262930,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_kota'  => 'Medan',
                'latitude'   => 3.59519560,
                'longitude'  => 98.67222270,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_kota'  => 'Makassar',
                'latitude'   => -5.14766510,
                'longitude'  => 119.43273140,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        $this->db->table('kota')->insertBatch($data);
    }
}
