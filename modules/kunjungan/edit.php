<?php
/**
 * Modul Kunjungan - Edit Data Kunjungan
 * Role: Admin
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role(['admin']);

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: ' . BASE_URL . '/modules/kunjungan/index.php');
    exit;
}

$page_title = 'Edit Kunjungan';
$error_msg = null;

// Ambil detail kunjungan
$detail_res = call_api('GET', '/kunjungan/index.php?id=' . $id);
if (!$detail_res['status'] || empty($detail_res['data'])) {
    set_flash('error', 'Data kunjungan tidak ditemukan.');
    header('Location: ' . BASE_URL . '/modules/kunjungan/index.php');
    exit;
}
$k = $detail_res['data'];

// Ambil poliklinik, dokter, pasien
$poli_res = call_api('GET', '/poliklinik/index.php');
$poliklinik = $poli_res['status'] ? ($poli_res['data'] ?? []) : [];

$dok_res = call_api('GET', '/dokter/index.php');
$dokter = $dok_res['status'] ? ($dok_res['data'] ?? []) : [];

$pas_res = call_api('GET', '/pasien/index.php');
$pasien_list = $pas_res['status'] ? ($pas_res['data'] ?? []) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pasien          = (int)($_POST['id_pasien'] ?? 0);
    $id_poli            = (int)($_POST['id_poli'] ?? 0);
    $id_dokter          = (int)($_POST['id_dokter'] ?? 0);
    $tanggal_kunjungan  = trim($_POST['tanggal_kunjungan'] ?? '');
    $keluhan            = trim($_POST['keluhan'] ?? '');
    $diagnosis          = trim($_POST['diagnosis'] ?? '');
    $tindakan           = trim($_POST['tindakan'] ?? '');
    $status             = trim($_POST['status'] ?? 'menunggu');

    if (!$id_pasien || !$id_poli || !$id_dokter || empty($tanggal_kunjungan)) {
        $error_msg = 'Semua field wajib diisi.';
    } else {
        $res = call_api('PUT', '/kunjungan/index.php', [
            'id_kunjungan'      => $id,
            'id_pasien'         => $id_pasien,
            'id_poli'           => $id_poli,
            'id_dokter'         => $id_dokter,
            'tanggal_kunjungan' => $tanggal_kunjungan,
            'keluhan'           => $keluhan,
            'diagnosis'         => $diagnosis,
            'tindakan'          => $tindakan,
            'status'            => $status
        ]);

        if ($res['status']) {
            set_flash('success', 'Data kunjungan berhasil diperbarui.');
            header('Location: ' . BASE_URL . '/modules/kunjungan/index.php');
            exit;
        } else {
            $error_msg = $res['message'] ?? 'Gagal memperbarui kunjungan.';
        }
    }
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="app-main">
    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>

    <main class="app-content">
        <div class="mb-4">
            <a href="<?= BASE_URL ?>/modules/kunjungan/index.php" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-1 mb-2">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar Kunjungan
            </a>
            <h4 class="fw-bold mb-0">Edit Kunjungan Pasien</h4>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
                    <?php if ($error_msg): ?>
                        <div class="alert alert-danger py-2 small mb-3"><?= escape($error_msg) ?></div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Pasien <span class="text-danger">*</span></label>
                            <select name="id_pasien" class="form-select" required>
                                <?php foreach ($pasien_list as $ps): ?>
                                    <option value="<?= $ps['id_pasien'] ?>" <?= (int)$k['id_pasien'] === (int)$ps['id_pasien'] ? 'selected' : '' ?>>
                                        [<?= escape($ps['no_rm']) ?>] <?= escape($ps['nama_pasien']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Poliklinik Tujuan <span class="text-danger">*</span></label>
                                <select name="id_poli" class="form-select" required>
                                    <?php foreach ($poliklinik as $p): ?>
                                        <option value="<?= $p['id_poli'] ?>" <?= (int)$k['id_poli'] === (int)$p['id_poli'] ? 'selected' : '' ?>>
                                            <?= escape($p['nama_poli']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Dokter Spesialis <span class="text-danger">*</span></label>
                                <select name="id_dokter" class="form-select" required>
                                    <?php foreach ($dokter as $d): ?>
                                        <option value="<?= $d['id_dokter'] ?>" <?= (int)$k['id_dokter'] === (int)$d['id_dokter'] ? 'selected' : '' ?>>
                                            <?= escape($d['nama_dokter']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Tanggal Kunjungan <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_kunjungan" class="form-control" value="<?= escape($k['tanggal_kunjungan']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Status Kunjungan</label>
                                <select name="status" class="form-select">
                                    <option value="menunggu" <?= $k['status'] === 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                                    <option value="diperiksa" <?= $k['status'] === 'diperiksa' ? 'selected' : '' ?>>Diperiksa</option>
                                    <option value="selesai" <?= $k['status'] === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                    <option value="batal" <?= $k['status'] === 'batal' ? 'selected' : '' ?>>Batal</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Keluhan</label>
                            <textarea name="keluhan" class="form-control" rows="2"><?= escape($k['keluhan'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Diagnosis</label>
                            <textarea name="diagnosis" class="form-control" rows="2"><?= escape($k['diagnosis'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold small">Tindakan / Resep Obat</label>
                            <textarea name="tindakan" class="form-control" rows="2"><?= escape($k['tindakan'] ?? '') ?></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4 fw-semibold">
                                <i class="bi bi-save me-1"></i> Perbarui Kunjungan
                            </button>
                            <a href="<?= BASE_URL ?>/modules/kunjungan/index.php" class="btn btn-light border px-4">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
