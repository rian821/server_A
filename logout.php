<?php
/**
 * Logout Pengguna
 * Memanggil API Server B untuk menghapus token, lalu membersihkan sesi Server A
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/helpers/api.php';

if (!empty($_SESSION['token'])) {
    call_api('POST', '/auth/logout.php');
}

// Hancurkan semua sesi di Server A
session_unset();
session_destroy();

session_start();
$_SESSION['flash_success'] = 'Anda telah berhasil logout.';
header('Location: ' . BASE_URL . '/login.php');
exit;
