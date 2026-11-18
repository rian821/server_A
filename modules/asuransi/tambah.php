<?php
/**
 * Modul Asuransi - Tambah Data
 * Role: Admin
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role(['admin']);

$page_title = 'Tambah Asuransi';
$error_msg = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_asuransi = trim($_POST['nama_asuransi'] ?? '');
    $jenis         = trim($_POST['jenis'] ?? 'Swasta');
    $no_kontak     = trim($_POST['no_kontak'] ?? '');
    $keterangan    = trim($_POST['keterangan'] ?? '');

    if (empty($nama_asuransi)) {
        $error_msg = 'Nama asuransi wajib diisi.';
    } else {
        $res = call_api('POST', '/asuransi/index.php', [
            'nama_asuransi' => $nama_asuransi,
            'jenis'         => $jenis,
            'no_kontak'     => $no_kontak,
            'keterangan'    => $keterangan
        ]);

        if ($res['status']) {
            set_flash('success', 'Asuransi berhasil ditambahkan.');
            header('Location: ' . BASE_URL . '/modules/asuransi/index.php');
            exit;
        } else {
            $error_msg = $res['message'] ?? 'Gagal menambahkan data asuransi.';
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
            <h4 class="fw-bold mb-0">Tambah Mitra Asuransi Baru</h4>
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
                            <input type="text" name="nama_asuransi" class="form-control" placeholder="Contoh: BPJS Kesehatan, Prudential" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Jenis Lembaga</label>
                            <select name="jenis" class="form-select">
                                <option value="Pemerintah">Pemerintah</option>
                                <option value="Swasta" selected>Swasta</option>
                                <option value="BUMN">BUMN</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">No. Kontak / Call Center</label>
                            <input type="text" name="no_kontak" class="form-control" placeholder="Contoh: 165 atau 021-xxxx">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold small">Keterangan / Ketentuan Klaim</label>
                            <textarea name="keterangan" class="form-control" rows="3" placeholder="Informasi kelas jaminan, cashless, dll..."></textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4 fw-semibold">
                                <i class="bi bi-save me-1"></i> Simpan Asuransi
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
