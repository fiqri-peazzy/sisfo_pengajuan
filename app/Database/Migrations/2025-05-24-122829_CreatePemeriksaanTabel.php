<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePemeriksaanTabel extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'sertifikat_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            'dokumen_pendukung_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'default'  => null,
            ],
            'pemeriksa_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            'status_pemeriksaan' => [
                'type'       => 'ENUM',
                'constraint' => ['disetujui', 'ditolak', 'revisi'],
                'default'    => 'revisi',
                'null'       => false,
            ],
            'tanggal_pemeriksaan' => [
                'type'    => 'DATETIME',
                'null'    => false,
                'default' => 'CURRENT_TIMESTAMP',
            ],
            'catatan_umum' => [
                'type' => 'TEXT',
                'null' => true,
                'default' => null,
            ],
            'created_at' => [
                'type'    => 'TIMESTAMP',
                'default' => 'CURRENT_TIMESTAMP',
            ],
            'updated_at' => [
                'type'    => 'TIMESTAMP',
                'default' => 'CURRENT_TIMESTAMP',
                'on_update' => 'CURRENT_TIMESTAMP',
            ],
        ]);

        $this->forge->addKey('id', true);

        // Foreign keys
        $this->forge->addForeignKey('sertifikat_id', 'sertifikat', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('dokumen_pendukung_id', 'dokumen_pendukung', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('pemeriksa_id', 'users', 'id', 'RESTRICT', 'CASCADE');

        $this->forge->createTable('pemeriksaan', true, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_general_ci']);
    }

    public function down()
    {
        $this->forge->dropTable('pemeriksaan', true);
    }
}