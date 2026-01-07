<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'env.php';


date_default_timezone_set('Asia/Jakarta');
$pukul = date('H:i A');

// Deteksi server
$host = $_SERVER['HTTP_HOST'];
if ($host === 'localhost' || strpos($host, '127.0.0.1') !== false) {
    $server     = 'localhost';
    $username   = 'root';
    $password   = '';
    $database   = 'to-do-list';
} else {
    $server     = '';
    $username   = '';
    $password   = '';
    $database   = '';
}

$koneksi = mysqli_connect($server, $username, $password, $database);

if (!$koneksi) {
    die('Koneksi gagal: ' . mysqli_connect_error());
}

/* SESSION GLOBAL */
$sesi_id = $_SESSION['sesi_id'] ?? null;


/* ==========================
   HELPER FORMAT TANGGAL
========================== */
if (!function_exists('formatTanggalIndonesia')) {
    function formatTanggalIndonesia($tanggalInggris)
    {
        $namaHari = [
            'Sunday'    => 'Minggu',
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu'
        ];

        $namaBulan = [
            'January'   => 'Januari',
            'February'  => 'Februari',
            'March'     => 'Maret',
            'April'     => 'April',
            'May'       => 'Mei',
            'June'      => 'Juni',
            'July'      => 'Juli',
            'August'    => 'Agustus',
            'September' => 'September',
            'October'   => 'Oktober',
            'November'  => 'November',
            'December'  => 'Desember'
        ];

        $date = new DateTime($tanggalInggris);

        return $namaHari[$date->format('l')] . ', ' .
            $date->format('d') . ' ' .
            $namaBulan[$date->format('F')] . ' ' .
            $date->format('Y');
    }
}
