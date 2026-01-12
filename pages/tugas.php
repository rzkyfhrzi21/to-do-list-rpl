<?php
require_once 'functions/config.php';

/* =========================
   SESSION
========================= */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$sesi_id = $_SESSION['sesi_id'] ?? 0;
if (!$sesi_id) {
    header("Location: login.php");
    exit;
}

/* =========================
   FILTER
========================= */
$filterStatus = $_GET['status'] ?? 'semua';
$filterTanggal = $_GET['tanggal'] ?? ''; // tanggal dari kalender

/* =========================
   HELPER URL
========================= */
function buildUrl($status = null, $tanggal = null)
{
    $s = $_GET['status'] ?? 'semua';
    $t = $_GET['tanggal'] ?? '';

    if ($status !== null)
        $s = $status;
    if ($tanggal !== null)
        $t = $tanggal;

    $t = urlencode($t);
    return "?page=tugas&status=$s&tanggal=$t";
}

/* =========================
   DATA MASTER (untuk modal tambah/edit)
========================= */
$qHariModal = mysqli_query($koneksi, "SELECT id_hari, nama_hari, tanggal FROM hari ORDER BY tanggal ASC");
$qWaktuModal = mysqli_query($koneksi, "SELECT id_waktu, jam_mulai, jam_selesai FROM waktu ORDER BY jam_mulai ASC");
$qKetModal = mysqli_query($koneksi, "SELECT id_keterangan, jenis_pekerjaan FROM keterangan ORDER BY id_keterangan ASC");
?>

