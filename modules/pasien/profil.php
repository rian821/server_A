<?php
/**
 * Modul Pasien - Profil Pasien Mandiri
 * Role: Pasien
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role(['pasien']);

$user = current_user();
$id_pasien = (int)($user['id_pasien'] ?? 0);

$page_title = 'Profil Pasien';
$error_msg = null;

// Ambil detail pasien
$detail_res = call_api('GET', '/pasien/index.php?id=' . $id_pasien);
if (!$detail_res['status'] || empty($detail_res['data'])) {
    set_flash('error', 'Data profil pasien tidak ditemukan atau belum ditautkan ke akun Anda.');
    header('Location: ' . BASE_URL . '/dashboard.php');
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
            'id_pasien'         => $id_pasien,
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
            $_SESSION['user']['nama_lengkap'] = $nama_pasien;
            set_flash('success', 'Profil Anda berhasil diperbarui.');
            header('Location: ' . BASE_URL . '/modules/pasien/profil.php');
            exit;
        } else {
            $error_msg = $res['message'] ?? 'Gagal memperbarui profil.';
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
            <h4 class="fw-bold mb-1">Profil Pasien</h4>
            <p class="text-muted small mb-0">Informasi rekam medis dan data diri pasien</p>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-circle m-0" style="width:50px; height:50px; font-size:20px;">
                                <?= strtoupper(substr($p['nama_pasien'], 0, 1)) ?>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0"><?= escape($p['nama_pasien']) ?></h5>
                                <span class="badge bg-primary-subtle text-primary border border-primary">No. RM: <?= escape($p['no_rm']) ?></span>
                            </div>
                        </div>
                    </div>

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
                                <label class="form-label fw-semibold small">NIK</label>
                                <input type="text" name="nik" class="form-control" value="<?= escape($p['nik'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-select">
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
                            <label class="form-label fw-semibold small">Alamat Tempat Tinggal</label>
                            <textarea name="alamat" class="form-control" rows="2"><?= escape($p['alamat'] ?? '') ?></textarea>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Jaminan Asuransi</label>
                                <select name="id_asuransi" class="form-select">
                                    <option value="">-- Umum / Mandiri --</option>
                                    <?php foreach ($asuransi as $a): ?>
                                        <option value="<?= $a['id_asuransi'] ?>" <?= (int)($p['id_asuransi'] ?? 0) === (int)$a['id_asuransi'] ? 'selected' : '' ?>>
                                            <?= escape($a['nama_asuransi']) ?> (<?= escape($a['jenis'] ?? '') ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">No. Kartu Asuransi</label>
                                <input type="text" name="no_kartu_asuransi" class="form-control" value="<?= escape($p['no_kartu_asuransi'] ?? '') ?>">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary px-4 fw-semibold">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan Profil
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
