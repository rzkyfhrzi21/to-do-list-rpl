<?php
// functions/cron_reminder_h1.php

require_once __DIR__ . '/config.php';
date_default_timezone_set("Asia/Jakarta");

require_once __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/* =========================
   KONFIG EMAIL (GMAIL SMTP)
========================= */
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'amaralkhairi753@gmail.com');
define('SMTP_PASS', 'syesfxjwtaqcxtaa'); // ⚠️ pindahkan ke config/.env kalau bisa
define('FROM_EMAIL', 'amaralkhairi753@gmail.com');
define('FROM_NAME', 'To-Do List App');

define('TO_EMAIL', 'amaralkhairi753@gmail.com'); // kalau multi-user: harusnya per user
define('TO_NAME', 'Amar');

define('APP_URL', 'http://localhost/to-do-list'); // sesuaikan

/* =========================
   TANGGAL HARI INI (WIB)
========================= */
$hariIni = date('Y-m-d');

/* =========================
   AMBIL TUGAS H-1 (deadline = H+1)
========================= */
$sql = "
SELECT
  t.id_tugas,
  t.id_pengguna,
  t.nama_tugas,
  t.deskripsi_tugas,
  h.tanggal AS deadline_tanggal,
  w.jam_mulai,
  w.jam_selesai
FROM tugas t
JOIN hari h ON t.id_hari = h.id_hari
JOIN waktu w ON t.id_waktu = w.id_waktu
WHERE t.status_tugas = 'belum'
  AND h.tanggal = DATE_ADD('$hariIni', INTERVAL 1 DAY)
ORDER BY t.id_pengguna ASC, w.jam_mulai ASC
";

$res = mysqli_query($koneksi, $sql);
if (!$res) {
    echo "Query error: " . mysqli_error($koneksi);
    exit;
}

if (mysqli_num_rows($res) == 0) {
    echo "Tidak ada reminder untuk dikirim.\n";
    exit;
}

/* =========================
   FILTER: anti dobel (reminder_log)
========================= */
$tasksToSend = [];
while ($row = mysqli_fetch_assoc($res)) {
    $idTugas = (int) $row['id_tugas'];

    $cek = mysqli_query($koneksi, "
        SELECT 1 FROM reminder_log
        WHERE id_tugas = $idTugas
          AND reminder_date = '$hariIni'
        LIMIT 1
    ");

    if ($cek && mysqli_num_rows($cek) == 0) {
        $tasksToSend[] = $row;
    }
}

if (count($tasksToSend) == 0) {
    echo "Sudah pernah dikirim hari ini.\n";
    exit;
}

/* =========================
   HELPER: format tanggal indo
========================= */
function formatTanggalIndo($ymd)
{
    $bulan = [
        "01" => "Januari",
        "02" => "Februari",
        "03" => "Maret",
        "04" => "April",
        "05" => "Mei",
        "06" => "Juni",
        "07" => "Juli",
        "08" => "Agustus",
        "09" => "September",
        "10" => "Oktober",
        "11" => "November",
        "12" => "Desember"
    ];
    $y = substr($ymd, 0, 4);
    $m = substr($ymd, 5, 2);
    $d = substr($ymd, 8, 2);
    return ltrim($d, "0") . " " . ($bulan[$m] ?? $m) . " " . $y;
}

/* =========================
   FUNGSI KIRIM EMAIL
========================= */
function sendMail($toEmail, $toName, $subject, $htmlBody, $textBody)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;

        $mail->setFrom(FROM_EMAIL, FROM_NAME);
        $mail->addAddress($toEmail, $toName);
        $mail->addReplyTo(FROM_EMAIL, FROM_NAME);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $htmlBody;
        $mail->AltBody = $textBody;

        $mail->send();
        return [true, null];
    } catch (Exception $e) {
        return [false, $mail->ErrorInfo];
    }
}

/* =========================
   BUILD EMAIL
========================= */
$jumlah = count($tasksToSend);
$deadlineTanggal = $tasksToSend[0]['deadline_tanggal'];
$deadlineIndo = formatTanggalIndo($deadlineTanggal);

$subject = "⏳ Reminder: $jumlah tugas (Deadline $deadlineIndo)";

$rowsHtml = "";
foreach ($tasksToSend as $t) {
    $nama = htmlspecialchars($t['nama_tugas']);
    $desk = trim($t['deskripsi_tugas'] ?? '');
    $deskHtml = $desk !== '' ? nl2br(htmlspecialchars($desk)) : "<span style='color:#6b7280'>-</span>";
    $jam = htmlspecialchars($t['jam_mulai']) . " - " . htmlspecialchars($t['jam_selesai']);

    $rowsHtml .= "
      <tr>
        <td style='padding:0 0 12px 0;'>
          <div style='border:1px solid #e5e7eb;border-radius:14px;padding:14px;background:#ffffff;'>
            <div style='font-size:15px;font-weight:700;color:#111827;margin-bottom:6px;'>$nama</div>
            <div style='font-size:13px;line-height:1.5;color:#374151;margin-bottom:10px;'>$deskHtml</div>
            <div style='display:inline-block;background:#f3f4f6;color:#111827;border-radius:999px;padding:6px 10px;font-size:12px;'>
              ⏰ $jam
            </div>
          </div>
        </td>
      </tr>
    ";
}

