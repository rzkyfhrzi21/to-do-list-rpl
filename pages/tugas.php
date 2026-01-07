<?php
require_once 'functions/config.php';

/* =========================
   SESSION
========================= */
$sesi_id = $_SESSION['sesi_id'] ?? 0;
if (!$sesi_id) {
    header("Location: login.php");
    exit;
}

/* =========================
   FILTER
========================= */
$filterStatus = $_GET['status'] ?? 'semua';
$filterHari   = $_GET['id_hari'] ?? 'semua';

/* =========================
   HELPER URL
========================= */
function buildUrl($status = null, $hari = null)
{
    $s = $_GET['status'] ?? 'semua';
    $h = $_GET['id_hari'] ?? 'semua';

    if ($status !== null) $s = $status;
    if ($hari !== null)   $h = $hari;

    return "?page=tugas&status=$s&id_hari=$h";
}

/* =========================
   DATA MASTER
========================= */
$qHariFilter = mysqli_query($koneksi, "SELECT id_hari, nama_hari, tanggal FROM hari ORDER BY tanggal ASC");
$qHariModal  = mysqli_query($koneksi, "SELECT id_hari, nama_hari, tanggal FROM hari ORDER BY tanggal ASC");
$qWaktuModal = mysqli_query($koneksi, "SELECT id_waktu, jam_mulai, jam_selesai FROM waktu ORDER BY jam_mulai ASC");
$qKetModal   = mysqli_query($koneksi, "SELECT id_keterangan, jenis_pekerjaan FROM keterangan ORDER BY id_keterangan ASC");
?>

