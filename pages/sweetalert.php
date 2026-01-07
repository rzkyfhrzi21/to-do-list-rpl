<script>
  const urlParams = new URLSearchParams(window.location.search);
  const status = urlParams.get("status");
  const action = urlParams.get("action");
  const ket = urlParams.get("ket");

  function showAlert(icon, title, text) {
    Swal.fire({
      icon: icon,
      title: title,
      text: text,
      timer: 3000,
      showConfirmButton: false
    });
  }

  /* ===============================
     SUCCESS
  =============================== */
  if (status === "success") {
    switch (action) {
      case "adduser":
        showAlert("success", "Berhasil", "Akun pengguna berhasil ditambahkan");
        break;
      case "edituser":
        showAlert("success", "Berhasil", "Data pengguna berhasil diperbarui");
        break;
      case "deleteuser":
        showAlert("success", "Berhasil", "Akun pengguna berhasil dihapus");
        break;
      case "addtask":
        showAlert("success", "Berhasil", "Tugas baru berhasil ditambahkan");
        break;
      case "edittask":
        showAlert("success", "Berhasil", "Tugas berhasil diperbarui");
        break;
      case "deletetask":
        showAlert("success", "Berhasil", "Tugas berhasil dihapus");
        break;
    }
  }

  /* ===============================
     ERROR
  =============================== */
  else if (status === "error") {
    switch (action) {
      case "adduser":
        showAlert("error", "Gagal", "Gagal menambahkan akun pengguna");
        break;
      case "edituser":
        showAlert("error", "Gagal", "Gagal memperbarui data pengguna");
        break;
      case "deleteuser":
        showAlert("error", "Gagal", "Gagal menghapus akun pengguna");
        break;
      case "addtask":
        showAlert("error", "Gagal", "Gagal menambahkan tugas");
        break;
      case "edittask":
        showAlert("error", "Gagal", "Gagal memperbarui tugas");
        break;
      case "deletetask":
        showAlert("error", "Gagal", "Gagal menghapus tugas");
        break;
      case "login":
        showAlert("error", "Login Gagal", "Username atau password tidak sesuai");
        break;
    }
  }

  /* ===============================
     WARNING
  =============================== */
  else if (status === "warning") {
    switch (action) {
      case "userexist":
        showAlert("warning", "Peringatan", "Username sudah digunakan, silakan gunakan yang lain");
        break;
      case "passwordnotsame":
        showAlert("warning", "Peringatan", "Konfirmasi password tidak sesuai");
        break;
    }

    if (ket === "datanotvalid") {
      showAlert("warning", "Peringatan", "Data yang dimasukkan tidak valid");
    }
  }
</script>