<?php
/**
 * Modul Asuransi - Hapus Data
 * Role: Admin
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role(['admin']);

$id = (int)($_GET['id'] ?? 0);
if ($id) {
    $res = call_api('DELETE', '/asuransi/index.php', ['id_asuransi' => $id]);
    if ($res['status']) {
        set_flash('success', 'Asuransi berhasil dihapus.');
    } else {
        set_flash('error', $res['message'] ?? 'Gagal menghapus asuransi.');
    }
}

header('Location: ' . BASE_URL . '/modules/asuransi/index.php');
exit;
