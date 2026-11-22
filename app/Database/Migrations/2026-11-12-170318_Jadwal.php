<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Jadwal extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'id_penyedia' => [
                'type' => 'INT',
            ],
            'id_lokasi_asal' => [
                'type' => 'INT',
                'null' => true,
            ],
            'id_lokasi_tujuan' => [
                'type' => 'INT',
                'null' => true,
            ],
            'start_hours' => [
                'type' => 'TIME',
            ],
            'ended_hours' => [
                'type' => 'TIME',
            ],
            'tarif_per_km' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_penyedia', 'penyedia', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_lokasi_asal', 'lokasi', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('id_lokasi_tujuan', 'lokasi', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('jadwal');
    }

    public function down()
    {
        $this->forge->dropTable('jadwal');
    }
}
