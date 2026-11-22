<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Lokasi extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'nama_lokasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'latitude' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,8',
            ],
            'longitude' => [
                'type'       => 'DECIMAL',
                'constraint' => '11,8',
            ],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('lokasi');
    }

    public function down()
    {
        $this->forge->dropTable('lokasi');
    }
}
