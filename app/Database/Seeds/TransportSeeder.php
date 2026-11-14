<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TransportSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $data = [
            ['jenis' => 'Bus',              'created_at' => $now, 'updated_at' => $now],
            ['jenis' => 'Kereta Api',       'created_at' => $now, 'updated_at' => $now],
            ['jenis' => 'Pesawat',          'created_at' => $now, 'updated_at' => $now],
            ['jenis' => 'Kapal Laut',       'created_at' => $now, 'updated_at' => $now],
            ['jenis' => 'Travel / Shuttle', 'created_at' => $now, 'updated_at' => $now],
        ];

        $this->db->table('transport')->insertBatch($data);
    }
}
