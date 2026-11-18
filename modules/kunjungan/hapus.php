<?php
/**
 * Modul Kunjungan - Hapus Kunjungan
 * Role: Admin
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role(['admin']);

$id = (int)($_GET['id'] ?? 0);
if ($id) {
    $res = call_api('DELETE', '/kunjungan/index.php', ['id_kunjungan' => $id]);
    if ($res['status']) {
        set_flash('success', 'Data kunjungan berhasil dihapus.');
    } else {
        set_flash('error', $res['message'] ?? 'Gagal menghapus kunjungan.');
    }
}

header('Location: ' . BASE_URL . '/modules/kunjungan/index.php');
exit;
