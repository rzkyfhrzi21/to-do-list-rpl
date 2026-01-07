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
   INPUT TANGGAL (SEARCH)
========================= */
$tanggalInput = $_GET['tanggal'] ?? date('Y-m-d');

/* =========================
   CARD STATISTIK
========================= */

/* 1. Apakah ada tugas di tanggal terdekat */
$qAda = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total
    FROM tugas t
    JOIN hari h ON t.id_hari = h.id_hari
    WHERE t.id_pengguna = '$sesi_id'
");
$totalSemua = mysqli_fetch_assoc($qAda)['total'];
$adaTugas = $totalSemua > 0 ? 'Ada' : 'Tidak Ada';

/* 2. Total tugas di tanggal terdekat */
$qTotal = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total
    FROM tugas t
    JOIN hari h ON t.id_hari = h.id_hari
    WHERE t.id_pengguna = '$sesi_id'
      AND h.tanggal = (
          SELECT h2.tanggal
          FROM tugas t2
          JOIN hari h2 ON t2.id_hari = h2.id_hari
          WHERE t2.id_pengguna = '$sesi_id'
          ORDER BY ABS(DATEDIFF(h2.tanggal, '$tanggalInput')) ASC
          LIMIT 1
      )
");
$totalTugasTerdekat = mysqli_fetch_assoc($qTotal)['total'];

/* 3. Hitung sisa hari menuju tanggal terdekat */
$qTerdekat = mysqli_query($koneksi, "
    SELECT h.tanggal
    FROM tugas t
    JOIN hari h ON t.id_hari = h.id_hari
    WHERE t.id_pengguna = '$sesi_id'
    ORDER BY ABS(DATEDIFF(h.tanggal, '$tanggalInput')) ASC
    LIMIT 1
");
$hariTerdekat = mysqli_fetch_assoc($qTerdekat)['tanggal'] ?? null;

$sisaHari = '-';
if ($hariTerdekat) {
    $sisaHari = (strtotime($hariTerdekat) - strtotime(date('Y-m-d'))) / 86400;
}

/* =========================
   DATA TABEL TUGAS
========================= */
$sql = "
    SELECT
        t.nama_tugas,
        t.deskripsi_tugas,
        t.status_tugas,
        h.nama_hari,
        h.tanggal,
        w.jam_mulai,
        w.jam_selesai,
        k.jenis_pekerjaan
    FROM tugas t
    JOIN hari h ON t.id_hari = h.id_hari
    JOIN waktu w ON t.id_waktu = w.id_waktu
    LEFT JOIN keterangan k ON t.id_keterangan = k.id_keterangan
    WHERE t.id_pengguna = '$sesi_id'
    ORDER BY ABS(DATEDIFF(h.tanggal, '$tanggalInput')) ASC,
             w.jam_mulai ASC
";
$data = mysqli_query($koneksi, $sql);
?>

<div class="page-heading">
    <h3>Hari Ini / Tanggal Terdekat</h3>
    <p class="text-muted">Pencarian tugas berdasarkan hari atau tanggal terdekat</p>

    <!-- =========================
         SEARCH TANGGAL
    ========================= -->
    <form method="get" class="mb-4">
        <input type="hidden" name="page" value="hari_ini">
        <div class="row g-2">
            <div class="col-md-4">
                <input type="date"
                    name="tanggal"
                    class="form-control"
                    value="<?= htmlspecialchars($tanggalInput) ?>">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100">
                    Cari Tanggal Terdekat
                </button>
            </div>
        </div>
    </form>

    <!-- =========================
         CARD STATISTIK
    ========================= -->
    <div class="row mb-4">

        <!-- ADA TUGAS -->
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="stats-icon purple mb-2">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <h6 class="text-muted font-semibold">Apakah Ada Tugas</h6>
                    <h6 class="font-extrabold mb-0"><?= $adaTugas ?></h6>
                </div>
            </div>
        </div>

        <!-- TOTAL TUGAS -->
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="stats-icon blue mb-2">
                        <i class="bi bi-list-task"></i>
                    </div>
                    <h6 class="text-muted font-semibold">Total Tugas (Tanggal Terdekat)</h6>
                    <h6 class="font-extrabold mb-0"><?= $totalTugasTerdekat ?></h6>
                </div>
            </div>
        </div>

        <!-- DEADLINE -->
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="stats-icon red mb-2">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <h6 class="text-muted font-semibold">Menuju Deadline Terdekat</h6>
                    <h6 class="font-extrabold mb-0">
                        <?= is_numeric($sisaHari) ? $sisaHari . ' Hari' : '-' ?>
                    </h6>
                </div>
            </div>
        </div>

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
                        <th>Hari / Tanggal</th>
                        <th>Waktu</th>
                        <th>Jenis</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>

                    <?php if (mysqli_num_rows($data) == 0): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                Tidak ada tugas.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php while ($row = mysqli_fetch_assoc($data)): ?>
                        <tr>
                            <td>
                                <b><?= htmlspecialchars($row['nama_tugas']) ?></b><br>
                                <small class="text-muted">
                                    <?= htmlspecialchars($row['deskripsi_tugas']) ?>
                                </small>
                            </td>
                            <td><?= $row['nama_hari'] ?>, <?= $row['tanggal'] ?></td>
                            <td><?= $row['jam_mulai'] ?> - <?= $row['jam_selesai'] ?></td>
                            <td><?= $row['jenis_pekerjaan'] ?? '-' ?></td>
                            <td>
                                <span class="badge <?= $row['status_tugas'] == 'selesai' ? 'bg-success' : 'bg-danger' ?>">
                                    <?= ucfirst($row['status_tugas']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                </tbody>
            </table>
        </div>
    </div>
</div>