<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTipeLokasiToLokasi extends Migration
{
    public function up()
    {
        $fields = [
            'tipe_lokasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'terminal',
                'after'      => 'nama_lokasi',
            ],
        ];

        // Cek apakah kolom sudah ada
        if (!$this->db->fieldExists('tipe_lokasi', 'lokasi')) {
            $this->forge->addColumn('lokasi', $fields);

            // Perbarui data lama berdasarkan nama_lokasi
            $this->db->query("
                UPDATE lokasi 
                SET tipe_lokasi = CASE 
                    WHEN LOWER(nama_lokasi) LIKE '%bandara%' THEN 'bandara'
                    WHEN LOWER(nama_lokasi) LIKE '%stasiun%' THEN 'stasiun'
                    WHEN LOWER(nama_lokasi) LIKE '%terminal%' THEN 'terminal'
                    WHEN LOWER(nama_lokasi) LIKE '%halte%' THEN 'halte'
                    WHEN LOWER(nama_lokasi) LIKE '%pelabuhan%' OR LOWER(nama_lokasi) LIKE '%dermaga%' THEN 'pelabuhan'
                    ELSE 'terminal'
                END
                WHERE tipe_lokasi IS NULL OR tipe_lokasi = ''
            ");
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('tipe_lokasi', 'lokasi')) {
            $this->forge->dropColumn('lokasi', 'tipe_lokasi');
        }
    }
}
