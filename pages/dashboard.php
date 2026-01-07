<?php
require_once 'functions/config.php';

$sesi_id   = $_SESSION['sesi_id'];
$sesi_nama = $_SESSION['sesi_nama'];

/* =========================
   TANGGAL & HARI HARI INI
========================= */
$tanggalHariIni = date('Y-m-d');

/* =========================
   DATA HARI INI
========================= */
$qHariIni = mysqli_query($koneksi, "
    SELECT id_hari, nama_hari, tanggal
    FROM hari
    WHERE tanggal = '$tanggalHariIni'
    LIMIT 1
");
$hariIni = mysqli_fetch_assoc($qHariIni);
$idHariIni = $hariIni['id_hari'] ?? null;

/* =========================
   STATISTIK RINGKAS
========================= */
$qTotalTugas = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total FROM tugas
    WHERE id_pengguna = '$sesi_id'
");
$totalTugas = mysqli_fetch_assoc($qTotalTugas)['total'];

$qSelesai = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total FROM tugas
    WHERE id_pengguna = '$sesi_id'
      AND status_tugas = 'selesai'
");
$totalSelesai = mysqli_fetch_assoc($qSelesai)['total'];

$qBelum = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total FROM tugas
    WHERE id_pengguna = '$sesi_id'
      AND status_tugas = 'belum'
");
$totalBelum = mysqli_fetch_assoc($qBelum)['total'];

/* =========================
   TUGAS HARI INI
========================= */
$qTugasHariIni = [];
$totalHariIni = 0;

if ($idHariIni) {
    $qTugasHariIni = mysqli_query($koneksi, "
        SELECT
            t.nama_tugas,
            t.deskripsi_tugas,
            t.status_tugas,
            w.jam_mulai,
            w.jam_selesai,
            k.jenis_pekerjaan
        FROM tugas t
        JOIN waktu w ON t.id_waktu = w.id_waktu
        LEFT JOIN keterangan k ON t.id_keterangan = k.id_keterangan
        WHERE t.id_pengguna = '$sesi_id'
          AND t.id_hari = '$idHariIni'
        ORDER BY w.jam_mulai ASC
    ");
    $totalHariIni = mysqli_num_rows($qTugasHariIni);
}

/* =========================
   JADWAL TERDEKAT (3 DATA)
========================= */
$qTerdekat = mysqli_query($koneksi, "
    SELECT
        t.nama_tugas,
        h.nama_hari,
        h.tanggal,
        w.jam_mulai,
        w.jam_selesai
    FROM tugas t
    JOIN hari h ON t.id_hari = h.id_hari
    JOIN waktu w ON t.id_waktu = w.id_waktu
    WHERE t.id_pengguna = '$sesi_id'
      AND h.tanggal >= '$tanggalHariIni'
    ORDER BY h.tanggal ASC, w.jam_mulai ASC
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

            <?php if (!$hariIni): ?>
                <p class="text-muted">Data hari belum tersedia.</p>
            <?php elseif ($totalHariIni == 0): ?>
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
                                    <?= $t['jam_mulai']; ?> - <?= $t['jam_selesai']; ?>
                                </small><br>

                                <?php if ($t['jenis_pekerjaan']): ?>
                                    <span class="badge bg-secondary">
                                        <?= $t['jenis_pekerjaan']; ?>
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
                                <?= $j['nama_hari']; ?> (<?= $j['tanggal']; ?>),
                                <?= $j['jam_mulai']; ?> - <?= $j['jam_selesai']; ?>
                            </small>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php endif; ?>

        </div>
    </div>
</section>