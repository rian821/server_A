<?php
/**
 * Modul Pasien - Hapus Data Pasien
 * Role: Admin
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role(['admin']);

$id = (int)($_GET['id'] ?? 0);
if ($id) {
    $res = call_api('DELETE', '/pasien/index.php', ['id_pasien' => $id]);
    if ($res['status']) {
        set_flash('success', 'Data pasien berhasil dihapus.');
    } else {
        set_flash('error', $res['message'] ?? 'Gagal menghapus pasien.');
    }
}

header('Location: ' . BASE_URL . '/modules/pasien/index.php');
exit;
