<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LokasiTerminalSumutSeeder extends Seeder
{
    public function run()
    {
        $terminals = [
            // 1. Kota Medan (5 Terminal — Amplas & Pinang Baris sudah ada di LokasiSeeder)
            ['nama_lokasi' => 'Medan - Terminal Amplas', 'latitude' => 3.535833, 'longitude' => 98.718333],
            ['nama_lokasi' => 'Medan - Terminal Pinang Baris', 'latitude' => 3.588056, 'longitude' => 98.609444],
            ['nama_lokasi' => 'Medan - Terminal Sambas Pusat Pasar', 'latitude' => 3.591200, 'longitude' => 98.678900],
            ['nama_lokasi' => 'Medan - Terminal Simpang Pos Padang Bulan', 'latitude' => 3.553900, 'longitude' => 98.656200],
            ['nama_lokasi' => 'Medan - Terminal Belawan', 'latitude' => 3.774500, 'longitude' => 98.683200],

            // 2. Kota Binjai
            ['nama_lokasi' => 'Binjai - Terminal Ikan Paus', 'latitude' => 3.606700, 'longitude' => 98.489200],
            ['nama_lokasi' => 'Binjai - Terminal Binjai Kota', 'latitude' => 3.597500, 'longitude' => 98.473000],
            ['nama_lokasi' => 'Binjai - Terminal Tandem', 'latitude' => 3.619000, 'longitude' => 98.508500],

            // 3. Kota Tebing Tinggi
            ['nama_lokasi' => 'Tebing Tinggi - Terminal Bandar Kajum', 'latitude' => 3.328500, 'longitude' => 99.162500],
            ['nama_lokasi' => 'Tebing Tinggi - Terminal Simpang Medan', 'latitude' => 3.321000, 'longitude' => 99.151000],
            ['nama_lokasi' => 'Tebing Tinggi - Terminal Kampung Bicara', 'latitude' => 3.337000, 'longitude' => 99.174000],

            // 4. Kota Pematangsiantar (Tanjung Pinggir sudah ada di LokasiSeeder)
            ['nama_lokasi' => 'Pematangsiantar - Terminal Tanjung Pinggir', 'latitude' => 2.987500, 'longitude' => 99.046944],
            ['nama_lokasi' => 'Pematangsiantar - Terminal Parluasan', 'latitude' => 2.959500, 'longitude' => 99.062000],
            ['nama_lokasi' => 'Pematangsiantar - Terminal Simpang Dua', 'latitude' => 2.942000, 'longitude' => 99.051000],

            // 5. Kota Tanjungbalai
            ['nama_lokasi' => 'Tanjungbalai - Terminal Batu Tujuh', 'latitude' => 2.966700, 'longitude' => 99.800000],
            ['nama_lokasi' => 'Tanjungbalai - Terminal Teluk Nibung', 'latitude' => 2.958000, 'longitude' => 99.791000],
            ['nama_lokasi' => 'Tanjungbalai - Terminal Datuk Bandar', 'latitude' => 2.974000, 'longitude' => 99.811000],

            // 6. Kota Sibolga
            ['nama_lokasi' => 'Sibolga - Terminal Kota Sibolga', 'latitude' => 1.739700, 'longitude' => 98.781900],
            ['nama_lokasi' => 'Sibolga - Terminal Simaremare', 'latitude' => 1.746500, 'longitude' => 98.776000],
            ['nama_lokasi' => 'Sibolga - Terminal Sambas', 'latitude' => 1.731000, 'longitude' => 98.790500],

            // 7. Kota Padangsidimpuan (Palopat Pijor Koling sudah ada di LokasiSeeder)
            ['nama_lokasi' => 'Padang Sidempuan - Terminal Palopat Pijor Koling', 'latitude' => 1.325833, 'longitude' => 99.313611],
            ['nama_lokasi' => 'Padang Sidempuan - Terminal Batunadua', 'latitude' => 1.378500, 'longitude' => 99.272000],
            ['nama_lokasi' => 'Padang Sidempuan - Terminal Sadabuan', 'latitude' => 1.391000, 'longitude' => 99.280000],

            // 8. Kota Gunungsitoli
            ['nama_lokasi' => 'Gunungsitoli - Terminal Kota Gunungsitoli', 'latitude' => 1.285600, 'longitude' => 97.615800],
            ['nama_lokasi' => 'Gunungsitoli - Terminal Olora', 'latitude' => 1.269000, 'longitude' => 97.624000],
            ['nama_lokasi' => 'Gunungsitoli - Terminal Fodo', 'latitude' => 1.251000, 'longitude' => 97.632000],

            // 9. Kabupaten Deli Serdang
            ['nama_lokasi' => 'Deli Serdang - Terminal Lubuk Pakam', 'latitude' => 3.545000, 'longitude' => 98.878000],
            ['nama_lokasi' => 'Deli Serdang - Terminal Tanjung Morawa', 'latitude' => 3.551000, 'longitude' => 98.782000],
            ['nama_lokasi' => 'Deli Serdang - Terminal Pancur Batu', 'latitude' => 3.447500, 'longitude' => 98.562000],

            // 10. Kabupaten Langkat
            ['nama_lokasi' => 'Langkat - Terminal Stabat', 'latitude' => 3.751000, 'longitude' => 98.455000],
            ['nama_lokasi' => 'Langkat - Terminal Tanjung Pura', 'latitude' => 3.896000, 'longitude' => 98.414000],
            ['nama_lokasi' => 'Langkat - Terminal Pangkalan Brandan', 'latitude' => 4.116000, 'longitude' => 98.215000],

            // 11. Kabupaten Karo
            ['nama_lokasi' => 'Karo - Terminal Berastagi', 'latitude' => 3.186900, 'longitude' => 98.508900],
            ['nama_lokasi' => 'Karo - Terminal Kabanjahe', 'latitude' => 3.100300, 'longitude' => 98.491400],
            ['nama_lokasi' => 'Karo - Terminal Tigabinanga', 'latitude' => 3.081000, 'longitude' => 98.372000],

            // 12. Kabupaten Simalungun
            ['nama_lokasi' => 'Simalungun - Terminal Sosor Saba Parapat', 'latitude' => 2.658900, 'longitude' => 98.938100],
            ['nama_lokasi' => 'Simalungun - Terminal Perdagangan', 'latitude' => 2.968000, 'longitude' => 99.191000],
            ['nama_lokasi' => 'Simalungun - Terminal Pematang Raya', 'latitude' => 2.971000, 'longitude' => 98.855000],

            // 13. Kabupaten Asahan
            ['nama_lokasi' => 'Asahan - Terminal Madya Kisaran', 'latitude' => 2.982500, 'longitude' => 99.625000],
            ['nama_lokasi' => 'Asahan - Terminal Pulau Raja', 'latitude' => 2.781000, 'longitude' => 99.538000],
            ['nama_lokasi' => 'Asahan - Terminal Simpang Kawat', 'latitude' => 3.058000, 'longitude' => 99.485000],

            // 14. Kabupaten Batu Bara
            ['nama_lokasi' => 'Batu Bara - Terminal Lima Puluh', 'latitude' => 3.173000, 'longitude' => 99.418000],
            ['nama_lokasi' => 'Batu Bara - Terminal Indrapura', 'latitude' => 3.298000, 'longitude' => 99.324000],
            ['nama_lokasi' => 'Batu Bara - Terminal Tanjung Tiram', 'latitude' => 3.225000, 'longitude' => 99.558000],

            // 15. Kabupaten Labuhanbatu
            ['nama_lokasi' => 'Labuhanbatu - Terminal Padang Bulan Rantauprapat', 'latitude' => 2.094000, 'longitude' => 99.826000],
            ['nama_lokasi' => 'Labuhanbatu - Terminal Aek Nabara', 'latitude' => 2.012000, 'longitude' => 99.945000],
            ['nama_lokasi' => 'Labuhanbatu - Terminal Negeri Lama', 'latitude' => 2.285000, 'longitude' => 100.068000],

            // 16. Kabupaten Labuhanbatu Utara
            ['nama_lokasi' => 'Labuhanbatu Utara - Terminal Aek Kanopan', 'latitude' => 2.567000, 'longitude' => 99.634000],
            ['nama_lokasi' => 'Labuhanbatu Utara - Terminal Marbau', 'latitude' => 2.371000, 'longitude' => 99.698000],
            ['nama_lokasi' => 'Labuhanbatu Utara - Terminal Gunting Saga', 'latitude' => 2.681000, 'longitude' => 99.589000],

            // 17. Kabupaten Labuhanbatu Selatan
            ['nama_lokasi' => 'Labuhanbatu Selatan - Terminal Kotapinang', 'latitude' => 1.895000, 'longitude' => 100.095000],
            ['nama_lokasi' => 'Labuhanbatu Selatan - Terminal Cikampak', 'latitude' => 1.745000, 'longitude' => 100.245000],
            ['nama_lokasi' => 'Labuhanbatu Selatan - Terminal Langga Payung', 'latitude' => 1.968000, 'longitude' => 99.985000],

            // 18. Kabupaten Dairi
            ['nama_lokasi' => 'Dairi - Terminal Sidikalang', 'latitude' => 2.744000, 'longitude' => 98.316000],
            ['nama_lokasi' => 'Dairi - Terminal Sumbul', 'latitude' => 2.851000, 'longitude' => 98.512000],
            ['nama_lokasi' => 'Dairi - Terminal Tigalingga', 'latitude' => 2.825000, 'longitude' => 98.248000],

            // 19. Kabupaten Pakpak Bharat
            ['nama_lokasi' => 'Pakpak Bharat - Terminal Salak', 'latitude' => 2.563000, 'longitude' => 98.324000],
            ['nama_lokasi' => 'Pakpak Bharat - Terminal Sukaramai', 'latitude' => 2.598000, 'longitude' => 98.265000],
            ['nama_lokasi' => 'Pakpak Bharat - Terminal Siempat Rube', 'latitude' => 2.512000, 'longitude' => 98.389000],

            // 20. Kabupaten Toba
            ['nama_lokasi' => 'Toba - Terminal Balige', 'latitude' => 2.333600, 'longitude' => 99.063100],
            ['nama_lokasi' => 'Toba - Terminal Porsea', 'latitude' => 2.435000, 'longitude' => 99.146000],
            ['nama_lokasi' => 'Toba - Terminal Laguboti', 'latitude' => 2.385000, 'longitude' => 99.118000],

            // 21. Kabupaten Samosir
            ['nama_lokasi' => 'Samosir - Terminal Pangururan', 'latitude' => 2.607500, 'longitude' => 98.694200],
            ['nama_lokasi' => 'Samosir - Terminal Tomok Simanindo', 'latitude' => 2.652000, 'longitude' => 98.861000],
            ['nama_lokasi' => 'Samosir - Terminal Onan Runggu', 'latitude' => 2.458000, 'longitude' => 98.812000],

            // 22. Kabupaten Humbang Hasundutan
            ['nama_lokasi' => 'Humbang Hasundutan - Terminal Doloksanggul', 'latitude' => 2.257200, 'longitude' => 98.748600],
            ['nama_lokasi' => 'Humbang Hasundutan - Terminal Lintong Nihuta', 'latitude' => 2.238000, 'longitude' => 98.892000],
            ['nama_lokasi' => 'Humbang Hasundutan - Terminal Parlilitan', 'latitude' => 2.145000, 'longitude' => 98.618000],

            // 23. Kabupaten Tapanuli Utara
            ['nama_lokasi' => 'Tapanuli Utara - Terminal Madya Tarutung', 'latitude' => 2.016000, 'longitude' => 98.967000],
            ['nama_lokasi' => 'Tapanuli Utara - Terminal Siborongborong', 'latitude' => 2.195000, 'longitude' => 98.972000],
            ['nama_lokasi' => 'Tapanuli Utara - Terminal Sarulla', 'latitude' => 1.875000, 'longitude' => 99.082000],

            // 24. Kabupaten Tapanuli Tengah
            ['nama_lokasi' => 'Tapanuli Tengah - Terminal Pandan', 'latitude' => 1.685000, 'longitude' => 98.824000],
            ['nama_lokasi' => 'Tapanuli Tengah - Terminal Pinangsori', 'latitude' => 1.792000, 'longitude' => 98.775000],
            ['nama_lokasi' => 'Tapanuli Tengah - Terminal Barus', 'latitude' => 2.012000, 'longitude' => 98.405000],

            // 25. Kabupaten Tapanuli Selatan
            ['nama_lokasi' => 'Tapanuli Selatan - Terminal Sipirok', 'latitude' => 1.578000, 'longitude' => 99.268000],
            ['nama_lokasi' => 'Tapanuli Selatan - Terminal Batang Toru', 'latitude' => 1.482000, 'longitude' => 99.065000],
            ['nama_lokasi' => 'Tapanuli Selatan - Terminal Sayur Matinggi', 'latitude' => 1.215000, 'longitude' => 99.432000],

            // 26. Kabupaten Padang Lawas
            ['nama_lokasi' => 'Padang Lawas - Terminal Sibuhuan', 'latitude' => 1.068000, 'longitude' => 99.742000],
            ['nama_lokasi' => 'Padang Lawas - Terminal Sosa', 'latitude' => 1.185000, 'longitude' => 99.645000],
            ['nama_lokasi' => 'Padang Lawas - Terminal Binanga', 'latitude' => 1.298000, 'longitude' => 99.812000],

            // 27. Kabupaten Padang Lawas Utara
            ['nama_lokasi' => 'Padang Lawas Utara - Terminal Gunung Tua', 'latitude' => 1.512000, 'longitude' => 99.625000],
            ['nama_lokasi' => 'Padang Lawas Utara - Terminal Portibi', 'latitude' => 1.648000, 'longitude' => 99.742000],
            ['nama_lokasi' => 'Padang Lawas Utara - Terminal Halongonan', 'latitude' => 1.412000, 'longitude' => 99.485000],

            // 28. Kabupaten Mandailing Natal
            ['nama_lokasi' => 'Mandailing Natal - Terminal Panyabungan', 'latitude' => 0.856000, 'longitude' => 99.558000],
            ['nama_lokasi' => 'Mandailing Natal - Terminal Kotanopan', 'latitude' => 0.648000, 'longitude' => 99.712000],
            ['nama_lokasi' => 'Mandailing Natal - Terminal Natal', 'latitude' => 0.558000, 'longitude' => 99.132000],

            // 29. Kabupaten Serdang Bedagai
            ['nama_lokasi' => 'Serdang Bedagai - Terminal Sei Rampah', 'latitude' => 3.486000, 'longitude' => 99.118000],
            ['nama_lokasi' => 'Serdang Bedagai - Terminal Perbaungan', 'latitude' => 3.562000, 'longitude' => 98.958000],
            ['nama_lokasi' => 'Serdang Bedagai - Terminal Dolok Masihul', 'latitude' => 3.245000, 'longitude' => 99.035000],

            // 30. Kabupaten Nias
            ['nama_lokasi' => 'Nias - Terminal Gido', 'latitude' => 1.125000, 'longitude' => 97.608000],
            ['nama_lokasi' => 'Nias - Terminal Bawolato', 'latitude' => 1.062000, 'longitude' => 97.542000],
            ['nama_lokasi' => 'Nias - Terminal Hiliduho', 'latitude' => 1.185000, 'longitude' => 97.685000],

            // 31. Kabupaten Nias Selatan
            ['nama_lokasi' => 'Nias Selatan - Terminal Teluk Dalam', 'latitude' => 0.562000, 'longitude' => 97.818000],
            ['nama_lokasi' => 'Nias Selatan - Terminal Lahusa', 'latitude' => 0.685000, 'longitude' => 97.742000],
            ['nama_lokasi' => 'Nias Selatan - Terminal Gomo', 'latitude' => 0.785000, 'longitude' => 97.665000],

            // 32. Kabupaten Nias Utara
            ['nama_lokasi' => 'Nias Utara - Terminal Lotu', 'latitude' => 1.412000, 'longitude' => 97.328000],
            ['nama_lokasi' => 'Nias Utara - Terminal Lahewa', 'latitude' => 1.485000, 'longitude' => 97.265000],
            ['nama_lokasi' => 'Nias Utara - Terminal Tuhemberua', 'latitude' => 1.352000, 'longitude' => 97.412000],

            // 33. Kabupaten Nias Barat
            ['nama_lokasi' => 'Nias Barat - Terminal Lahomi', 'latitude' => 1.068000, 'longitude' => 97.432000],
            ['nama_lokasi' => 'Nias Barat - Terminal Sirombu', 'latitude' => 1.142000, 'longitude' => 97.385000],
            ['nama_lokasi' => 'Nias Barat - Terminal Mandrehe', 'latitude' => 1.012000, 'longitude' => 97.495000],
        ];

        $existingRows = $this->db->table('lokasi')->select('nama_lokasi')->get()->getResultArray();
        $existingMap  = array_flip(array_column($existingRows, 'nama_lokasi'));

        $toInsert = [];
        foreach ($terminals as $row) {
            if (! isset($existingMap[$row['nama_lokasi']])) {
                $toInsert[] = [
                    'nama_lokasi' => $row['nama_lokasi'],
                    'latitude'    => $row['latitude'],
                    'longitude'   => $row['longitude'],
                ];
            }
        }

        if (! empty($toInsert)) {
            $this->db->table('lokasi')->insertBatch($toInsert);
        }

        echo 'Berhasil menambahkan ' . count($toInsert) . " terminal Sumut baru.\n";
    }
}