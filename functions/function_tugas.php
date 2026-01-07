<?php
require_once 'config.php';

$sesi_id = $_SESSION['sesi_id'];

/* =======================
   HELPER: NULL ATAU ANGKA
======================= */
function nullOrValue($val)
{
    return ($val === '' || $val === null) ? "NULL" : "'" . $val . "'";
}

/* =======================
   TAMBAH TUGAS
======================= */
if (isset($_POST['btn_add'])) {

    $nama  = trim($_POST['nama_tugas']);
    $desk  = trim($_POST['deskripsi_tugas']);
    $hari  = $_POST['id_hari'];
    $waktu = $_POST['id_waktu'];
    $ket   = $_POST['id_keterangan'] ?? null;

    $ketSql = nullOrValue($ket);

    mysqli_query($koneksi, "
        INSERT INTO tugas (
            nama_tugas,
            deskripsi_tugas,
            id_pengguna,
            id_hari,
            id_waktu,
            id_keterangan,
            status_tugas
        ) VALUES (
            '$nama',
            '$desk',
            '$sesi_id',
            '$hari',
            '$waktu',
            $ketSql,
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

    $id    = $_POST['id_tugas'];
    $nama  = trim($_POST['nama_tugas']);
    $desk  = trim($_POST['deskripsi_tugas']);
    $hari  = $_POST['id_hari'];
    $waktu = $_POST['id_waktu'];
    $ket   = $_POST['id_keterangan'] ?? null;

    $ketSql = nullOrValue($ket);

    mysqli_query($koneksi, "
        UPDATE tugas SET
            nama_tugas      = '$nama',
            deskripsi_tugas = '$desk',
            id_hari         = '$hari',
            id_waktu        = '$waktu',
            id_keterangan   = $ketSql
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
        WHERE id_tugas  = '$id'
        AND id_pengguna = '$sesi_id'
    ");

    header("Location: ../index.php?page=tugas");
    exit;
}