<div class="page-heading">
    <h3>Tugas</h3>
    <p class="text-muted">Melihat dan mengelola tugas berdasarkan tanggal</p>

    <!-- =========================
         NOTIFIKASI (AUTO) - akan terisi via JS
    ========================= -->
    <div id="notifWrap" class="mb-3"></div>

    <!-- =========================
         FILTER TANGGAL (KALENDER)
    ========================= -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="get" class="row g-2 align-items-end">
                <input type="hidden" name="page" value="tugas">
                <input type="hidden" name="status" value="<?= htmlspecialchars($filterStatus) ?>">

                <div class="col-md-4">
                    <label class="form-label">Pilih Tanggal</label>
                    <input type="date" name="tanggal" class="form-control"
                        value="<?= htmlspecialchars($filterTanggal) ?>">
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Tampilkan</button>
                </div>

                <div class="col-md-2">
                    <a href="?page=tugas&status=<?= urlencode($filterStatus) ?>" class="btn btn-light w-100">Reset</a>
                </div>

                <div class="col-md-4 text-end">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal"
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
        <a href="<?= buildUrl('semua', null) ?>"
            class="btn <?= $filterStatus == 'semua' ? 'btn-primary' : 'btn-light' ?>">Semua</a>
        <a href="<?= buildUrl('belum', null) ?>"
            class="btn <?= $filterStatus == 'belum' ? 'btn-secondary' : 'btn-light' ?>">Belum</a>
        <a href="<?= buildUrl('selesai', null) ?>"
            class="btn <?= $filterStatus == 'selesai' ? 'btn-success' : 'btn-light' ?>">Selesai</a>
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
                        <th>Tanggal Deadline</th>
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
                            id_tugas,
                            nama_tugas,
                            deskripsi_tugas,
                            tanggal_deadline,
                            waktu_deadline,
                            keterangan,
                            status_tugas
                        FROM tugas
                        WHERE id_pengguna = '$sesi_id'
                    ";

                    /* FILTER TANGGAL */
                    if (!empty($filterTanggal)) {
                        $tgl = mysqli_real_escape_string($koneksi, $filterTanggal);
                        $sql .= " AND tanggal_deadline = '$tgl'";
                    }

                    /* FILTER STATUS */
                    if ($filterStatus !== 'semua') {
                        $st = mysqli_real_escape_string($koneksi, $filterStatus);
                        $sql .= " AND status_tugas = '$st'";
                    }

                    $sql .= " ORDER BY tanggal_deadline ASC, waktu_deadline ASC";

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
                                <b><?= htmlspecialchars($row['nama_tugas']); ?></b><br>
                                <small class="text-muted">
                                    <?= htmlspecialchars($row['deskripsi_tugas']); ?>
                                </small>
                            </td>

                            <td>
                                <?= date('d M Y', strtotime($row['tanggal_deadline'])); ?>
                            </td>

                            <td>
                                <?= date('H:i', strtotime($row['waktu_deadline'])); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['keterangan']); ?>
                            </td>

                            <td>
                                <span class="badge <?= $row['status_tugas'] === 'selesai' ? 'bg-success' : 'bg-danger'; ?>">
                                    <?= ucfirst($row['status_tugas']); ?>
                                </span>
                            </td>

                            <td>
                                <button class="btn btn-sm btn-warning"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditTugas"
                                    data-id="<?= $row['id_tugas']; ?>"
                                    data-nama="<?= htmlspecialchars($row['nama_tugas']); ?>"
                                    data-deskripsi="<?= htmlspecialchars($row['deskripsi_tugas']); ?>"
                                    data-tanggal="<?= $row['tanggal_deadline']; ?>"
                                    data-waktu="<?= $row['waktu_deadline']; ?>"
                                    data-keterangan="<?= htmlspecialchars($row['keterangan']); ?>">
                                    Edit
                                </button>

                                <button class="btn btn-sm btn-danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalDeleteTugas"
                                    data-id="<?= $row['id_tugas']; ?>">
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
                        <label>Tanggal</label>
                        <input type="date" name="tanggal_deadline" class="form-select flatpickr mb-3" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Waktu</label>
                        <input type="date" name="waktu_deadline" class="form-control flatpickr-time-picker-24h" placeholder="Select time..">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Keterangan</label>
                        <select name="keterangan" class="form-select" required>
                            <option value="">-</option>
                            <option value="Sebagian">Sebagian</option>
                            <option value="Perbaikan">Perbaikan</option>
                            <option value="Keseluruhan">Keseluruhan</option>
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
                        <label>Tanggal</label>
                        <input type="date"
                            name="tanggal_deadline"
                            id="edit-tanggal"
                            class="form-select flatpickr mb-3"
                            required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Waktu</label>
                        <input type="time"
                            name="waktu_deadline"
                            id="edit-waktu"
                            class="form-control flatpickr-time-picker-24h"
                            required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Keterangan</label>
                        <select name="keterangan" id="edit-keterangan" class="form-select">
                            <option value="">- Pilih Keterangan -</option>
                            <option value="Sebagian">Sebagian</option>
                            <option value="Perbaikan">Perbaikan</option>
                            <option value="Keseluruhan">Keseluruhan</option>
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
        // isi modal edit
        document.querySelectorAll('[data-bs-target="#modalEditTugas"]').forEach(btn => {
            btn.addEventListener("click", function() {

                document.getElementById('edit-id').value =
                    this.dataset.id || '';

                document.getElementById('edit-nama').value =
                    this.dataset.nama || '';

                document.getElementById('edit-deskripsi').value =
                    this.dataset.deskripsi || '';

                document.getElementById('edit-tanggal').value =
                    this.dataset.tanggal || '';

                document.getElementById('edit-waktu').value =
                    this.dataset.waktu || '';

                document.getElementById('edit-keterangan').value =
                    this.dataset.keterangan || '';
            });
        });

        // isi modal delete
        document.querySelectorAll('[data-bs-target="#modalDeleteTugas"]').forEach(btn => {
            btn.addEventListener("click", function() {
                document.getElementById('delete-id').value = this.dataset.id;
            });
        });

        // ====== NOTIF AUTO LOAD ======
        async function loadNotif() {
            const wrap = document.getElementById('notifWrap');
            try {
                const res = await fetch('/to-do-list/api/notifications.php');
                const data = await res.json();

                if (!data.length) {
                    wrap.innerHTML = '';
                    return;
                }

                wrap.innerHTML = `
              <div class="alert alert-warning">
                <div class="d-flex justify-content-between align-items-start">
                  <div>
                    <b>Reminder (H-1)</b><br>
                    <small>Ini akan muncul otomatis jika ada tugas yang deadline besok.</small>
                  </div>
                  <button class="btn btn-sm btn-outline-dark" id="btnReadAll">Tandai semua dibaca</button>
                </div>
                <hr class="my-2">
                ${data.map(n => `
                  <div class="mb-2 p-2 bg-white rounded border">
                    <b>${escapeHtml(n.title)}</b><br>
                    <div>${escapeHtml(n.message)}</div>
                    <small class="text-muted">${n.created_at}</small><br>
                    <button class="btn btn-sm btn-outline-secondary mt-2" onclick="markRead(${n.id})">Tandai dibaca</button>
                  </div>
                `).join('')}
              </div>
            `;

                document.getElementById('btnReadAll').onclick = () => markRead('all');
            } catch (e) {
                console.error(e);
            }
        }

        // helper escape agar aman dari XSS
        function escapeHtml(str) {
            if (str === null || str === undefined) return '';
            return String(str)
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        // tandai dibaca
        window.markRead = async function(id) {
            try {
                const form = new URLSearchParams();
                form.append('id', id);

                const res = await fetch('/to-do-list/api/notifications_read.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: form.toString()
                });

                const out = await res.json();
                if (out && out.ok) {
                    loadNotif();
                }
            } catch (e) {
                console.error(e);
            }
        }

        loadNotif();
        setInterval(loadNotif, 15000);
    });
</script>