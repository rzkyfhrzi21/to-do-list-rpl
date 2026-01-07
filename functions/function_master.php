<?php
require_once 'config.php';

/* =======================
   HARI
======================= */
if (isset($_POST['add_hari'])) {
    mysqli_query($koneksi, "
        INSERT INTO hari (nama_hari, tanggal)
        VALUES ('$_POST[nama_hari]', '$_POST[tanggal]')
    ");
}

if (isset($_POST['edit_hari'])) {
    mysqli_query($koneksi, "
        UPDATE hari SET
            nama_hari='$_POST[nama_hari]',
            tanggal='$_POST[tanggal]'
        WHERE id_hari='$_POST[id_hari]'
    ");
}

if (isset($_POST['delete_hari'])) {
    mysqli_query($koneksi, "
        DELETE FROM hari WHERE id_hari='$_POST[id_hari]'
    ");
}

/* =======================
   WAKTU
======================= */
if (isset($_POST['add_waktu'])) {
    mysqli_query($koneksi, "
        INSERT INTO waktu (jam_mulai, jam_selesai)
        VALUES ('$_POST[jam_mulai]', '$_POST[jam_selesai]')
    ");
}

if (isset($_POST['edit_waktu'])) {
    mysqli_query($koneksi, "
        UPDATE waktu SET
            jam_mulai='$_POST[jam_mulai]',
            jam_selesai='$_POST[jam_selesai]'
        WHERE id_waktu='$_POST[id_waktu]'
    ");
}

if (isset($_POST['delete_waktu'])) {
    mysqli_query($koneksi, "
        DELETE FROM waktu WHERE id_waktu='$_POST[id_waktu]'
    ");
}

/* =======================
   KETERANGAN
======================= */
if (isset($_POST['add_keterangan'])) {
    mysqli_query($koneksi, "
        INSERT INTO keterangan (jenis_pekerjaan, deskripsi_keterangan)
        VALUES ('$_POST[jenis_pekerjaan]', '$_POST[deskripsi_keterangan]')
    ");
}

if (isset($_POST['edit_keterangan'])) {
    mysqli_query($koneksi, "
        UPDATE keterangan SET
            jenis_pekerjaan='$_POST[jenis_pekerjaan]',
            deskripsi_keterangan='$_POST[deskripsi_keterangan]'
        WHERE id_keterangan='$_POST[id_keterangan]'
    ");
}

if (isset($_POST['delete_keterangan'])) {
    mysqli_query($koneksi, "
        DELETE FROM keterangan WHERE id_keterangan='$_POST[id_keterangan]'
    ");
}

header("Location: ../index.php?page=master_data");
exit;
