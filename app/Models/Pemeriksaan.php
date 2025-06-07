<?php

namespace App\Models;

use CodeIgniter\Model;

class Pemeriksaan extends Model
{
    protected $table = 'pemeriksaan';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'sertifikat_id', 'dokumen_pendukung_id', 'pemeriksa_id', 'jenis_file', 'status_pemeriksaan', 'tanggal_pemeriksaan', 'catatan_umum'
    ];

    // Contoh method untuk mengambil semua data pemeriksaan dengan join ke tabel kontraktor (user)
    public function getAllPemeriksaan()
    {
        return $this->select('pemeriksaan.*, sertifikat.file_sertifikat, dokumen_pendukung.nama_file, users.name as pemeriksa_name')
            ->join('sertifikat', 'sertifikat.id = pemeriksaan.sertifikat_id', 'left')
            ->join('dokumen_pendukung', 'dokumen_pendukung.id = pemeriksaan.dokumen_pendukung_id', 'left')
            ->join('users', 'users.id = pemeriksaan.pemeriksa_id', 'left')
            ->findAll();
    }
}