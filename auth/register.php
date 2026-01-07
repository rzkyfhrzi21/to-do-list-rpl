<?php
require_once '../functions/config.php';
$usernameLogin  =  isset($_GET['username']) ? $_GET['username'] : '';
$nama_penggunaLogin =  isset($_GET['nama_pengguna']) ? $_GET['nama_pengguna'] : '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="robots" content="noindex, nofollow">

    <title>Registrasi - <?php echo NAMA_WEB ?></title>

    <link rel="shortcut icon" href="../assets/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="../assets/compiled/css/app.css">
    <link rel="stylesheet" href="../assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="../assets/compiled/css/auth.css">
    <link rel="stylesheet" href="../assets/extensions/sweetalert2/sweetalert2.min.css">

    <style>
        body {
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            height: auto;
            margin: 0;
        }

        #auth {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            max-width: 400px;
            width: 100%;
        }

        p {
            font-size: 16px;
        }

        label {
            font-size: 14px;
        }
    </style>
</head>

<body>
    <script src="../assets/static/js/initTheme.js"></script>

    <div id="app">
        <div class="content-wrapper container">
            <div class="row h-100">
                <div class="card mt-5">
                    <div class="card-header">
                        <h2 class="auth-title text-primary">Registrasi Akun</h2>
                        <p class="auth-subtitle mb-2">
                            Buat akun untuk mulai menggunakan platform pengelolaan tugas dan <br> aktivitas harian Anda
                        </p>
                    </div>

                    <div class="card-body">
                        <form class="form" data-parsley-validate action="../functions/function_auth.php" method="post" autocomplete="off">
                            <div class="form-group position-relative has-icon-left mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <div class="position-relative">
                                    <input type="text"
                                        name="nama"
                                        class="form-control form-control-xl"
                                        placeholder="Nama lengkap Anda"
                                        value="<?= $nama_penggunaLogin; ?>"
                                        required minlength="3">
                                    <div class="form-control-icon">
                                        <i class="bi bi-person"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group position-relative has-icon-left mb-3">
                                <label class="form-label">Username</label>
                                <div class="position-relative">
                                    <input type="username"
                                        name="username"
                                        class="form-control form-control-xl"
                                        placeholder="Username"
                                        value="<?= $usernameLogin; ?>"
                                        required minlength="5">
                                    <div class="form-control-icon">
                                        <i class="bi bi-person-badge"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group position-relative has-icon-left mb-3">
                                <label class="form-label">Password</label>
                                <div class="position-relative">
                                    <input type="password"
                                        name="password"
                                        class="form-control form-control-xl"
                                        placeholder="Minimal 5 karakter"
                                        required minlength="5">
                                    <div class="form-control-icon">
                                        <i class="bi bi-shield-lock"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group position-relative has-icon-left mb-3">
                                <label class="form-label">Konfirmasi Password</label>
                                <div class="position-relative">
                                    <input type="password"
                                        name="konfirmasi_password"
                                        class="form-control form-control-xl"
                                        placeholder="Ulangi password"
                                        required minlength="5">
                                    <div class="form-control-icon">
                                        <i class="bi bi-shield-lock"></i>
                                    </div>
                                </div>
                            </div>
                            <button type="submit"
                                name="btn_register"
                                class="btn btn-primary btn-block btn-lg shadow-lg mt-2">
                                Daftar Sekarang
                            </button>
                        </form>

                        <div class="text-center mt-3 text-lg fs-4">
                            <p class="text-gray-600">
                                Sudah punya akun?
                                <a href="login" class="font-bold">Masuk</a>
                            </p>
                            <p>© <?= date('Y'); ?> <?= NAMA_WEB; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/extensions/jquery/jquery.min.js"></script>
    <script src="../assets/extensions/parsleyjs/parsley.min.js"></script>
    <script src="../assets/static/js/pages/parsley.js"></script>
    <script src="../assets/extensions/sweetalert2/sweetalert2.min.js"></script>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get("status");
        const action = urlParams.get("action");

        if (status === "success") {
            if (action === "registered") {
                Swal.fire({
                    icon: "success",
                    title: "Registrasi Berhasil",
                    text: "Akun berhasil dibuat. Silakan login untuk mulai mengelola tugas Anda.",
                    timer: 3000,
                    showConfirmButton: false,
                });
            }
        } else if (status === "error") {
            if (action === "login") {
                Swal.fire({
                    icon: "error",
                    title: "Login Gagal",
                    text: "Username atau password tidak valid.",
                    timer: 3000,
                    showConfirmButton: false,
                });
            }
        }
    </script>
</body>

</html>