<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Bersihkan data lama jika ada dan reset sequence ID
        $this->db->query('TRUNCATE TABLE jadwal, penyedia, lokasi, transport RESTART IDENTITY CASCADE;');

        // Jalankan seeder berurutan sesuai relasi foreign key
        $this->call('TransportSeeder');
        $this->call('LokasiSeeder');
        $this->call('PenyediaSeeder');
        $this->call('JadwalSeeder');
    }
}
