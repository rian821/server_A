<?php
/**
 * Modul Pasien - Edit Data Pasien
 * Role: Admin
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role(['admin']);

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: ' . BASE_URL . '/modules/pasien/index.php');
    exit;
}

$page_title = 'Edit Pasien';
$error_msg = null;

// Ambil data pasien
$detail_res = call_api('GET', '/pasien/index.php?id=' . $id);
if (!$detail_res['status'] || empty($detail_res['data'])) {
    set_flash('error', 'Data pasien tidak ditemukan.');
    header('Location: ' . BASE_URL . '/modules/pasien/index.php');
    exit;
}
$p = $detail_res['data'];

// Ambil daftar asuransi
$asu_res = call_api('GET', '/asuransi/index.php');
$asuransi = $asu_res['status'] ? ($asu_res['data'] ?? []) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pasien       = trim($_POST['nama_pasien'] ?? '');
    $nik               = trim($_POST['nik'] ?? '');
    $jenis_kelamin     = trim($_POST['jenis_kelamin'] ?? 'L');
    $tanggal_lahir     = trim($_POST['tanggal_lahir'] ?? '');
    $alamat            = trim($_POST['alamat'] ?? '');
    $no_hp             = trim($_POST['no_hp'] ?? '');
    $id_asuransi       = !empty($_POST['id_asuransi']) ? (int)$_POST['id_asuransi'] : null;
    $no_kartu_asuransi = trim($_POST['no_kartu_asuransi'] ?? '');

    if (empty($nama_pasien)) {
        $error_msg = 'Nama pasien wajib diisi.';
    } else {
        $res = call_api('PUT', '/pasien/index.php', [
            'id_pasien'         => $id,
            'nama_pasien'       => $nama_pasien,
            'nik'               => $nik,
            'jenis_kelamin'     => $jenis_kelamin,
            'tanggal_lahir'     => !empty($tanggal_lahir) ? $tanggal_lahir : null,
            'alamat'            => $alamat,
            'no_hp'             => $no_hp,
            'id_asuransi'       => $id_asuransi,
            'no_kartu_asuransi' => $no_kartu_asuransi
        ]);

        if ($res['status']) {
            set_flash('success', 'Data pasien berhasil diperbarui.');
            header('Location: ' . BASE_URL . '/modules/pasien/index.php');
            exit;
        } else {
            $error_msg = $res['message'] ?? 'Gagal memperbarui data pasien.';
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
            <a href="<?= BASE_URL ?>/modules/pasien/index.php" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-1 mb-2">
                <i class="bi bi-arrow-left"></i> Kembali ke Data Pasien
            </a>
            <h4 class="fw-bold mb-0">Edit Data Pasien (<?= escape($p['no_rm']) ?>)</h4>
        </div>

        <div class="row">
            <div class="col-lg-9">
                <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
                    <?php if ($error_msg): ?>
                        <div class="alert alert-danger py-2 small mb-3"><?= escape($error_msg) ?></div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <div class="row g-3 mb-3">
                            <div class="col-md-7">
                                <label class="form-label fw-semibold small">Nama Lengkap Pasien <span class="text-danger">*</span></label>
                                <input type="text" name="nama_pasien" class="form-control" value="<?= escape($p['nama_pasien']) ?>" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-semibold small">NIK (Nomor Induk Kependudukan)</label>
                                <input type="text" name="nik" class="form-control" value="<?= escape($p['nik'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select name="jenis_kelamin" class="form-select" required>
                                    <option value="L" <?= $p['jenis_kelamin'] === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="P" <?= $p['jenis_kelamin'] === 'P' ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control" value="<?= escape($p['tanggal_lahir'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">No. HP / WhatsApp</label>
                                <input type="text" name="no_hp" class="form-control" value="<?= escape($p['no_hp'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control" rows="2"><?= escape($p['alamat'] ?? '') ?></textarea>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Jaminan Asuransi</label>
                                <select name="id_asuransi" class="form-select">
                                    <option value="">-- Umum / Bayar Mandiri --</option>
                                    <?php foreach ($asuransi as $a): ?>
                                        <option value="<?= $a['id_asuransi'] ?>" <?= (int)($p['id_asuransi'] ?? 0) === (int)$a['id_asuransi'] ? 'selected' : '' ?>>
                                            <?= escape($a['nama_asuransi']) ?> (<?= escape($a['jenis'] ?? '') ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">No. Kartu / Polis Asuransi</label>
                                <input type="text" name="no_kartu_asuransi" class="form-control" value="<?= escape($p['no_kartu_asuransi'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4 fw-semibold">
                                <i class="bi bi-save me-1"></i> Perbarui Pasien
                            </button>
                            <a href="<?= BASE_URL ?>/modules/pasien/index.php" class="btn btn-light border px-4">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
