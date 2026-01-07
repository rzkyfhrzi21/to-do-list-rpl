<?php
require_once 'functions/config.php';

$sesi_id = $_SESSION['sesi_id'] ?? 0;
if (!$sesi_id) {
    header("Location: login.php");
    exit;
}

/* =========================
   DATA MASTER
========================= */
$qHari  = mysqli_query($koneksi, "SELECT * FROM hari ORDER BY tanggal ASC");
$qWaktu = mysqli_query($koneksi, "SELECT * FROM waktu ORDER BY jam_mulai ASC");
$qKet   = mysqli_query($koneksi, "SELECT * FROM keterangan ORDER BY id_keterangan ASC");
?>

<div class="page-heading">
    <h3>Master Data</h3>
    <p class="text-muted">Kelola data hari, waktu, dan keterangan</p>
</div>

<!-- =========================
     MASTER HARI
========================= -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between">
        <h5>📅 Data Hari</h5>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahHari">
            Tambah
        </button>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Hari</th>
                    <th>Tanggal</th>
                    <th width="150">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($h = mysqli_fetch_assoc($qHari)): ?>
                    <tr>
                        <td><?= $h['nama_hari']; ?></td>
                        <td><?= $h['tanggal']; ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEditHari"
                                data-id="<?= $h['id_hari']; ?>"
                                data-nama="<?= $h['nama_hari']; ?>"
                                data-tanggal="<?= $h['tanggal']; ?>">
                                Edit
                            </button>
                            <button class="btn btn-sm btn-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#modalDeleteHari"
                                data-id="<?= $h['id_hari']; ?>">
                                Hapus
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- =========================
     MASTER WAKTU
========================= -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between">
        <h5>⏰ Data Waktu</h5>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahWaktu">
            Tambah
        </button>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Jam Mulai</th>
                    <th>Jam Selesai</th>
                    <th width="150">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($w = mysqli_fetch_assoc($qWaktu)): ?>
                    <tr>
                        <td><?= $w['jam_mulai']; ?></td>
                        <td><?= $w['jam_selesai']; ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEditWaktu"
                                data-id="<?= $w['id_waktu']; ?>"
                                data-mulai="<?= $w['jam_mulai']; ?>"
                                data-selesai="<?= $w['jam_selesai']; ?>">
                                Edit
                            </button>
                            <button class="btn btn-sm btn-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#modalDeleteWaktu"
                                data-id="<?= $w['id_waktu']; ?>">
                                Hapus
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- =========================
     MASTER KETERANGAN
========================= -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between">
        <h5>📝 Data Keterangan</h5>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKeterangan">
            Tambah
        </button>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Jenis</th>
                    <th>Deskripsi</th>
                    <th width="150">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($k = mysqli_fetch_assoc($qKet)): ?>
                    <tr>
                        <td><?= $k['jenis_pekerjaan']; ?></td>
                        <td><?= $k['deskripsi_keterangan']; ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEditKeterangan"
                                data-id="<?= $k['id_keterangan']; ?>"
                                data-jenis="<?= $k['jenis_pekerjaan']; ?>"
                                data-deskripsi="<?= htmlspecialchars($k['deskripsi_keterangan']); ?>">
                                Edit
                            </button>
                            <button class="btn btn-sm btn-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#modalDeleteKeterangan"
                                data-id="<?= $k['id_keterangan']; ?>">
                                Hapus
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- =================================================
     9 MODAL: TAMBAH / EDIT / DELETE
================================================== -->

<!-- =========================
     HARI
========================= -->
<div class="modal fade" id="modalTambahHari" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" action="functions/function_master.php" class="modal-content">
            <div class="modal-header">
                <h5>Tambah Hari</h5>
            </div>
            <div class="modal-body">
                <input type="text" name="nama_hari" class="form-control mb-2" required>
                <input type="date" name="tanggal" class="form-control" required>
            </div>
            <div class="modal-footer">
                <button name="add_hari" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalEditHari" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" action="functions/function_master.php" class="modal-content">
            <input type="hidden" name="id_hari" id="edit-hari-id">
            <div class="modal-header">
                <h5>Edit Hari</h5>
            </div>
            <div class="modal-body">
                <input type="text" name="nama_hari" id="edit-hari-nama" class="form-control mb-2" required>
                <input type="date" name="tanggal" id="edit-hari-tanggal" class="form-control" required>
            </div>
            <div class="modal-footer">
                <button name="edit_hari" class="btn btn-warning">Update</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalDeleteHari" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" action="functions/function_master.php" class="modal-content">
            <input type="hidden" name="id_hari" id="delete-hari-id">
            <div class="modal-header">
                <h5 class="text-danger">Hapus Hari</h5>
            </div>
            <div class="modal-body">Yakin ingin menghapus data hari ini?</div>
            <div class="modal-footer">
                <button name="delete_hari" class="btn btn-danger">Hapus</button>
            </div>
        </form>
    </div>
</div>

<!-- =========================
     WAKTU
