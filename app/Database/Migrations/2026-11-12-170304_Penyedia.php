<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Penyedia extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'id_transport' => [
                'type' => 'INT',
            ],
            'nama_penyedia' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'latitude' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,8',
                'null'       => true,
            ],
            'longitude' => [
                'type'       => 'DECIMAL',
                'constraint' => '11,8',
                'null'       => true,
            ],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_transport', 'transport', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('penyedia');
    }

    public function down()
    {
        $this->forge->dropTable('penyedia');
    }
}
