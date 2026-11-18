<?php
/**
 * Modul Pengguna - Hapus Data
 * Role: Admin
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role(['admin']);

$id = (int)($_GET['id'] ?? 0);
if ($id) {
    $res = call_api('DELETE', '/pengguna/index.php', ['id_pengguna' => $id]);
    if ($res['status']) {
        set_flash('success', 'Akun pengguna berhasil dihapus.');
    } else {
        set_flash('error', $res['message'] ?? 'Gagal menghapus pengguna.');
    }
}

header('Location: ' . BASE_URL . '/modules/pengguna/index.php');
exit;
