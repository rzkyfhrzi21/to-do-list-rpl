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

/* 1. Apakah ada tugas */
$qAda = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total
    FROM tugas
    WHERE id_pengguna = '$sesi_id'
");
$totalSemua = mysqli_fetch_assoc($qAda)['total'];
$adaTugas = $totalSemua > 0 ? 'Ada' : 'Tidak Ada';

/* 2. Total tugas di tanggal terdekat */
$qTotal = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total
    FROM tugas
    WHERE id_pengguna = '$sesi_id'
      AND tanggal_deadline = (
          SELECT tanggal_deadline
          FROM tugas
          WHERE id_pengguna = '$sesi_id'
          ORDER BY ABS(DATEDIFF(tanggal_deadline, '$tanggalInput')) ASC
          LIMIT 1
      )
");
$totalTugasTerdekat = mysqli_fetch_assoc($qTotal)['total'];

/* 3. Hitung sisa hari ke deadline terdekat */
$qTerdekat = mysqli_query($koneksi, "
    SELECT tanggal_deadline
    FROM tugas
    WHERE id_pengguna = '$sesi_id'
    ORDER BY ABS(DATEDIFF(tanggal_deadline, '$tanggalInput')) ASC
    LIMIT 1
");
$hariTerdekat = mysqli_fetch_assoc($qTerdekat)['tanggal_deadline'] ?? null;

$sisaHari = '-';
if ($hariTerdekat) {
    $sisaHari = (strtotime($hariTerdekat) - strtotime(date('Y-m-d'))) / 86400;
}

/* =========================
   DATA TABEL TUGAS
========================= */
$sql = "
    SELECT
        nama_tugas,
        deskripsi_tugas,
        tanggal_deadline,
        waktu_deadline,
        keterangan,
        status_tugas
    FROM tugas
    WHERE id_pengguna = '$sesi_id'
    ORDER BY ABS(DATEDIFF(tanggal_deadline, '$tanggalInput')) ASC,
             waktu_deadline ASC
";
$data = mysqli_query($koneksi, $sql);
?>

<div class="page-heading">
    <h3>Hari Ini / Tanggal Terdekat</h3>
    <p class="text-muted">Pencarian tugas berdasarkan tanggal deadline terdekat</p>

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

        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Apakah Ada Tugas</h6>
                    <h5><?= $adaTugas ?></h5>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Total Tugas (Tanggal Terdekat)</h6>
                    <h5><?= $totalTugasTerdekat ?></h5>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Menuju Deadline Terdekat</h6>
                    <h5><?= is_numeric($sisaHari) ? $sisaHari . ' Hari' : '-' ?></h5>
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
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Keterangan</th>
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
                            <td><?= $row['tanggal_deadline'] ?></td>
                            <td><?= $row['waktu_deadline'] ?></td>
                            <td><?= $row['keterangan'] ?: '-' ?></td>
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