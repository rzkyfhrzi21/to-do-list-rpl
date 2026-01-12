<?php
// api/notifications.php

require_once __DIR__ . '/../functions/config.php';
date_default_timezone_set("Asia/Jakarta");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userId = (int) ($_SESSION['sesi_id'] ?? 0);

if (!$userId) {
    // kalau belum login, balikin kosong
    header('Content-Type: application/json');
    echo json_encode([]);
    exit;
}

$res = mysqli_query($koneksi, "
    SELECT id, task_id, title, message, created_at
    FROM notifications
    WHERE user_id = $userId
      AND is_read = 0
    ORDER BY created_at DESC
    LIMIT 20
");

$out = [];
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $out[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($out);
