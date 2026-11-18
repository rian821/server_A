<?php
/**
 * Modul Dokter - Profil Dokter Mandiri
 * Role: Dokter
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role(['dokter']);

$user = current_user();
$id_dokter = (int)($user['id_dokter'] ?? 0);

$page_title = 'Profil Dokter';
$error_msg = null;

// Ambil detail dokter
$detail_res = call_api('GET', '/dokter/index.php?id=' . $id_dokter);
if (!$detail_res['status'] || empty($detail_res['data'])) {
    set_flash('error', 'Profil dokter tidak ditemukan atau belum terhubung.');
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit;
}
$dok = $detail_res['data'];

// Poliklinik dropdown
$poli_res = call_api('GET', '/poliklinik/index.php');
$poliklinik = $poli_res['status'] ? ($poli_res['data'] ?? []) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_dokter  = trim($_POST['nama_dokter'] ?? '');
    $id_poli      = (int)($_POST['id_poli'] ?? $dok['id_poli']);
    $spesialisasi = trim($_POST['spesialisasi'] ?? '');
    $no_str       = trim($_POST['no_str'] ?? '');
    $no_hp        = trim($_POST['no_hp'] ?? '');

    if (empty($nama_dokter)) {
        $error_msg = 'Nama dokter wajib diisi.';
    } else {
        $res = call_api('PUT', '/dokter/index.php', [
            'id_dokter'    => $id_dokter,
            'nama_dokter'  => $nama_dokter,
            'id_poli'      => $id_poli,
            'spesialisasi' => $spesialisasi,
            'no_str'       => $no_str,
            'no_hp'        => $no_hp
        ]);

        if ($res['status']) {
            // Update nama di sesi jika berubah
            $_SESSION['user']['nama_lengkap'] = $nama_dokter;
            set_flash('success', 'Profil Anda berhasil diperbarui.');
            header('Location: ' . BASE_URL . '/modules/dokter/profil.php');
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
            <h4 class="fw-bold mb-1">Profil Dokter</h4>
            <p class="text-muted small mb-0">Kelola informasi identitas profesional dan kontak Anda</p>
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
                                <label class="form-label fw-semibold small">Nama Lengkap & Gelar <span class="text-danger">*</span></label>
                                <input type="text" name="nama_dokter" class="form-control" value="<?= escape($dok['nama_dokter']) ?>" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-semibold small">Poliklinik Bertugas</label>
                                <select name="id_poli" class="form-select">
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
                                <label class="form-label fw-semibold small">No. Kontak / HP</label>
                                <input type="text" name="no_hp" class="form-control" value="<?= escape($dok['no_hp'] ?? '') ?>">
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
