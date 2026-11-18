<?php
/**
 * Modul Poliklinik - Hapus Data
 * Role: Admin
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role(['admin']);

$id = (int)($_GET['id'] ?? 0);
if ($id) {
    $res = call_api('DELETE', '/poliklinik/index.php', ['id_poli' => $id]);
    if ($res['status']) {
        set_flash('success', 'Poliklinik berhasil dihapus.');
    } else {
        set_flash('error', $res['message'] ?? 'Gagal menghapus poliklinik.');
    }
}

header('Location: ' . BASE_URL . '/modules/poliklinik/index.php');
exit;
