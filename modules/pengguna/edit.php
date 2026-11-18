<?php
/**
 * Modul Pengguna - Edit Data / Reset Password
 * Role: Admin
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role(['admin']);

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: ' . BASE_URL . '/modules/pengguna/index.php');
    exit;
}

$page_title = 'Edit Pengguna';
$error_msg = null;

// Ambil detail pengguna
$detail_res = call_api('GET', '/pengguna/index.php?id=' . $id);
if (!$detail_res['status'] || empty($detail_res['data'])) {
    set_flash('error', 'Data pengguna tidak ditemukan.');
    header('Location: ' . BASE_URL . '/modules/pengguna/index.php');
    exit;
}
$u = $detail_res['data'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $role         = trim($_POST['role'] ?? '');
    $status       = trim($_POST['status'] ?? 'aktif');
    $password     = trim($_POST['password'] ?? '');

    if (empty($nama_lengkap) || empty($role)) {
        $error_msg = 'Nama lengkap dan role wajib diisi.';
    } else {
        $payload = [
            'id_pengguna'  => $id,
            'nama_lengkap' => $nama_lengkap,
            'role'         => $role,
            'status'       => $status
        ];
        if (!empty($password)) {
            $payload['password'] = $password;
        }

        $res = call_api('PUT', '/pengguna/index.php', $payload);

        if ($res['status']) {
            set_flash('success', 'Data pengguna berhasil diperbarui.');
            header('Location: ' . BASE_URL . '/modules/pengguna/index.php');
            exit;
        } else {
            $error_msg = $res['message'] ?? 'Gagal memperbarui pengguna.';
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
            <a href="<?= BASE_URL ?>/modules/pengguna/index.php" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-1 mb-2">
                <i class="bi bi-arrow-left"></i> Kembali ke Kelola Pengguna
            </a>
            <h4 class="fw-bold mb-0">Edit Akun Pengguna</h4>
        </div>

        <div class="row">
            <div class="col-lg-7">
                <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
                    <?php if ($error_msg): ?>
                        <div class="alert alert-danger py-2 small mb-3"><?= escape($error_msg) ?></div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Username</label>
                            <input type="text" class="form-control bg-light" value="<?= escape($u['username']) ?>" disabled>
                            <small class="text-muted">Username unik tidak dapat diubah.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lengkap" class="form-control" value="<?= escape($u['nama_lengkap']) ?>" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Peran (Role) <span class="text-danger">*</span></label>
                                <select name="role" class="form-select" required>
                                    <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Administrator</option>
                                    <option value="dokter" <?= $u['role'] === 'dokter' ? 'selected' : '' ?>>Dokter</option>
                                    <option value="pasien" <?= $u['role'] === 'pasien' ? 'selected' : '' ?>>Pasien</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Status Akun</label>
                                <select name="status" class="form-select">
                                    <option value="aktif" <?= $u['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                    <option value="nonaktif" <?= $u['status'] === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4 pt-2 border-top">
                            <label class="form-label fw-semibold small">Reset Password (Opsional)</label>
                            <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password">
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4 fw-semibold">
                                <i class="bi bi-save me-1"></i> Perbarui Akun
                            </button>
                            <a href="<?= BASE_URL ?>/modules/pengguna/index.php" class="btn btn-light border px-4">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
