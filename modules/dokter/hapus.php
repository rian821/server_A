<?php
/**
 * Modul Dokter - Hapus Data
 * Role: Admin
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role(['admin']);

$id = (int)($_GET['id'] ?? 0);
if ($id) {
    $res = call_api('DELETE', '/dokter/index.php', ['id_dokter' => $id]);
    if ($res['status']) {
        set_flash('success', 'Dokter berhasil dihapus.');
    } else {
        set_flash('error', $res['message'] ?? 'Gagal menghapus dokter.');
    }
}

header('Location: ' . BASE_URL . '/modules/dokter/index.php');
exit;
