<?php
/**
 * Modul Dokter - Edit Data
 * Role: Admin
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role(['admin']);

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: ' . BASE_URL . '/modules/dokter/index.php');
    exit;
}

$page_title = 'Edit Dokter';
$error_msg = null;

// Ambil detail dokter
$detail_res = call_api('GET', '/dokter/index.php?id=' . $id);
if (!$detail_res['status'] || empty($detail_res['data'])) {
    set_flash('error', 'Data dokter tidak ditemukan.');
    header('Location: ' . BASE_URL . '/modules/dokter/index.php');
    exit;
}
$dok = $detail_res['data'];

// Ambil daftar poliklinik
$poli_res = call_api('GET', '/poliklinik/index.php');
$poliklinik = $poli_res['status'] ? ($poli_res['data'] ?? []) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_dokter  = trim($_POST['nama_dokter'] ?? '');
    $id_poli      = (int)($_POST['id_poli'] ?? 0);
    $spesialisasi = trim($_POST['spesialisasi'] ?? '');
    $no_str       = trim($_POST['no_str'] ?? '');
    $no_hp        = trim($_POST['no_hp'] ?? '');

    if (empty($nama_dokter) || !$id_poli) {
        $error_msg = 'Nama dokter dan poliklinik wajib diisi.';
    } else {
        $res = call_api('PUT', '/dokter/index.php', [
            'id_dokter'    => $id,
            'nama_dokter'  => $nama_dokter,
            'id_poli'      => $id_poli,
            'spesialisasi' => $spesialisasi,
            'no_str'       => $no_str,
            'no_hp'        => $no_hp
        ]);

        if ($res['status']) {
            set_flash('success', 'Data dokter berhasil diperbarui.');
            header('Location: ' . BASE_URL . '/modules/dokter/index.php');
            exit;
        } else {
            $error_msg = $res['message'] ?? 'Gagal memperbarui data dokter.';
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
            <a href="<?= BASE_URL ?>/modules/dokter/index.php" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-1 mb-2">
                <i class="bi bi-arrow-left"></i> Kembali ke Data Dokter
            </a>
            <h4 class="fw-bold mb-0">Edit Data Dokter</h4>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
                    <?php if ($error_msg): ?>
                        <div class="alert alert-danger py-2 small mb-3"><?= escape($error_msg) ?></div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <div class="row g-3 mb-3">
                            <div class="col-md-7">
                                <label class="form-label fw-semibold small">Nama Lengkap Dokter <span class="text-danger">*</span></label>
                                <input type="text" name="nama_dokter" class="form-control" value="<?= escape($dok['nama_dokter']) ?>" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-semibold small">Poliklinik Bertugas <span class="text-danger">*</span></label>
                                <select name="id_poli" class="form-select" required>
                                    <?php foreach ($poliklinik as $p): ?>
                                        <option value="<?= $p['id_poli'] ?>" <?= (int)$dok['id_poli'] === (int)$p['id_poli'] ? 'selected' : '' ?>>
                                            <?= escape($p['nama_poli']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Spesialisasi</label>
                                <input type="text" name="spesialisasi" class="form-control" value="<?= escape($dok['spesialisasi'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">No. STR</label>
                                <input type="text" name="no_str" class="form-control" value="<?= escape($dok['no_str'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">No. Telepon / HP</label>
                                <input type="text" name="no_hp" class="form-control" value="<?= escape($dok['no_hp'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4 fw-semibold">
                                <i class="bi bi-save me-1"></i> Perbarui Dokter
                            </button>
                            <a href="<?= BASE_URL ?>/modules/dokter/index.php" class="btn btn-light border px-4">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