<div class="page-heading">
    <h3>Tugas</h3>
    <p class="text-muted">Melihat dan mengelola tugas berdasarkan hari</p>

    <!-- =========================
         PILIH HARI (FITUR BARU)
    ========================= -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="get" class="row g-2 align-items-end">
                <input type="hidden" name="page" value="tugas">
                <input type="hidden" name="status" value="<?= htmlspecialchars($filterStatus) ?>">

                <div class="col-md-4">
                    <label class="form-label">Pilih Hari</label>
                    <select name="id_hari" class="form-select">
                        <option value="semua">-- Semua Hari --</option>
                        <?php while ($h = mysqli_fetch_assoc($qHariFilter)): ?>
                            <option value="<?= $h['id_hari'] ?>" <?= $filterHari == $h['id_hari'] ? 'selected' : '' ?>>
                                <?= $h['nama_hari'] ?> (<?= $h['tanggal'] ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Tampilkan</button>
                </div>

                <div class="col-md-6 text-end">
                    <button type="button"
                        class="btn btn-success"
                        data-bs-toggle="modal"
                        data-bs-target="#modalTambahTugas">
                        <i class="bi bi-plus-circle"></i> Tambah Tugas
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- =========================
         FILTER STATUS
    ========================= -->
    <div class="btn-group mb-3">
        <a href="<?= buildUrl('semua', null) ?>" class="btn <?= $filterStatus == 'semua' ? 'btn-primary' : 'btn-light' ?>">Semua</a>
        <a href="<?= buildUrl('belum', null) ?>" class="btn <?= $filterStatus == 'belum' ? 'btn-secondary' : 'btn-light' ?>">Belum</a>
        <a href="<?= buildUrl('selesai', null) ?>" class="btn <?= $filterStatus == 'selesai' ? 'btn-success' : 'btn-light' ?>">Selesai</a>
    </div>

    <!-- =========================
         TABEL TUGAS
    ========================= -->
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nama Tugas</th>
                        <th>Hari</th>
                        <th>Waktu</th>
                        <th>Keterangan</th>
                        <th>Status</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    $sql = "
                        SELECT
                            t.id_tugas,
                            t.nama_tugas,
                            t.deskripsi_tugas,
                            t.status_tugas,
                            h.nama_hari,
                            h.tanggal,
                            w.jam_mulai,
                            w.jam_selesai,
                            k.jenis_pekerjaan,
                            t.id_hari,
                            t.id_waktu,
                            t.id_keterangan
                        FROM tugas t
                        JOIN hari h ON t.id_hari = h.id_hari
                        JOIN waktu w ON t.id_waktu = w.id_waktu
                        LEFT JOIN keterangan k ON t.id_keterangan = k.id_keterangan
                        WHERE t.id_pengguna = '$sesi_id'
                    ";

                    if ($filterHari !== 'semua') {
                        $sql .= " AND t.id_hari = '$filterHari'";
                    }

                    if ($filterStatus !== 'semua') {
                        $sql .= " AND t.status_tugas = '$filterStatus'";
                    }

                    $sql .= " ORDER BY h.tanggal ASC, w.jam_mulai ASC";

                    $data = mysqli_query($koneksi, $sql);

                    if (mysqli_num_rows($data) == 0):
                    ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                Tidak ada tugas.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php while ($row = mysqli_fetch_assoc($data)): ?>
                        <tr>
                            <td>
                                <b><?= htmlspecialchars($row['nama_tugas']) ?></b><br>
                                <small class="text-muted"><?= htmlspecialchars($row['deskripsi_tugas']) ?></small>
                            </td>
                            <td><?= $row['nama_hari'] ?> (<?= $row['tanggal'] ?>)</td>
                            <td><?= $row['jam_mulai'] ?> - <?= $row['jam_selesai'] ?></td>
                            <td><?= $row['jenis_pekerjaan'] ?? '-' ?></td>
                            <td>
                                <span class="badge <?= $row['status_tugas'] == 'selesai' ? 'bg-success' : 'bg-danger' ?>">
                                    <?= ucfirst($row['status_tugas']) ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-warning"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditTugas"
                                    data-id="<?= $row['id_tugas'] ?>"
                                    data-nama="<?= htmlspecialchars($row['nama_tugas']) ?>"
                                    data-deskripsi="<?= htmlspecialchars($row['deskripsi_tugas']) ?>"
                                    data-hari="<?= $row['id_hari'] ?>"
                                    data-waktu="<?= $row['id_waktu'] ?>"
                                    data-keterangan="<?= $row['id_keterangan'] ?>">
                                    Edit
                                </button>

                                <button class="btn btn-sm btn-danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalDeleteTugas"
                                    data-id="<?= $row['id_tugas'] ?>">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- =========================
     MODAL TAMBAH TUGAS
========================= -->
<div class="modal fade" id="modalTambahTugas" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form method="post" action="functions/function_tugas.php" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Tugas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label>Nama Tugas</label>
                    <input type="text" name="nama_tugas" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi_tugas" class="form-control"></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>Hari</label>
                        <select name="id_hari" class="form-select" required>
                            <?php mysqli_data_seek($qHariModal, 0);
                            while ($h = mysqli_fetch_assoc($qHariModal)): ?>
                                <option value="<?= $h['id_hari'] ?>">
                                    <?= $h['nama_hari'] ?> (<?= $h['tanggal'] ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Waktu</label>
                        <select name="id_waktu" class="form-select" required>
                            <?php mysqli_data_seek($qWaktuModal, 0);
                            while ($w = mysqli_fetch_assoc($qWaktuModal)): ?>
                                <option value="<?= $w['id_waktu'] ?>">
                                    <?= $w['jam_mulai'] ?> - <?= $w['jam_selesai'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Keterangan</label>
                        <select name="id_keterangan" class="form-select">
                            <option value="">-</option>
                            <?php mysqli_data_seek($qKetModal, 0);
                            while ($k = mysqli_fetch_assoc($qKetModal)): ?>
                                <option value="<?= $k['id_keterangan'] ?>">
                                    <?= $k['jenis_pekerjaan'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" name="btn_add" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- =========================
     MODAL EDIT TUGAS
========================= -->
<div class="modal fade" id="modalEditTugas" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form method="post" action="functions/function_tugas.php" class="modal-content">
            <input type="hidden" name="id_tugas" id="edit-id">

            <div class="modal-header">
                <h5 class="modal-title">Edit Tugas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label>Nama Tugas</label>
                    <input type="text" name="nama_tugas" id="edit-nama" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi_tugas" id="edit-deskripsi" class="form-control"></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>Hari</label>
                        <select name="id_hari" id="edit-hari" class="form-select" required>
                            <?php mysqli_data_seek($qHariModal, 0);
                            while ($h = mysqli_fetch_assoc($qHariModal)): ?>
                                <option value="<?= $h['id_hari'] ?>">
                                    <?= $h['nama_hari'] ?> (<?= $h['tanggal'] ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Waktu</label>
                        <select name="id_waktu" id="edit-waktu" class="form-select" required>
                            <?php mysqli_data_seek($qWaktuModal, 0);
                            while ($w = mysqli_fetch_assoc($qWaktuModal)): ?>
                                <option value="<?= $w['id_waktu'] ?>">
                                    <?= $w['jam_mulai'] ?> - <?= $w['jam_selesai'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Keterangan</label>
                        <select name="id_keterangan" id="edit-keterangan" class="form-select">
                            <option value="">-</option>
                            <?php mysqli_data_seek($qKetModal, 0);
                            while ($k = mysqli_fetch_assoc($qKetModal)): ?>
                                <option value="<?= $k['id_keterangan'] ?>">
                                    <?= $k['jenis_pekerjaan'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" name="btn_edit" class="btn btn-primary">Update</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- =========================
     MODAL DELETE TUGAS
========================= -->
<div class="modal fade" id="modalDeleteTugas" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" action="functions/function_tugas.php" class="modal-content">
            <input type="hidden" name="id_tugas" id="delete-id">

            <div class="modal-header">
                <h5 class="modal-title text-danger">Hapus Tugas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus tugas ini?</p>
            </div>

            <div class="modal-footer">
                <button type="submit" name="btn_delete" class="btn btn-danger">Hapus</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        document.querySelectorAll('[data-bs-target="#modalEditTugas"]').forEach(btn => {
            btn.addEventListener("click", function() {
                document.getElementById('edit-id').value = this.dataset.id;
                document.getElementById('edit-nama').value = this.dataset.nama;
                document.getElementById('edit-deskripsi').value = this.dataset.deskripsi;
                document.getElementById('edit-hari').value = this.dataset.hari;
                document.getElementById('edit-waktu').value = this.dataset.waktu;
                document.getElementById('edit-keterangan').value = this.dataset.keterangan || '';
            });
        });

        document.querySelectorAll('[data-bs-target="#modalDeleteTugas"]').forEach(btn => {
            btn.addEventListener("click", function() {
                document.getElementById('delete-id').value = this.dataset.id;
            });
        });

    });
</script>