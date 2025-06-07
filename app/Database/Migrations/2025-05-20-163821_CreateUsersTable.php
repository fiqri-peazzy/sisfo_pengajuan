<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
                'unsigned' => true
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '255'
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => '55'
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => '255'
            ],
            'password' => [
                'type' => 'VARCHAR',

                'constraint' => '255'
            ],
            'role' => [
                'type' => 'ENUM',
                'constraint' => ['kontraktor', 'konsultan', 'ppk'],
                'default' => 'user'
            ],
            'picture' => [
                'type' => 'VARCHAR',
                'null' => true,
                'constraint' => '500'
            ],

            'created_at timestamp default current_timestamp',
            'updated_at timestamp default current_timestamp on update current_timestamp'
        ]);

        $this->forge->addKey('id');
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}