$textBody =
    "Reminder Tugas\n"
    . "Deadline: $deadlineIndo\n"
    . "Jumlah tugas: $jumlah\n\n"
    . "Buka aplikasi: " . APP_URL . "\n\n"
    . "— " . FROM_NAME;

$previewText = "Deadline $deadlineIndo • $jumlah tugas";

$html = "
<!DOCTYPE html>
<html>
<head>
  <meta charset='utf-8'>
  <meta name='viewport' content='width=device-width, initial-scale=1'>
  <title>Reminder</title>
</head>
<body style='margin:0;padding:0;background:#f3f4f6;'>
  <div style='display:none;max-height:0;overflow:hidden;color:#f3f4f6;opacity:0;'>$previewText</div>

  <table role='presentation' cellpadding='0' cellspacing='0' width='100%' style='background:#f3f4f6;padding:24px 0;'>
    <tr>
      <td align='center'>
        <table role='presentation' cellpadding='0' cellspacing='0' width='600' style='max-width:600px;width:100%;'>

          <tr>
            <td style='padding:0 16px 12px 16px;'>
              <div style='font-family:Arial,sans-serif;font-size:14px;color:#6b7280;'>
                " . htmlspecialchars(FROM_NAME) . "
              </div>
            </td>
          </tr>

          <tr>
            <td style='padding:0 16px;'>
              <div style='background:#ffffff;border-radius:18px;overflow:hidden;border:1px solid #e5e7eb;'>

                <div style='background:#111827;color:#ffffff;padding:18px;font-family:Arial,sans-serif;'>
                  <div style='font-size:18px;font-weight:700;margin:0;'>Reminder Tugas</div>
                  <div style='font-size:13px;opacity:0.9;margin-top:6px;'>
                    Deadline: <b>$deadlineIndo</b> • Total: <b>$jumlah</b> tugas
                  </div>
                </div>

                <div style='padding:18px;font-family:Arial,sans-serif;'>
                  <div style='font-size:14px;color:#111827;line-height:1.6;'>
                    Halo <b>" . htmlspecialchars(TO_NAME) . "</b>,<br>
                    Berikut daftar tugas dengan <b>deadline $deadlineIndo</b> yang statusnya masih <b>belum</b>.
                  </div>

                  <div style='margin-top:16px;'>
                    <a href='" . htmlspecialchars(APP_URL) . "'
                       style='display:inline-block;background:#2563eb;color:#ffffff;text-decoration:none;
                              padding:12px 16px;border-radius:12px;font-weight:700;font-size:14px;'>
                      Buka To-Do List
                    </a>
                    <div style='font-size:12px;color:#6b7280;margin-top:10px;'>
                      Jika tombol tidak bisa diklik, copy link ini:<br>
                      <span style='word-break:break-all;'>" . htmlspecialchars(APP_URL) . "</span>
                    </div>
                  </div>

                  <div style='margin-top:18px;'>
                    <div style='font-size:14px;font-weight:700;color:#111827;margin-bottom:10px;'>
                      Daftar Tugas
                    </div>
                    <table role='presentation' cellpadding='0' cellspacing='0' width='100%'>
                      $rowsHtml
                    </table>
                  </div>

                </div>
              </div>
            </td>
          </tr>

          <tr>
            <td style='padding:14px 16px 0 16px;'>
              <div style='font-family:Arial,sans-serif;font-size:12px;color:#6b7280;line-height:1.6;text-align:center;'>
                Email ini dikirim otomatis oleh <b>" . htmlspecialchars(FROM_NAME) . "</b>.<br>
                Waktu kirim: " . date('d-m-Y H:i') . " WIB
              </div>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
";

/* =========================
   KIRIM EMAIL
========================= */
list($ok, $err) = sendMail(TO_EMAIL, TO_NAME, $subject, $html, $textBody);

if (!$ok) {
    echo "Gagal kirim email: $err\n";
    exit;
}

/* =========================
   INSERT NOTIF KE TABEL notifications
   (agar muncul di page tanpa trigger)
========================= */
foreach ($tasksToSend as $t) {
    $userId = (int) $t['id_pengguna'];
    $taskId = (int) $t['id_tugas'];

    $title = "Reminder H-1: " . $t['nama_tugas'];
    $message = "Deadline: $deadlineIndo | Jam: " . $t['jam_mulai'] . " - " . $t['jam_selesai'];

    $titleEsc = mysqli_real_escape_string($koneksi, $title);
    $msgEsc = mysqli_real_escape_string($koneksi, $message);

    // anti dobel notif per hari untuk task yang sama
    $cekNotif = mysqli_query($koneksi, "
        SELECT 1 FROM notifications
        WHERE user_id = $userId
          AND task_id = $taskId
          AND DATE(created_at) = '$hariIni'
        LIMIT 1
    ");

    if ($cekNotif && mysqli_num_rows($cekNotif) == 0) {
        mysqli_query($koneksi, "
            INSERT INTO notifications (user_id, task_id, title, message, is_read)
            VALUES ($userId, $taskId, '$titleEsc', '$msgEsc', 0)
        ");
    }
}

/* =========================
   SIMPAN LOG (ANTI DOBEL EMAIL)
========================= */
foreach ($tasksToSend as $t) {
    $idTugas = (int) $t['id_tugas'];
    mysqli_query($koneksi, "
        INSERT IGNORE INTO reminder_log (id_tugas, reminder_date)
        VALUES ($idTugas, '$hariIni')
    ");
}

echo "Selesai. Reminder terkirim (" . count($tasksToSend) . " tugas).\n";
