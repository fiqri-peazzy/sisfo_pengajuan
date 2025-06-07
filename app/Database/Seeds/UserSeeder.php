<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'username' => 'ppk_user',
                'password' => password_hash('ppk12345', PASSWORD_DEFAULT),
                'role' => 'ppk',
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@example.com',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'kontraktor_user',
                'password' => password_hash('kontraktor123', PASSWORD_DEFAULT),
                'role' => 'kontraktor',
                'name' => 'Sari Dewi',
                'email' => 'sari.dewi@example.com',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'konsultan_user',
                'password' => password_hash('konsultan123', PASSWORD_DEFAULT),
                'role' => 'konsultan',
                'name' => 'Agus Wijaya',
                'email' => 'agus.wijaya@example.com',
                'created_at' => date('Y-m-d H:i:s')
            ],
        ];

        // Insert batch data into users table
        $this->db->table('users')->insertBatch($data);
    }
}