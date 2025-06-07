<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DokumenPendukungTabel extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'sertifikat_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'nama_file' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'tipe_dokumen' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'path_file' => [
                'type' => 'TEXT',
            ],
            'uploaded_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('sertifikat_id', 'sertifikat', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('dokumen_pendukung');
    }

    public function down()
    {
        $this->forge->dropTable('dokumen_pendukung', true);
    }
}