========================= -->
<div class="modal fade" id="modalTambahWaktu" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" action="functions/function_master.php" class="modal-content">
            <div class="modal-header">
                <h5>Tambah Waktu</h5>
            </div>
            <div class="modal-body">
                <input type="time" name="jam_mulai" class="form-control mb-2" required>
                <input type="time" name="jam_selesai" class="form-control" required>
            </div>
            <div class="modal-footer">
                <button name="add_waktu" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalEditWaktu" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" action="functions/function_master.php" class="modal-content">
            <input type="hidden" name="id_waktu" id="edit-waktu-id">
            <div class="modal-header">
                <h5>Edit Waktu</h5>
            </div>
            <div class="modal-body">
                <input type="time" name="jam_mulai" id="edit-waktu-mulai" class="form-control mb-2" required>
                <input type="time" name="jam_selesai" id="edit-waktu-selesai" class="form-control" required>
            </div>
            <div class="modal-footer">
                <button name="edit_waktu" class="btn btn-warning">Update</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalDeleteWaktu" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" action="functions/function_master.php" class="modal-content">
            <input type="hidden" name="id_waktu" id="delete-waktu-id">
            <div class="modal-header">
                <h5 class="text-danger">Hapus Waktu</h5>
            </div>
            <div class="modal-body">Yakin ingin menghapus data waktu ini?</div>
            <div class="modal-footer">
                <button name="delete_waktu" class="btn btn-danger">Hapus</button>
            </div>
        </form>
    </div>
</div>

<!-- =========================
     KETERANGAN
========================= -->
<div class="modal fade" id="modalTambahKeterangan" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" action="functions/function_master.php" class="modal-content">
            <div class="modal-header">
                <h5>Tambah Keterangan</h5>
            </div>
            <div class="modal-body">
                <select name="jenis_pekerjaan" class="form-select mb-2">
                    <option>Keseluruhan</option>
                    <option>Sebagian</option>
                    <option>Perbaikan</option>
                </select>
                <textarea name="deskripsi_keterangan" class="form-control" required></textarea>
            </div>
            <div class="modal-footer">
                <button name="add_keterangan" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalEditKeterangan" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" action="functions/function_master.php" class="modal-content">
            <input type="hidden" name="id_keterangan" id="edit-ket-id">
            <div class="modal-header">
                <h5>Edit Keterangan</h5>
            </div>
            <div class="modal-body">
                <select name="jenis_pekerjaan" id="edit-ket-jenis" class="form-select mb-2">
                    <option>Keseluruhan</option>
                    <option>Sebagian</option>
                    <option>Perbaikan</option>
                </select>
                <textarea name="deskripsi_keterangan" id="edit-ket-deskripsi" class="form-control" required></textarea>
            </div>
            <div class="modal-footer">
                <button name="edit_keterangan" class="btn btn-warning">Update</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalDeleteKeterangan" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" action="functions/function_master.php" class="modal-content">
            <input type="hidden" name="id_keterangan" id="delete-ket-id">
            <div class="modal-header">
                <h5 class="text-danger">Hapus Keterangan</h5>
            </div>
            <div class="modal-body">Yakin ingin menghapus data keterangan ini?</div>
            <div class="modal-footer">
                <button name="delete_keterangan" class="btn btn-danger">Hapus</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        document.querySelectorAll('[data-bs-target="#modalEditHari"]').forEach(btn => {
            btn.onclick = () => {
                document.getElementById('edit-hari-id').value = btn.dataset.id;
                document.getElementById('edit-hari-nama').value = btn.dataset.nama;
                document.getElementById('edit-hari-tanggal').value = btn.dataset.tanggal;
            };
        });

        document.querySelectorAll('[data-bs-target="#modalDeleteHari"]').forEach(btn => {
            btn.onclick = () => {
                document.getElementById('delete-hari-id').value = btn.dataset.id;
            };
        });

        document.querySelectorAll('[data-bs-target="#modalEditWaktu"]').forEach(btn => {
            btn.onclick = () => {
                document.getElementById('edit-waktu-id').value = btn.dataset.id;
                document.getElementById('edit-waktu-mulai').value = btn.dataset.mulai;
                document.getElementById('edit-waktu-selesai').value = btn.dataset.selesai;
            };
        });

        document.querySelectorAll('[data-bs-target="#modalDeleteWaktu"]').forEach(btn => {
            btn.onclick = () => {
                document.getElementById('delete-waktu-id').value = btn.dataset.id;
            };
        });

        document.querySelectorAll('[data-bs-target="#modalEditKeterangan"]').forEach(btn => {
            btn.onclick = () => {
                document.getElementById('edit-ket-id').value = btn.dataset.id;
                document.getElementById('edit-ket-jenis').value = btn.dataset.jenis;
                document.getElementById('edit-ket-deskripsi').value = btn.dataset.deskripsi;
            };
        });

        document.querySelectorAll('[data-bs-target="#modalDeleteKeterangan"]').forEach(btn => {
            btn.onclick = () => {
                document.getElementById('delete-ket-id').value = btn.dataset.id;
            };
        });

    });
</script>