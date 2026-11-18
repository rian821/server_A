<?php
/**
 * Modul Asuransi - Edit Data
 * Role: Admin
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role(['admin']);

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: ' . BASE_URL . '/modules/asuransi/index.php');
    exit;
}

$page_title = 'Edit Asuransi';
$error_msg = null;

// Ambil detail asuransi
$detail_res = call_api('GET', '/asuransi/index.php?id=' . $id);
if (!$detail_res['status'] || empty($detail_res['data'])) {
    set_flash('error', 'Data asuransi tidak ditemukan.');
    header('Location: ' . BASE_URL . '/modules/asuransi/index.php');
    exit;
}
$asu = $detail_res['data'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_asuransi = trim($_POST['nama_asuransi'] ?? '');
    $jenis         = trim($_POST['jenis'] ?? 'Swasta');
    $no_kontak     = trim($_POST['no_kontak'] ?? '');
    $keterangan    = trim($_POST['keterangan'] ?? '');

    if (empty($nama_asuransi)) {
        $error_msg = 'Nama asuransi wajib diisi.';
    } else {
        $res = call_api('PUT', '/asuransi/index.php', [
            'id_asuransi'   => $id,
            'nama_asuransi' => $nama_asuransi,
            'jenis'         => $jenis,
            'no_kontak'     => $no_kontak,
            'keterangan'    => $keterangan
        ]);

        if ($res['status']) {
            set_flash('success', 'Asuransi berhasil diperbarui.');
            header('Location: ' . BASE_URL . '/modules/asuransi/index.php');
            exit;
        } else {
            $error_msg = $res['message'] ?? 'Gagal memperbarui data asuransi.';
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
            <a href="<?= BASE_URL ?>/modules/asuransi/index.php" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-1 mb-2">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar Asuransi
            </a>
            <h4 class="fw-bold mb-0">Edit Data Asuransi</h4>
        </div>

        <div class="row">
            <div class="col-lg-7">
                <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
                    <?php if ($error_msg): ?>
                        <div class="alert alert-danger py-2 small mb-3"><?= escape($error_msg) ?></div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Nama Asuransi <span class="text-danger">*</span></label>
                            <input type="text" name="nama_asuransi" class="form-control" value="<?= escape($asu['nama_asuransi']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Jenis Lembaga</label>
                            <select name="jenis" class="form-select">
                                <option value="Pemerintah" <?= ($asu['jenis'] ?? '') === 'Pemerintah' ? 'selected' : '' ?>>Pemerintah</option>
                                <option value="Swasta" <?= ($asu['jenis'] ?? '') === 'Swasta' ? 'selected' : '' ?>>Swasta</option>
                                <option value="BUMN" <?= ($asu['jenis'] ?? '') === 'BUMN' ? 'selected' : '' ?>>BUMN</option>
                                <option value="Lainnya" <?= ($asu['jenis'] ?? '') === 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">No. Kontak / Call Center</label>
                            <input type="text" name="no_kontak" class="form-control" value="<?= escape($asu['no_kontak'] ?? '') ?>">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold small">Keterangan / Ketentuan Klaim</label>
                            <textarea name="keterangan" class="form-control" rows="3"><?= escape($asu['keterangan'] ?? '') ?></textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4 fw-semibold">
                                <i class="bi bi-save me-1"></i> Perbarui Asuransi
                            </button>
                            <a href="<?= BASE_URL ?>/modules/asuransi/index.php" class="btn btn-light border px-4">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
