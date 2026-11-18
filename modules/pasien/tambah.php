<?php
/**
 * Modul Pasien - Tambah Data Pasien Baru
 * Role: Admin
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role(['admin']);

$page_title = 'Tambah Pasien';
$error_msg = null;

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
    $no_rm             = trim($_POST['no_rm'] ?? '');
    $create_user       = !empty($_POST['create_user']);
    $username          = trim($_POST['username'] ?? '');
    $password          = trim($_POST['password'] ?? '');

    if (empty($nama_pasien)) {
        $error_msg = 'Nama pasien wajib diisi.';
    } elseif ($create_user && (empty($username) || empty($password))) {
        $error_msg = 'Username dan password wajib diisi jika membuat akun pasien.';
    } else {
        $payload = [
            'nama_pasien'       => $nama_pasien,
            'nik'               => $nik,
            'jenis_kelamin'     => $jenis_kelamin,
            'tanggal_lahir'     => !empty($tanggal_lahir) ? $tanggal_lahir : null,
            'alamat'            => $alamat,
            'no_hp'             => $no_hp,
            'id_asuransi'       => $id_asuransi,
            'no_kartu_asuransi' => $no_kartu_asuransi,
            'no_rm'             => $no_rm
        ];

        if ($create_user) {
            $payload['username'] = $username;
            $payload['password'] = $password;
        }

        $res = call_api('POST', '/pasien/index.php', $payload);

        if ($res['status']) {
            set_flash('success', 'Pasien baru berhasil didaftarkan dengan No. RM: ' . ($res['data']['no_rm'] ?? ''));
            header('Location: ' . BASE_URL . '/modules/pasien/index.php');
            exit;
        } else {
            $error_msg = $res['message'] ?? 'Gagal mendaftarkan pasien.';
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
            <h4 class="fw-bold mb-0">Pendaftaran Pasien Baru</h4>
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
                                <input type="text" name="nama_pasien" class="form-control" placeholder="Nama sesuai KTP" required autofocus>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-semibold small">NIK (Nomor Induk Kependudukan)</label>
                                <input type="text" name="nik" class="form-control" maxlength="20" placeholder="16 digit NIK KTP">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select name="jenis_kelamin" class="form-select" required>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">No. HP / WhatsApp</label>
                                <input type="text" name="no_hp" class="form-control" placeholder="08xxxxxxxx">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control" rows="2" placeholder="Nama jalan, RT/RW, kelurahan, kota..."></textarea>
                        </div>

                        <!-- Jaminan Asuransi -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Jaminan Asuransi</label>
                                <select name="id_asuransi" class="form-select">
                                    <option value="">-- Umum / Bayar Mandiri --</option>
                                    <?php foreach ($asuransi as $a): ?>
                                        <option value="<?= $a['id_asuransi'] ?>"><?= escape($a['nama_asuransi']) ?> (<?= escape($a['jenis'] ?? '') ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">No. Kartu / Polis Asuransi</label>
                                <input type="text" name="no_kartu_asuransi" class="form-control" placeholder="Contoh: No. BPJS atau No. Polis">
                            </div>
                        </div>

                        <!-- Opsi Akun Pasien Mandiri -->
                        <div class="border rounded-3 p-3 bg-light mb-4">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="create_user" id="checkUserPasien" onchange="toggleUserPasien(this.checked)">
                                <label class="form-check-label fw-bold text-dark small" for="checkUserPasien">
                                    <i class="bi bi-person-lock me-1 text-primary"></i> Buat akun portal pasien agar bisa login mandiri
                                </label>
                            </div>
                            <div id="userPasienFields" class="d-none mt-3 pt-2 border-top">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Username Login</label>
                                        <input type="text" name="username" class="form-control form-control-sm" placeholder="Contoh: ahmad_fauzi">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Password</label>
                                        <input type="password" name="password" class="form-control form-control-sm" placeholder="Password akun pasien">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4 fw-semibold">
                                <i class="bi bi-person-check me-1"></i> Daftarkan Pasien
                            </button>
                            <a href="<?= BASE_URL ?>/modules/pasien/index.php" class="btn btn-light border px-4">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
function toggleUserPasien(checked) {
    const fields = document.getElementById('userPasienFields');
    if (checked) {
        fields.classList.remove('d-none');
    } else {
        fields.classList.add('d-none');
    }
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
