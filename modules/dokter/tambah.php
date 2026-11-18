<?php
/**
 * Modul Dokter - Tambah Data
 * Role: Admin
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role(['admin']);

$page_title = 'Tambah Dokter';
$error_msg = null;

// Ambil daftar poliklinik untuk dropdown
$poli_res = call_api('GET', '/poliklinik/index.php');
$poliklinik = $poli_res['status'] ? ($poli_res['data'] ?? []) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_dokter  = trim($_POST['nama_dokter'] ?? '');
    $id_poli      = (int)($_POST['id_poli'] ?? 0);
    $spesialisasi = trim($_POST['spesialisasi'] ?? '');
    $no_str       = trim($_POST['no_str'] ?? '');
    $no_hp        = trim($_POST['no_hp'] ?? '');
    $create_user  = !empty($_POST['create_user']);
    $username     = trim($_POST['username'] ?? '');
    $password     = trim($_POST['password'] ?? '');

    if (empty($nama_dokter) || !$id_poli) {
        $error_msg = 'Nama dokter dan poliklinik wajib diisi.';
    } elseif ($create_user && (empty($username) || empty($password))) {
        $error_msg = 'Username dan password wajib diisi jika membuat akun login.';
    } else {
        $payload = [
            'nama_dokter'  => $nama_dokter,
            'id_poli'      => $id_poli,
            'spesialisasi' => $spesialisasi,
            'no_str'       => $no_str,
            'no_hp'        => $no_hp
        ];

        if ($create_user) {
            $payload['username'] = $username;
            $payload['password'] = $password;
        }

        $res = call_api('POST', '/dokter/index.php', $payload);

        if ($res['status']) {
            set_flash('success', 'Dokter baru berhasil ditambahkan.');
            header('Location: ' . BASE_URL . '/modules/dokter/index.php');
            exit;
        } else {
            $error_msg = $res['message'] ?? 'Gagal menambahkan dokter.';
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
            <h4 class="fw-bold mb-0">Tambah Dokter Baru</h4>
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
                                <label class="form-label fw-semibold small">Nama Lengkap Dokter & Gelar <span class="text-danger">*</span></label>
                                <input type="text" name="nama_dokter" class="form-control" placeholder="Contoh: dr. Andi Wijaya, Sp.PD" required autofocus>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-semibold small">Poliklinik Bertugas <span class="text-danger">*</span></label>
                                <select name="id_poli" class="form-select" required>
                                    <option value="">-- Pilih Poliklinik --</option>
                                    <?php foreach ($poliklinik as $p): ?>
                                        <option value="<?= $p['id_poli'] ?>"><?= escape($p['nama_poli']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Spesialisasi</label>
                                <input type="text" name="spesialisasi" class="form-control" placeholder="Contoh: Penyakit Dalam">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">No. STR (Surat Tanda Registrasi)</label>
                                <input type="text" name="no_str" class="form-control" placeholder="STR-xxxx-xxxx">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">No. Telepon / HP</label>
                                <input type="text" name="no_hp" class="form-control" placeholder="08xxxxxxxx">
                            </div>
                        </div>

                        <!-- Opsi Akun Pengguna -->
                        <div class="border rounded-3 p-3 bg-light mb-4">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="create_user" id="checkUser" onchange="toggleUserFields(this.checked)">
                                <label class="form-check-label fw-bold text-dark small" for="checkUser">
                                    <i class="bi bi-person-check me-1 text-primary"></i> Sekaligus buat akun pengguna untuk dokter ini
                                </label>
                            </div>
                            <div id="userFields" class="d-none mt-3 pt-2 border-top">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Username Login</label>
                                        <input type="text" name="username" id="username" class="form-control form-control-sm" placeholder="Contoh: dr_andi">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Password</label>
                                        <input type="password" name="password" id="password" class="form-control form-control-sm" placeholder="Minimal 6 karakter">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4 fw-semibold">
                                <i class="bi bi-save me-1"></i> Simpan Dokter
                            </button>
                            <a href="<?= BASE_URL ?>/modules/dokter/index.php" class="btn btn-light border px-4">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
function toggleUserFields(checked) {
    const fields = document.getElementById('userFields');
    if (checked) {
        fields.classList.remove('d-none');
    } else {
        fields.classList.add('d-none');
    }
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
