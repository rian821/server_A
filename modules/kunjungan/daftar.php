<?php
/**
 * Modul Kunjungan - Pendaftaran Kunjungan Baru
 * Role: Admin, Pasien
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role(['admin', 'pasien']);

$page_title = 'Pendaftaran Kunjungan';
$role = current_role();
$user = current_user();
$error_msg = null;

// Ambil data poliklinik dan dokter dari API
$poli_res = call_api('GET', '/poliklinik/index.php');
$poliklinik = $poli_res['status'] ? ($poli_res['data'] ?? []) : [];

$dok_res = call_api('GET', '/dokter/index.php');
$dokter = $dok_res['status'] ? ($dok_res['data'] ?? []) : [];

// Jika admin, ambil daftar pasien
$pasien_list = [];
if ($role === 'admin') {
    $pas_res = call_api('GET', '/pasien/index.php');
    $pasien_list = $pas_res['status'] ? ($pas_res['data'] ?? []) : [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_poli           = (int)($_POST['id_poli'] ?? 0);
    $id_dokter         = (int)($_POST['id_dokter'] ?? 0);
    $tanggal_kunjungan = trim($_POST['tanggal_kunjungan'] ?? date('Y-m-d'));
    $keluhan           = trim($_POST['keluhan'] ?? '');

    $payload = [
        'id_poli'           => $id_poli,
        'id_dokter'         => $id_dokter,
        'tanggal_kunjungan' => $tanggal_kunjungan,
        'keluhan'           => $keluhan
    ];

    if ($role === 'admin') {
        $id_pasien = (int)($_POST['id_pasien'] ?? 0);
        if (!$id_pasien) {
            $error_msg = 'Pasien wajib dipilih.';
        }
        $payload['id_pasien'] = $id_pasien;
    } else {
        // Pasien otomatis menggunakan id_pasien miliknya
        $payload['id_pasien'] = (int)($user['id_pasien'] ?? 0);
    }

    if (!$error_msg) {
        if (!$id_poli || !$id_dokter || empty($tanggal_kunjungan)) {
            $error_msg = 'Poliklinik, dokter, dan tanggal kunjungan wajib diisi.';
        } else {
            $res = call_api('POST', '/kunjungan/index.php', $payload);

            if ($res['status']) {
                set_flash('success', 'Pendaftaran kunjungan berhasil dibuat! Silakan datang sesuai jadwal.');
                header('Location: ' . BASE_URL . '/modules/kunjungan/index.php');
                exit;
            } else {
                $error_msg = $res['message'] ?? 'Gagal mendaftarkan kunjungan.';
            }
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
            <a href="<?= BASE_URL ?>/modules/kunjungan/index.php" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-1 mb-2">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar Kunjungan
            </a>
            <h4 class="fw-bold mb-0">Pendaftaran Kunjungan Pasien</h4>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
                    <?php if ($error_msg): ?>
                        <div class="alert alert-danger py-2 small mb-3"><?= escape($error_msg) ?></div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <?php if ($role === 'admin'): ?>
                            <!-- Pilihan Pasien Khusus Admin -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Pilih Pasien <span class="text-danger">*</span></label>
                                <select name="id_pasien" class="form-select" required>
                                    <option value="">-- Pilih Pasien Terdaftar --</option>
                                    <?php foreach ($pasien_list as $ps): ?>
                                        <option value="<?= $ps['id_pasien'] ?>">
                                            [<?= escape($ps['no_rm']) ?>] <?= escape($ps['nama_pasien']) ?> (<?= escape($ps['nama_asuransi'] ?? 'Umum') ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-primary bg-primary-subtle border-0 py-2 small mb-3 d-flex align-items-center gap-2">
                                <i class="bi bi-person-check fs-5 text-primary"></i>
                                <div>
                                    Mendaftar sebagai Pasien: <strong><?= escape($user['nama_lengkap']) ?></strong>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Pilih Poliklinik Tujuan <span class="text-danger">*</span></label>
                                <select name="id_poli" id="selectPoli" class="form-select" required onchange="filterDokterByPoli()">
                                    <option value="">-- Pilih Poliklinik --</option>
                                    <?php foreach ($poliklinik as $p): ?>
                                        <option value="<?= $p['id_poli'] ?>"><?= escape($p['nama_poli']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Pilih Dokter Spesialis <span class="text-danger">*</span></label>
                                <select name="id_dokter" id="selectDokter" class="form-select" required>
                                    <option value="">-- Pilih Dokter --</option>
                                    <?php foreach ($dokter as $d): ?>
                                        <option value="<?= $d['id_dokter'] ?>" data-poli="<?= $d['id_poli'] ?>">
                                            <?= escape($d['nama_dokter']) ?> (<?= escape($d['spesialisasi'] ?? '-') ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Rencana Tanggal Kunjungan <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_kunjungan" class="form-control" value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold small">Keluhan yang Dirasakan / Gejala Awal</label>
                            <textarea name="keluhan" class="form-control" rows="3" placeholder="Jelaskan keluhan medis seperti: sakit perut melilit 2 hari, demam tinggi, pusing..."></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4 fw-semibold">
                                <i class="bi bi-calendar-check me-1"></i> Simpan Pendaftaran
                            </button>
                            <a href="<?= BASE_URL ?>/modules/kunjungan/index.php" class="btn btn-light border px-4">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
function filterDokterByPoli() {
    const selectedPoli = document.getElementById('selectPoli').value;
    const dokterSelect = document.getElementById('selectDokter');
    const options = dokterSelect.querySelectorAll('option');

    let firstMatch = '';
    options.forEach(opt => {
        if (!opt.value) return; // opsi placeholder
        const poliId = opt.getAttribute('data-poli');
        if (!selectedPoli || poliId === selectedPoli) {
            opt.style.display = 'block';
            if (!firstMatch) firstMatch = opt.value;
        } else {
            opt.style.display = 'none';
        }
    });

    if (firstMatch) {
        dokterSelect.value = firstMatch;
    } else {
        dokterSelect.value = '';
    }
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
