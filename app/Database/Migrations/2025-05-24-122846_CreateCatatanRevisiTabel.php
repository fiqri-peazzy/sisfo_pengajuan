<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCatatanRevisiTabel extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'pemeriksaan_id' => [
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
            'file_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'dibuat_oleh' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            'tanggal_catatan' => [
                'type'    => 'TIMESTAMP',
                'null'    => false,
                'default' => 'CURRENT_TIMESTAMP',
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
        $this->forge->addForeignKey('pemeriksaan_id', 'pemeriksaan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('dokumen_pendukung_id', 'dokumen_pendukung', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('dibuat_oleh', 'users', 'id', 'RESTRICT', 'CASCADE');

        $this->forge->createTable('catatan_revisi', true, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_general_ci']);
    }

    public function down()
    {
        $this->forge->dropTable('catatan_revisi', true);
    }
}