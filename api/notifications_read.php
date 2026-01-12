<?php
// api/notifications_read.php

require_once __DIR__ . '/../functions/config.php';
date_default_timezone_set("Asia/Jakarta");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userId = (int) ($_SESSION['sesi_id'] ?? 0);
if (!$userId) {
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => 'NOT_LOGGED_IN']);
    exit;
}

$id = $_POST['id'] ?? '';
$id = trim($id);

if ($id === 'all') {
    mysqli_query($koneksi, "
        UPDATE notifications
        SET is_read = 1
        WHERE user_id = $userId AND is_read = 0
    ");

    header('Content-Type: application/json');
    echo json_encode(['ok' => true]);
    exit;
}

$notifId = (int) $id;
if ($notifId <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => 'INVALID_ID']);
    exit;
}

mysqli_query($koneksi, "
    UPDATE notifications
    SET is_read = 1
    WHERE id = $notifId AND user_id = $userId
    LIMIT 1
");

header('Content-Type: application/json');
echo json_encode(['ok' => true]);
