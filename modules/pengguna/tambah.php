<?php
/**
 * Modul Pengguna - Tambah Pengguna Baru
 * Role: Admin
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role(['admin']);

$page_title = 'Tambah Pengguna';
$error_msg = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $username     = trim($_POST['username'] ?? '');
    $password     = trim($_POST['password'] ?? '');
    $role         = trim($_POST['role'] ?? 'pasien');
    $status       = trim($_POST['status'] ?? 'aktif');

    if (empty($nama_lengkap) || empty($username) || empty($password)) {
        $error_msg = 'Nama lengkap, username, dan password wajib diisi.';
    } else {
        $res = call_api('POST', '/pengguna/index.php', [
            'nama_lengkap' => $nama_lengkap,
            'username'     => $username,
            'password'     => $password,
            'role'         => $role,
            'status'       => $status
        ]);

        if ($res['status']) {
            set_flash('success', 'Pengguna baru berhasil ditambahkan.');
            header('Location: ' . BASE_URL . '/modules/pengguna/index.php');
            exit;
        } else {
            $error_msg = $res['message'] ?? 'Gagal menambahkan pengguna.';
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
            <h4 class="fw-bold mb-0">Tambah Akun Pengguna Baru</h4>
        </div>

        <div class="row">
            <div class="col-lg-7">
                <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
                    <?php if ($error_msg): ?>
                        <div class="alert alert-danger py-2 small mb-3"><?= escape($error_msg) ?></div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Nama Lengkap Pengguna <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lengkap" class="form-control" placeholder="Nama lengkap staf / user" required autofocus>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Username <span class="text-danger">*</span></label>
                                <input type="text" name="username" class="form-control" placeholder="Karakter huruf & angka tanpa spasi" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Peran Hak Akses (Role) <span class="text-danger">*</span></label>
                                <select name="role" class="form-select" required>
                                    <option value="admin">Administrator</option>
                                    <option value="dokter">Dokter</option>
                                    <option value="pasien" selected>Pasien</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Status Akun</label>
                                <select name="status" class="form-select">
                                    <option value="aktif" selected>Aktif</option>
                                    <option value="nonaktif">Nonaktif</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4 fw-semibold">
                                <i class="bi bi-save me-1"></i> Simpan Pengguna
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
