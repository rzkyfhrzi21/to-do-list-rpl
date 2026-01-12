<?php
require_once 'functions/config.php';

$sesi_id = $_SESSION['sesi_id'];

/* =========================
   WAKTU SEKARANG
========================= */
$tanggalHariIni = date('Y-m-d');
$jamSekarang    = date('H:i:s');

/* =========================
   QUERY PENGINGAT (FINAL)
   - Deadline hari ini
   - Belum selesai
   - Jam deadline > sekarang
========================= */
$qPengingat = mysqli_query($koneksi, "
    SELECT
        id_tugas,
        nama_tugas,
        deskripsi_tugas,
        tanggal_deadline,
        waktu_deadline,
        TIMESTAMPDIFF(
            MINUTE,
            NOW(),
            CONCAT(tanggal_deadline, ' ', waktu_deadline)
        ) AS sisa_menit
    FROM tugas
    WHERE id_pengguna = '$sesi_id'
      AND status_tugas = 'belum'
      AND tanggal_deadline = '$tanggalHariIni'
      AND waktu_deadline > '$jamSekarang'
    ORDER BY waktu_deadline ASC
");
?>

<?php if (mysqli_num_rows($qPengingat) > 0): ?>
    <?php while ($p = mysqli_fetch_assoc($qPengingat)): ?>

        <?php
        /* =========================
           WARNA ALERT
        ========================= */
        if ($p['sisa_menit'] <= 30) {
            $alertClass = 'danger';
        } elseif ($p['sisa_menit'] <= 60) {
            $alertClass = 'warning';
        } else {
            $alertClass = 'info';
        }
        ?>

        <div class="card-body mb-3 notif-card"
            data-notif-id="<?= $p['id_tugas']; ?>">

            <div class="alert alert-<?= $alertClass; ?> mb-0 position-relative">

                <!-- TOMBOL CLOSE -->
                <button type="button"
                    class="btn-close position-absolute top-0 end-0 m-2 notif-close"
                    aria-label="Close">
                </button>

                <h5 class="alert-heading mb-1">
                    ⏰ Pengingat Tugas Hari Ini
                </h5>

                <p class="mb-2">
                    Tugas <b><?= htmlspecialchars($p['nama_tugas']); ?></b>
                    masih belum selesai.
                </p>

                <hr class="my-2">

                <p class="mb-1">
                    <b>📅 Deadline:</b>
                    <?= date('d M Y', strtotime($p['tanggal_deadline'])); ?>
                </p>

                <p class="mb-1">
                    <b>🕒 Waktu:</b>
                    <?= date('H:i', strtotime($p['waktu_deadline'])); ?>
                </p>

                <p class="mb-0">
                    <b>⏳ Sisa Waktu:</b>
                    <?= $p['sisa_menit']; ?> menit lagi
                </p>

            </div>
        </div>

    <?php endwhile; ?>
<?php endif; ?>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        const STORAGE_KEY = "hidden_pengingat_hari_ini";
        let hiddenNotifs = JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];

        // sembunyikan notif yg sudah ditutup
        document.querySelectorAll(".notif-card").forEach(card => {
            const id = card.dataset.notifId;
            if (hiddenNotifs.includes(id)) {
                card.remove();
            }
        });

        // tombol close
        document.addEventListener("click", function(e) {
            if (e.target.classList.contains("notif-close")) {

                const card = e.target.closest(".notif-card");
                const id = card.dataset.notifId;

                if (!hiddenNotifs.includes(id)) {
                    hiddenNotifs.push(id);
                    localStorage.setItem(STORAGE_KEY, JSON.stringify(hiddenNotifs));
                }

                card.remove();
            }
        });

    });
</script>