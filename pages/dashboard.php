<?php
require_once 'functions/config.php';

$sesi_id   = $_SESSION['sesi_id'];
$sesi_nama = $_SESSION['sesi_nama'];

/* =========================
   TANGGAL HARI INI
========================= */
$tanggalHariIni = date('Y-m-d');

/* =========================
   STATISTIK RINGKAS
========================= */
$qTotalTugas = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total
    FROM tugas
    WHERE id_pengguna = '$sesi_id'
");
$totalTugas = mysqli_fetch_assoc($qTotalTugas)['total'];

$qSelesai = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total
    FROM tugas
    WHERE id_pengguna = '$sesi_id'
      AND status_tugas = 'selesai'
");
$totalSelesai = mysqli_fetch_assoc($qSelesai)['total'];

$qBelum = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total
    FROM tugas
    WHERE id_pengguna = '$sesi_id'
      AND status_tugas = 'belum'
");
$totalBelum = mysqli_fetch_assoc($qBelum)['total'];

/* =========================
   TUGAS HARI INI
========================= */
$qTugasHariIni = mysqli_query($koneksi, "
    SELECT
        nama_tugas,
        deskripsi_tugas,
        waktu_deadline,
        keterangan,
        status_tugas
    FROM tugas
    WHERE id_pengguna = '$sesi_id'
      AND tanggal_deadline = '$tanggalHariIni'
    ORDER BY waktu_deadline ASC
");
$totalHariIni = mysqli_num_rows($qTugasHariIni);

/* =========================
   JADWAL TERDEKAT (3 DATA)
========================= */
$qTerdekat = mysqli_query($koneksi, "
    SELECT
        nama_tugas,
        tanggal_deadline,
        waktu_deadline
    FROM tugas
    WHERE id_pengguna = '$sesi_id'
      AND tanggal_deadline >= '$tanggalHariIni'
    ORDER BY tanggal_deadline ASC, waktu_deadline ASC
    LIMIT 3
");
?>

<div class="page-heading">
    <h3>Dashboard</h3>
    <p class="text-muted">
        Selamat datang, <b><?= htmlspecialchars($sesi_nama); ?></b>.
        Berikut ringkasan aktivitas dan jadwal Anda.
    </p>
</div>

<!-- =========================
     CARD STATISTIK
========================= -->
<section class="row mb-4">

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted">Total Tugas</h6>
                <h3><?= $totalTugas; ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted">Tugas Selesai</h6>
                <h3 class="text-success"><?= $totalSelesai; ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted">Belum Selesai</h6>
                <h3 class="text-danger"><?= $totalBelum; ?></h3>
            </div>
        </div>
    </div>

</section>

<!-- =========================
     HARI INI
========================= -->
<section class="section mb-4">
    <div class="card">
        <div class="card-body">
            <h5>📅 Hari Ini</h5>

            <?php if ($totalHariIni == 0): ?>
                <span class="badge bg-info">Tidak Ada Tugas</span>
            <?php else: ?>
                <span class="badge bg-success">Ada Tugas</span>
                <p class="mt-2">Total tugas hari ini: <b><?= $totalHariIni; ?></b></p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- =========================
     DAFTAR TUGAS HARI INI
========================= -->
<section class="section mb-4">
    <div class="card">
        <div class="card-header">
            <h5>📌 Tugas Hari Ini</h5>
        </div>
        <div class="card-body">

            <?php if ($totalHariIni == 0): ?>
                <p class="text-muted">Tidak ada tugas hari ini.</p>
            <?php else: ?>
                <ul class="list-group">
                    <?php while ($t = mysqli_fetch_assoc($qTugasHariIni)): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div>
                                <b><?= htmlspecialchars($t['nama_tugas']); ?></b><br>
                                <small class="text-muted">
                                    ⏰ <?= $t['waktu_deadline']; ?>
                                </small><br>

                                <?php if (!empty($t['keterangan'])): ?>
                                    <span class="badge bg-secondary">
                                        <?= htmlspecialchars($t['keterangan']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <span class="badge <?= $t['status_tugas'] == 'selesai' ? 'bg-success' : 'bg-danger'; ?>">
                                <?= ucfirst($t['status_tugas']); ?>
                            </span>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php endif; ?>

        </div>
    </div>
</section>

<!-- =========================
     JADWAL TERDEKAT
========================= -->
<section class="section">
    <div class="card">
        <div class="card-header">
            <h5>⏳ Jadwal Terdekat</h5>
        </div>
        <div class="card-body">

            <?php if (mysqli_num_rows($qTerdekat) == 0): ?>
                <p class="text-muted">Belum ada jadwal mendatang.</p>
            <?php else: ?>
                <ul class="list-group">
                    <?php while ($j = mysqli_fetch_assoc($qTerdekat)): ?>
                        <li class="list-group-item">
                            <b><?= htmlspecialchars($j['nama_tugas']); ?></b><br>
                            <small class="text-muted">
                                📅 <?= $j['tanggal_deadline']; ?> |
                                ⏰ <?= $j['waktu_deadline']; ?>
                            </small>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php endif; ?>

        </div>
    </div>
</section>