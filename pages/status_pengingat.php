<?php
require_once 'functions/config.php';

$sesi_id = $_SESSION['sesi_id'];

/* =========================
   WAKTU & HARI INI
========================= */
$tanggalHariIni = date('Y-m-d');
$jamSekarang    = date('H:i:s');

/* =========================
   QUERY PENGINGAT
   - Hari ini
   - Belum selesai
   - Jam sekarang < jam_selesai
========================= */
$qPengingat = mysqli_query($koneksi, "
    SELECT
        t.id_tugas,
        t.nama_tugas,
        t.deskripsi_tugas,
        h.nama_hari,
        h.tanggal,
        w.jam_mulai,
        w.jam_selesai,
        TIMESTAMPDIFF(
            MINUTE,
            NOW(),
            CONCAT(h.tanggal, ' ', w.jam_selesai)
        ) AS sisa_menit
    FROM tugas t
    JOIN hari h ON t.id_hari = h.id_hari
    JOIN waktu w ON t.id_waktu = w.id_waktu
    WHERE t.id_pengguna = '$sesi_id'
      AND t.status_tugas = 'belum'
      AND h.tanggal = '$tanggalHariIni'
      AND w.jam_selesai > '$jamSekarang'
    ORDER BY w.jam_selesai ASC
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
                    <b>📅 Hari:</b>
                    <?= $p['nama_hari']; ?> (<?= $p['tanggal']; ?>)
                </p>

                <p class="mb-1">
                    <b>🕒 Waktu:</b>
                    <?= $p['jam_mulai']; ?> - <?= $p['jam_selesai']; ?>
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

        /* =========================
           SEMBUNYIKAN YANG SUDAH DITUTUP
        ========================= */
        document.querySelectorAll(".notif-card").forEach(card => {
            const id = card.dataset.notifId;
            if (hiddenNotifs.includes(id)) {
                card.remove();
            }
        });

        /* =========================
           TOMBOL CLOSE
        ========================= */
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