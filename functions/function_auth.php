<?php

session_start();

require_once 'config.php';

if (isset($_POST['btn_login'])) {
	$username 		= htmlspecialchars($_POST['username']);
	$password 		= htmlspecialchars($_POST['password']);

	$sql_login 		= mysqli_query($koneksi, "SELECT * from pengguna where username = '$username' and password = '$password'");
	$jumlah_user 	= mysqli_num_rows($sql_login);
	$data_user		= mysqli_fetch_array($sql_login);

	if ($jumlah_user > 0) {
		$_SESSION['sesi_id']		= $data_user['id_pengguna'];
		$_SESSION['sesi_nama']		= $data_user['nama'];
		$_SESSION['sesi_username']	= $data_user['username'];

		header('Location: ../index');
	} else {
		header("Location: ../auth/login?action=login&status=error");
	}
}

if (isset($_POST['btn_register'])) {
	$nama          			= htmlspecialchars($_POST['nama']);
	$username          		= htmlspecialchars($_POST['username']);
	$password               = htmlspecialchars($_POST['password']);
	$konfirmasi_password    = htmlspecialchars($_POST['konfirmasi_password']);

	$sql_login          	= mysqli_query($koneksi, "SELECT * from pengguna where username = '$username'");
	$jumlah_pengguna       	= mysqli_num_rows($sql_login);
	$data_pengguna         	= mysqli_fetch_array($sql_login);

	if ($password !== $konfirmasi_password) {
		header("Location: ../auth/register?action=passwordnotsame&status=warning&username=" . $username . '&nama=' . $nama);
	} else {
		if ($jumlah_pengguna > 0) {
			header("Location: ../auth/register?action=userexist&status=warning&nama=" . $nama);
		} else {

			$query_daftar    = "INSERT into pengguna 
                                    set username    = '$username',
                                        nama   = '$nama', 
                                        password    = '$password'";
			$daftar         = mysqli_query($koneksi, $query_daftar);

			if ($daftar) {
				header("Location: ../auth/login?action=registered&status=success");
			} else {
				header("Location: ../auth/register");
			}
		}
	}
}
