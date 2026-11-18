<?php
/**
 * Modul Poliklinik - Edit Data
 * Role: Admin
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role(['admin']);

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: ' . BASE_URL . '/modules/poliklinik/index.php');
    exit;
}

$page_title = 'Edit Poliklinik';
$error_msg = null;

// Ambil detail poliklinik
$detail_res = call_api('GET', '/poliklinik/index.php?id=' . $id);
if (!$detail_res['status'] || empty($detail_res['data'])) {
    set_flash('error', 'Data poliklinik tidak ditemukan.');
    header('Location: ' . BASE_URL . '/modules/poliklinik/index.php');
    exit;
}
$poli = $detail_res['data'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_poli  = trim($_POST['nama_poli'] ?? '');
    $lokasi     = trim($_POST['lokasi'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');

    if (empty($nama_poli)) {
        $error_msg = 'Nama poliklinik wajib diisi.';
    } else {
        $res = call_api('PUT', '/poliklinik/index.php', [
            'id_poli'    => $id,
            'nama_poli'  => $nama_poli,
            'lokasi'     => $lokasi,
            'keterangan' => $keterangan
        ]);

        if ($res['status']) {
            set_flash('success', 'Poliklinik berhasil diperbarui.');
            header('Location: ' . BASE_URL . '/modules/poliklinik/index.php');
            exit;
        } else {
            $error_msg = $res['message'] ?? 'Gagal memperbarui data poliklinik.';
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
            <a href="<?= BASE_URL ?>/modules/poliklinik/index.php" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-1 mb-2">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar Poliklinik
            </a>
            <h4 class="fw-bold mb-0">Edit Data Poliklinik</h4>
        </div>

        <div class="row">
            <div class="col-lg-7">
                <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
                    <?php if ($error_msg): ?>
                        <div class="alert alert-danger py-2 small mb-3"><?= escape($error_msg) ?></div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Nama Poliklinik <span class="text-danger">*</span></label>
                            <input type="text" name="nama_poli" class="form-control" value="<?= escape($poli['nama_poli']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Lokasi Gedung / Ruangan</label>
                            <input type="text" name="lokasi" class="form-control" value="<?= escape($poli['lokasi'] ?? '') ?>">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold small">Keterangan Layanan</label>
                            <textarea name="keterangan" class="form-control" rows="3"><?= escape($poli['keterangan'] ?? '') ?></textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4 fw-semibold">
                                <i class="bi bi-save me-1"></i> Perbarui Poliklinik
                            </button>
                            <a href="<?= BASE_URL ?>/modules/poliklinik/index.php" class="btn btn-light border px-4">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
