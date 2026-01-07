<?php

require_once 'config.php';
$tanggal_sekarang   = date('Y-m-d');
$waktu_sekarang     = date('H:i');
$bulan_sekarang     = date('m');
$tahun_sekarang     = date('Y');

$hitung_status_semua   = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM tasks WHERE id_user='$sesi_id' "));
$hitung_status_belum   = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM tasks WHERE id_user='$sesi_id' AND status='belum' "));
$hitung_status_proses  = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM tasks WHERE id_user='$sesi_id' AND status='proses' "));
$hitung_status_selesai = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM tasks WHERE id_user='$sesi_id' AND status='selesai' "));

$hitung_priority_semua  = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM tasks WHERE id_user='$sesi_id' "));
$hitung_priority_rendah = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM tasks WHERE id_user='$sesi_id' AND priority='rendah' "));
$hitung_priority_sedang = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM tasks WHERE id_user='$sesi_id' AND priority='sedang' "));
$hitung_priority_tinggi = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM tasks WHERE id_user='$sesi_id' AND priority='tinggi' "));