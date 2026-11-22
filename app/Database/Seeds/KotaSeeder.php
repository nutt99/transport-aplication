<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KotaSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // ==========================================
            // BANDARA UTAMA INDONESIA (41)
            // (38 Bandara Utama per Provinsi + 3 Bandara Hub/Wisata)
            // ==========================================

            // --- Pulau Sumatra ---
            [
                'nama_kota' => 'Banda Aceh - Bandara Sultan Iskandar Muda (BTJ)',
                'latitude'  => 5.523522,
                'longitude' => 95.420372,
            ],
            [
                'nama_kota' => 'Medan - Bandara Kualanamu (KNO)',
                'latitude'  => 3.642222,
                'longitude' => 98.885278,
            ],
            [
                'nama_kota' => 'Danau Toba / Siborong-Borong - Bandara Silangit (DTB)',
                'latitude'  => 2.259889,
                'longitude' => 98.995222,
            ],
            [
                'nama_kota' => 'Padang - Bandara Minangkabau (PDG)',
                'latitude'  => -0.786861,
                'longitude' => 100.280556,
            ],
            [
                'nama_kota' => 'Pekanbaru - Bandara Sultan Syarif Kasim II (PKU)',
                'latitude'  => 0.460786,
                'longitude' => 101.444539,
            ],
            [
                'nama_kota' => 'Batam - Bandara Hang Nadim (BTH)',
                'latitude'  => 1.121033,
                'longitude' => 104.118769,
            ],
            [
                'nama_kota' => 'Jambi - Bandara Sultan Thaha (DJB)',
                'latitude'  => -1.638017,
                'longitude' => 103.644383,
            ],
            [
                'nama_kota' => 'Palembang - Bandara Sultan Mahmud Badaruddin II (PLM)',
                'latitude'  => -2.898250,
                'longitude' => 104.699903,
            ],
            [
                'nama_kota' => 'Pangkalpinang - Bandara Depati Amir (PGK)',
                'latitude'  => -2.162200,
                'longitude' => 106.139200,
            ],
            [
                'nama_kota' => 'Bengkulu - Bandara Fatmawati Soekarno (BKS)',
                'latitude'  => -3.863700,
                'longitude' => 102.339000,
            ],
            [
                'nama_kota' => 'Bandar Lampung - Bandara Radin Inten II (TKG)',
                'latitude'  => -5.242339,
                'longitude' => 105.178939,
            ],

            // --- Pulau Jawa ---
            [
                'nama_kota' => 'Jakarta / Tangerang - Bandara Soekarno-Hatta (CGK)',
                'latitude'  => -6.125567,
                'longitude' => 106.655897,
            ],
            [
                'nama_kota' => 'Jakarta Timur - Bandara Halim Perdanakusuma (HLP)',
                'latitude'  => -6.266608,
                'longitude' => 106.891000,
            ],
            [
                'nama_kota' => 'Bandung / Majalengka - Bandara Kertajati (KJT)',
                'latitude'  => -6.654250,
                'longitude' => 108.157278,
            ],
            [
                'nama_kota' => 'Semarang - Bandara Jenderal Ahmad Yani (SRG)',
                'latitude'  => -6.971447,
                'longitude' => 110.374122,
            ],
            [
                'nama_kota' => 'Yogyakarta - Bandara Internasional Yogyakarta (YIA)',
                'latitude'  => -7.905833,
                'longitude' => 110.056667,
            ],
            [
                'nama_kota' => 'Solo / Surakarta - Bandara Adi Soemarmo (SOC)',
                'latitude'  => -7.516089,
                'longitude' => 110.756892,
            ],
            [
                'nama_kota' => 'Surabaya - Bandara Juanda (SUB)',
                'latitude'  => -7.379831,
                'longitude' => 112.786858,
            ],

            // --- Bali & Nusa Tenggara ---
            [
                'nama_kota' => 'Denpasar / Bali - Bandara I Gusti Ngurah Rai (DPS)',
                'latitude'  => -8.748169,
                'longitude' => 115.167172,
            ],
            [
                'nama_kota' => 'Lombok - Bandara Zainuddin Abdul Madjid (LOP)',
                'latitude'  => -8.757322,
                'longitude' => 116.276675,
            ],
            [
                'nama_kota' => 'Labuan Bajo - Bandara Komodo (LBJ)',
                'latitude'  => -8.486667,
                'longitude' => 119.889167,
            ],
            [
                'nama_kota' => 'Kupang - Bandara El Tari (KOE)',
                'latitude'  => -10.171597,
                'longitude' => 123.671211,
            ],

            // --- Pulau Kalimantan ---
            [
                'nama_kota' => 'Pontianak - Bandara Supadio (PNK)',
                'latitude'  => -0.150711,
                'longitude' => 109.403892,
            ],
            [
                'nama_kota' => 'Palangka Raya - Bandara Tjilik Riwut (PKY)',
                'latitude'  => -2.225100,
                'longitude' => 113.942700,
            ],
            [
                'nama_kota' => 'Banjarmasin - Bandara Syamsudin Noor (BDJ)',
                'latitude'  => -3.442356,
                'longitude' => 114.762553,
            ],
            [
                'nama_kota' => 'Balikpapan - Bandara Sepinggan (BPN)',
                'latitude'  => -1.268272,
                'longitude' => 116.894478,
            ],
            [
                'nama_kota' => 'Tarakan - Bandara Juwata (TRK)',
                'latitude'  => 3.326700,
                'longitude' => 117.565600,
            ],

            // --- Pulau Sulawesi ---
            [
                'nama_kota' => 'Manado - Bandara Sam Ratulangi (MDC)',
                'latitude'  => 1.549253,
                'longitude' => 124.925886,
            ],
            [
                'nama_kota' => 'Gorontalo - Bandara Djalaluddin (GTO)',
                'latitude'  => 0.637100,
                'longitude' => 122.849900,
            ],
            [
                'nama_kota' => 'Palu - Bandara Mutiara SIS Al-Jufrie (PLW)',
                'latitude'  => -0.918500,
                'longitude' => 119.909600,
            ],
            [
                'nama_kota' => 'Mamuju - Bandara Tampa Padang (MJU)',
                'latitude'  => -2.586900,
                'longitude' => 119.029400,
            ],
            [
                'nama_kota' => 'Makassar - Bandara Sultan Hasanuddin (UPG)',
                'latitude'  => -5.061631,
                'longitude' => 119.554042,
            ],
            [
                'nama_kota' => 'Kendari - Bandara Haluoleo (KDI)',
                'latitude'  => -4.081600,
                'longitude' => 122.418200,
            ],

            // --- Kepulauan Maluku & Papua ---
            [
                'nama_kota' => 'Ambon - Bandara Pattimura (AMQ)',
                'latitude'  => -3.710264,
                'longitude' => 128.089136,
            ],
            [
                'nama_kota' => 'Ternate - Bandara Sultan Babullah (TTE)',
                'latitude'  => 0.831400,
                'longitude' => 127.381000,
            ],
            [
                'nama_kota' => 'Manokwari - Bandara Rendani (MKW)',
                'latitude'  => -0.891800,
                'longitude' => 134.049200,
            ],
            [
                'nama_kota' => 'Sorong - Bandara Domine Eduard Osok (SOQ)',
                'latitude'  => -0.890600,
                'longitude' => 131.287500,
            ],
            [
                'nama_kota' => 'Jayapura - Bandara Sentani (DJJ)',
                'latitude'  => -2.576953,
                'longitude' => 140.516372,
            ],
            [
                'nama_kota' => 'Timika - Bandara Mozes Kilangin (TIM)',
                'latitude'  => -4.528300,
                'longitude' => 136.887200,
            ],
            [
                'nama_kota' => 'Wamena - Bandara Wamena (WMX)',
                'latitude'  => -4.102500,
                'longitude' => 138.957400,
            ],
            [
                'nama_kota' => 'Merauke - Bandara Mopah (MKQ)',
                'latitude'  => -8.520300,
                'longitude' => 140.418500,
            ],

            // ==========================================
            // STASIUN KERETA API SUMATERA UTARA (28)
            // ==========================================
            [
                'nama_kota' => 'Medan - Stasiun Medan (MDN)',
                'latitude'  => 3.591400,
                'longitude' => 98.678400,
            ],
            [
                'nama_kota' => 'Binjai - Stasiun Binjai (BIJ)',
                'latitude'  => 3.608500,
                'longitude' => 98.487600,
            ],
            [
                'nama_kota' => 'Langkat - Stasiun Kuala Bingai (KBG)',
                'latitude'  => 3.743600,
                'longitude' => 98.452500,
            ],
            [
                'nama_kota' => 'Deli Serdang - Stasiun Bandar Khalipah (BKP)',
                'latitude'  => 3.601900,
                'longitude' => 98.749700,
            ],
            [
                'nama_kota' => 'Deli Serdang - Stasiun Batang Kuis (BTK)',
                'latitude'  => 3.611400,
                'longitude' => 98.795800,
            ],
            [
                'nama_kota' => 'Deli Serdang - Stasiun Araskabu (ARB)',
                'latitude'  => 3.609400,
                'longitude' => 98.841400,
            ],
            [
                'nama_kota' => 'Deli Serdang - Stasiun Kualanamu (KNM)',
                'latitude'  => 3.635800,
                'longitude' => 98.878100,
            ],
            [
                'nama_kota' => 'Deli Serdang - Stasiun Lubuk Pakam (LBP)',
                'latitude'  => 3.553900,
                'longitude' => 98.872200,
            ],
            [
                'nama_kota' => 'Serdang Bedagai - Stasiun Perbaungan (PBA)',
                'latitude'  => 3.568600,
                'longitude' => 98.958100,
            ],
            [
                'nama_kota' => 'Serdang Bedagai - Stasiun Lidah Tanah (LDT)',
                'latitude'  => 3.536700,
                'longitude' => 99.004400,
            ],
            [
                'nama_kota' => 'Serdang Bedagai - Stasiun Teluk Mengkudu (TKD)',
                'latitude'  => 3.504200,
                'longitude' => 99.051700,
            ],
            [
                'nama_kota' => 'Serdang Bedagai - Stasiun Rampah (RPH)',
                'latitude'  => 3.475000,
                'longitude' => 99.115800,
            ],
            [
                'nama_kota' => 'Serdang Bedagai - Stasiun Bamban (BMB)',
                'latitude'  => 3.404400,
                'longitude' => 99.163900,
            ],
            [
                'nama_kota' => 'Tebing Tinggi - Stasiun Tebing Tinggi (TBI)',
                'latitude'  => 3.331100,
                'longitude' => 99.161400,
            ],
            [
                'nama_kota' => 'Simalungun - Stasiun Bajalinggei (BJL)',
                'latitude'  => 3.175300,
                'longitude' => 99.146400,
            ],
            [
                'nama_kota' => 'Simalungun - Stasiun Dolok Merangir (DMR)',
                'latitude'  => 3.112800,
                'longitude' => 99.119700,
            ],
            [
                'nama_kota' => 'Pematangsiantar - Stasiun Siantar (SIR)',
                'latitude'  => 2.961700,
                'longitude' => 99.064400,
            ],
            [
                'nama_kota' => 'Simalungun - Stasiun Perlanaan (PRA)',
                'latitude'  => 3.175600,
                'longitude' => 99.365800,
            ],
            [
                'nama_kota' => 'Batu Bara - Stasiun Lima Puluh (LMP)',
                'latitude'  => 3.156400,
                'longitude' => 99.423900,
            ],
            [
                'nama_kota' => 'Batu Bara - Stasiun Sei Bejangkar (SBJ)',
                'latitude'  => 3.095800,
                'longitude' => 99.497500,
            ],
            [
                'nama_kota' => 'Asahan - Stasiun Kisaran (KIS)',
                'latitude'  => 2.983900,
                'longitude' => 99.622500,
            ],
            [
                'nama_kota' => 'Tanjungbalai - Stasiun Tanjungbalai (TNB)',
                'latitude'  => 2.964700,
                'longitude' => 99.803100,
            ],
            [
                'nama_kota' => 'Asahan - Stasiun Hengelo (HL)',
                'latitude'  => 2.863900,
                'longitude' => 99.605300,
            ],
            [
                'nama_kota' => 'Asahan - Stasiun Pulau Raja (PUR)',
                'latitude'  => 2.710300,
                'longitude' => 99.619400,
            ],
            [
                'nama_kota' => 'Labuhanbatu Utara - Stasiun Mambang Muda (MBM)',
                'latitude'  => 2.559400,
                'longitude' => 99.644200,
            ],
            [
                'nama_kota' => 'Labuhanbatu Utara - Stasiun Padang Halaban (PHA)',
                'latitude'  => 2.388300,
                'longitude' => 99.728900,
            ],
            [
                'nama_kota' => 'Labuhanbatu Utara - Stasiun Marbau (MBU)',
                'latitude'  => 2.266900,
                'longitude' => 99.791700,
            ],
            [
                'nama_kota' => 'Labuhanbatu - Stasiun Rantau Prapat (RAP)',
                'latitude'  => 2.100800,
                'longitude' => 99.832200,
            ],

            // ==========================================
            // TERMINAL BUS UTAMA SUMATRA (15)
            // ==========================================
            [
                'nama_kota' => 'Banda Aceh - Terminal Batoh',
                'latitude'  => 5.529722,
                'longitude' => 95.333889,
            ],
            [
                'nama_kota' => 'Lhokseumawe - Terminal Lhokseumawe',
                'latitude'  => 5.182500,
                'longitude' => 97.143611,
            ],
            [
                'nama_kota' => 'Medan - Terminal Amplas',
                'latitude'  => 3.535833,
                'longitude' => 98.718333,
            ],
            [
                'nama_kota' => 'Medan - Terminal Pinang Baris',
                'latitude'  => 3.588056,
                'longitude' => 98.609444,
            ],
            [
                'nama_kota' => 'Pematangsiantar - Terminal Tanjung Pinggir',
                'latitude'  => 2.987500,
                'longitude' => 99.046944,
            ],
            [
                'nama_kota' => 'Padang Sidempuan - Terminal Palopat Pijor Koling',
                'latitude'  => 1.325833,
                'longitude' => 99.313611,
            ],
            [
                'nama_kota' => 'Padang - Terminal Anak Air',
                'latitude'  => -0.840833,
                'longitude' => 100.343611,
            ],
            [
                'nama_kota' => 'Bukittinggi - Terminal Simpang Aur',
                'latitude'  => -0.312778,
                'longitude' => 100.379722,
            ],
            [
                'nama_kota' => 'Pekanbaru - Terminal Bandar Raya Payung Sekaki',
                'latitude'  => 0.491944,
                'longitude' => 101.402500,
            ],
            [
                'nama_kota' => 'Dumai - Terminal Dumai',
                'latitude'  => 1.663611,
                'longitude' => 101.448056,
            ],
            [
                'nama_kota' => 'Jambi - Terminal Alam Barajo',
                'latitude'  => -1.640278,
                'longitude' => 103.575833,
            ],
            [
                'nama_kota' => 'Bengkulu - Terminal Air Sebakul',
                'latitude'  => -3.829444,
                'longitude' => 102.333611,
            ],
            [
                'nama_kota' => 'Palembang - Terminal Alang-Alang Lebar',
                'latitude'  => -2.919167,
                'longitude' => 104.691389,
            ],
            [
                'nama_kota' => 'Palembang - Terminal Karya Jaya',
                'latitude'  => -3.058056,
                'longitude' => 104.728611,
            ],
            [
                'nama_kota' => 'Bandar Lampung - Terminal Rajabasa',
                'latitude'  => -5.365278,
                'longitude' => 105.235833,
            ],
        ];

        $this->db->table('kota')->insertBatch($data);
    }
}