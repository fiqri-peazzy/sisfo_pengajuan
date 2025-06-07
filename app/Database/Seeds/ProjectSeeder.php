<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name'       => 'Jalan 1',
                'location'           => 'Kabupaten A',
                'start_date'         => '2025-01-01',
                'end_date'           => '2025-06-30',
                'description'        => 'Pembangunan jembatan baru di Kabupaten A',
                'status'             => 'in-progress',
                'progress_percentage' => 45,
                'ppk_id'        => 2,
                'kontraktor_id' => 3,
                'konsultan_id'  => 4,
                'created_at'         => date('Y-m-d H:i:s'),
                'updated_at'         => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Jalan Jalan 2',
                'location'           => 'Kota B',
                'start_date'         => '2024-11-15',
                'end_date'           => '2025-05-15',
                'description'        => 'Pembangunan jalan Jalan 2',
                'status'             => 'completed',
                'progress_percentage' => 100,
                'ppk_id'        => 2,
                'kontraktor_id' => 3,
                'konsultan_id'  => 4,
                'created_at'         => date('Y-m-d H:i:s'),
                'updated_at'         => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Renovasi Jembatan Lama',
                'location'           => 'Kabupaten C',
                'start_date'         => '2025-03-01',
                'end_date'           => '2025-09-30',
                'description'        => 'Renovasi jembatan lama yang sudah rusak',
                'status'             => 'pending',
                'progress_percentage' => 0,
                'ppk_id'        => 2,
                'kontraktor_id' => 3,
                'konsultan_id'  => 4,
                'created_at'         => date('Y-m-d H:i:s'),
                'updated_at'         => date('Y-m-d H:i:s'),
            ],
        ];

        // Insert data ke tabel projects
        $this->db->table('projects')->insertBatch($data);
    }
}