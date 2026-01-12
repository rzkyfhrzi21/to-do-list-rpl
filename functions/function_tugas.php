<?php
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$sesi_id = $_SESSION['sesi_id'] ?? 0;
if (!$sesi_id) {
    header("Location: ../login.php");
    exit;
}

/* =======================
   TAMBAH TUGAS
======================= */
if (isset($_POST['btn_add'])) {

    $nama      = trim($_POST['nama_tugas']);
    $desk      = trim($_POST['deskripsi_tugas']);
    $tanggal   = $_POST['tanggal_deadline'];
    $waktu     = $_POST['waktu_deadline'];
    $keterangan = $_POST['keterangan'];

    mysqli_query($koneksi, "
        INSERT INTO tugas (
            nama_tugas,
            deskripsi_tugas,
            tanggal_deadline,
            waktu_deadline,
            keterangan,
            id_pengguna,
            status_tugas
        ) VALUES (
            '$nama',
            '$desk',
            '$tanggal',
            '$waktu',
            '$keterangan',
            '$sesi_id',
            'belum'
        )
    ");

    header("Location: ../index.php?page=tugas");
    exit;
}

/* =======================
   UPDATE TUGAS
======================= */
if (isset($_POST['btn_edit'])) {

    $id        = $_POST['id_tugas'];
    $nama      = trim($_POST['nama_tugas']);
    $desk      = trim($_POST['deskripsi_tugas']);
    $tanggal   = $_POST['tanggal_deadline'];
    $waktu     = $_POST['waktu_deadline'];
    $keterangan = $_POST['keterangan'];

    mysqli_query($koneksi, "
        UPDATE tugas SET
            nama_tugas       = '$nama',
            deskripsi_tugas  = '$desk',
            tanggal_deadline = '$tanggal',
            waktu_deadline   = '$waktu',
            keterangan       = '$keterangan'
        WHERE id_tugas    = '$id'
        AND id_pengguna   = '$sesi_id'
    ");

    header("Location: ../index.php?page=tugas");
    exit;
}

/* =======================
   DELETE TUGAS
======================= */
if (isset($_POST['btn_delete'])) {

    $id = $_POST['id_tugas'];

    mysqli_query($koneksi, "
        DELETE FROM tugas
        WHERE id_tugas    = '$id'
        AND id_pengguna   = '$sesi_id'
    ");

    header("Location: ../index.php?page=tugas");
    exit;
}
