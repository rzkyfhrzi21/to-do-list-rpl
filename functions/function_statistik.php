<?php
require_once 'config.php';

$sesi_id = $_SESSION['sesi_id'];

/* =======================
   JUMLAH TUGAS
======================= */
$qTotal = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total
    FROM tugas
    WHERE id_pengguna = '$sesi_id'
");
$totalTask = mysqli_fetch_assoc($qTotal)['total'];

/* =======================
   TUGAS SELESAI
======================= */
$qSelesai = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total
    FROM tugas
    WHERE id_pengguna = '$sesi_id'
      AND status_tugas = 'selesai'
");
$totalSelesai = mysqli_fetch_assoc($qSelesai)['total'];

/* =======================
   TUGAS BELUM SELESAI
======================= */
$qBelum = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total
    FROM tugas
    WHERE id_pengguna = '$sesi_id'
      AND status_tugas = 'belum'
");
$totalBelum = mysqli_fetch_assoc($qBelum)['total'];
