<?php

use App\Libraries\CIauth;
use App\Models\User;


if (!function_exists('get_user')) {
    function get_user()
    {
        if (CIauth::check()) {
            $user  = new User();
            return $user->asObject()->where('id', CIauth::id())->first();
        } else {
            return null;
        }
    }
}
if (!function_exists('mapping_month')) {
    function mapping_month($d)
    {
        $bulan = str_pad($d, 2, '0', STR_PAD_LEFT);

        $namaBulan = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];

        $bulanIndo = isset($namaBulan[$bulan]) ? $namaBulan[$bulan] : $bulan;

        return $bulanIndo;
    }
}
if (!function_exists('formatTanggalIndonesia')) {
    function formatTanggalIndonesia($tanggal)
    {
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $tgl = date('j', strtotime($tanggal));
        $bln = date('n', strtotime($tanggal));
        $thn = date('Y', strtotime($tanggal));

        return $tgl . ' ' . $bulan[$bln] . ' ' . $thn;
    }
}