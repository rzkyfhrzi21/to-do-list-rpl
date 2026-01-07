<?php
require_once 'config.php';
session_start();
ob_start();

$sesi_id   = $_SESSION['sesi_id'] ?? null;


/* ======================================================
   UPDATE DATA PROFIL (DATA UMUM)
====================================================== */
if (isset($_POST['btn_editdatapribadi'])) {

	$id_pengguna	= $_POST['id_pengguna'];
	$nama			= trim(htmlspecialchars($_POST['nama']));
	$username		= trim(htmlspecialchars($_POST['username']));

	mysqli_query($koneksi, "
        UPDATE pengguna SET
            nama = '$nama',
            username = '$username'
        WHERE id_pengguna = '$id_pengguna'
    ");

	header("Location: ../?page=profil&action=editprofil&result=success");
	exit;
}

/* ======================================================
   UPDATE DATA AKUN (USERNAME / PASSWORD / ROLE)
====================================================== */
if (isset($_POST['btn_editdataakun'])) {

	$id_pengguna   = $_POST['id_pengguna'];
	$password  = htmlspecialchars(trim($_POST['password']));
	$confirm   = htmlspecialchars(trim($_POST['konfirmasi_password']));


	if (!empty($password)) {
		if ($password !== $confirm) {
			header("Location: ../?page=profil&action=password_mismatch");
			exit;
		}
		$query = "
            UPDATE pengguna SET 
                password = '$password'
            WHERE id_pengguna = '$id_pengguna'
        ";
	}

	mysqli_query($koneksi, $query);

	header("Location: ../?page=profil&action=editprofil&result=success");
	exit;
}

/* ======================================================
   HAPUS AKUN
====================================================== */
if (isset($_POST['btn_deleteakun'])) {

	$id_pengguna  = $_POST['id_pengguna'];

	mysqli_query($koneksi, "DELETE FROM pengguna WHERE id_pengguna='$id_pengguna'");

	if ($id_pengguna === $sesi_id) {
		header("Location: ../auth/logout.php");
	} else {
		header("Location: ../?page=profil&action=deleteakun&result=success");
	}
	exit;
}

if (isset($_POST['btn_userregister'])) {

	$nama     = trim($_POST['nama'] ?? '');
	$username    = trim($_POST['username'] ?? '');
	$password = trim($_POST['password'] ?? '');
	$confirm  = trim($_POST['konfirmasi_password'] ?? '');

	// simpan input untuk refill form
	$_SESSION['form_data'] = [
		'nama' => $nama,
		'username' => $username
	];

	// ================= VALIDASI WAJIB =================
	if ($nama === '' || $username === '' || $password === '' || $confirm === '') {
		header("Location: ../?page=registrasi&action=emptyregisterfield");
		exit;
	}

	// validasi format username
	if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
		header("Location: ../?page=registrasi&action=invalidusername");
		exit;
	}

	// konfirmasi password
	if ($password !== $confirm) {
		header("Location: ../?page=registrasi&action=passwordnotsame");
		exit;
	}

	// cek username unik
	$cek_username = mysqli_query($koneksi, "SELECT id_pengguna FROM pengguna WHERE username='$username' LIMIT 1");
	if (mysqli_num_rows($cek_username) > 0) {
		header("Location: ../?page=registrasi&action=usernameexist");
		exit;
	}

	// ================= INSERT DATA =================
	$query = "
        INSERT INTO pengguna (
            nama,
            username,
            password,
        ) VALUES (
            '$nama',
            '$username',
            '$password'
        )
    ";

	$insert = mysqli_query($koneksi, $query);

	if ($insert) {
		unset($_SESSION['form_data']);
		header("Location: ../?page=registrasi&action=adduser&result=success");
		exit;
	} else {
		header("Location: ../?page=registrasi&action=adduser&result=error");
		exit;
	}
}


ob_end_flush();
