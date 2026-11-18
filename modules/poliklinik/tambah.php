<?php
/**
 * Modul Poliklinik - Tambah Data
 * Role: Admin
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role(['admin']);

$page_title = 'Tambah Poliklinik';
$error_msg = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_poli  = trim($_POST['nama_poli'] ?? '');
    $lokasi     = trim($_POST['lokasi'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');

    if (empty($nama_poli)) {
        $error_msg = 'Nama poliklinik wajib diisi.';
    } else {
        $res = call_api('POST', '/poliklinik/index.php', [
            'nama_poli'  => $nama_poli,
            'lokasi'     => $lokasi,
            'keterangan' => $keterangan
        ]);

        if ($res['status']) {
            set_flash('success', 'Poliklinik baru berhasil ditambahkan.');
            header('Location: ' . BASE_URL . '/modules/poliklinik/index.php');
            exit;
        } else {
            $error_msg = $res['message'] ?? 'Gagal menambahkan data poliklinik.';
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
            <h4 class="fw-bold mb-0">Tambah Poliklinik Baru</h4>
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
                            <input type="text" name="nama_poli" class="form-control" placeholder="Contoh: Poli Mata, Poli THT" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Lokasi Gedung / Ruangan</label>
                            <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Gedung B Lantai 2">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold small">Keterangan Layanan</label>
                            <textarea name="keterangan" class="form-control" rows="3" placeholder="Deskripsi jenis layanan atau jam operasional..."></textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4 fw-semibold">
                                <i class="bi bi-save me-1"></i> Simpan Poliklinik
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
