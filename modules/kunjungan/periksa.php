<?php
/**
 * Modul Kunjungan - Pemeriksaan Dokter & Rekam Medis Pasien
 * Role: Admin, Dokter (Isi Rekam Medis), Pasien (Lihat Hasil Rekam Medis)
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_login();

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: ' . BASE_URL . '/modules/kunjungan/index.php');
    exit;
}

$page_title = 'Pemeriksaan & Rekam Medis';
$role = current_role();
$user = current_user();
$error_msg = null;

// Ambil detail kunjungan dari API Server B
$res = call_api('GET', '/kunjungan/index.php?id=' . $id);
if (!$res['status'] || empty($res['data'])) {
    set_flash('error', $res['message'] ?? 'Data kunjungan tidak ditemukan.');
    header('Location: ' . BASE_URL . '/modules/kunjungan/index.php');
    exit;
}
$k = $res['data'];

// Jika dokter atau admin mengirim form update rekam medis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($role === 'dokter' || $role === 'admin')) {
    $diagnosis = trim($_POST['diagnosis'] ?? '');
    $tindakan  = trim($_POST['tindakan'] ?? '');
    $status    = trim($_POST['status'] ?? 'selesai');

    $update_res = call_api('PUT', '/kunjungan/index.php', [
        'id_kunjungan' => $id,
        'diagnosis'    => $diagnosis,
        'tindakan'     => $tindakan,
        'status'       => $status
    ]);

    if ($update_res['status']) {
        set_flash('success', 'Rekam medis dan hasil pemeriksaan berhasil disimpan.');
        header('Location: ' . BASE_URL . '/modules/kunjungan/periksa.php?id=' . $id);
        exit;
    } else {
        $error_msg = $update_res['message'] ?? 'Gagal memperbarui pemeriksaan.';
    }
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="app-main">
    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>

    <main class="app-content">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <a href="<?= BASE_URL ?>/modules/kunjungan/index.php" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-1 mb-2 no-print">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar Kunjungan
                </a>
                <h4 class="fw-bold mb-0">Catatan Rekam Medis Kunjungan</h4>
            </div>
            <div class="no-print">
                <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> Cetak Rekam Medis
                </button>
            </div>
        </div>

        <!-- Header Khusus Cetak -->
        <div class="print-header d-none">
            <div class="d-flex align-items-center gap-3">
                <div>
                    <h3 class="fw-bold mb-0"><?= APP_NAME ?></h3>
                    <p class="mb-0 small text-muted"><?= APP_TAGLINE ?> &bull; Rekam Medis Resmi</p>
                </div>
            </div>
        </div>

        <!-- Ringkasan Info Pasien & Kunjungan -->
        <div class="row g-4 mb-4">
            <div class="col-lg-5">
                <div class="card border-0 rounded-4 shadow-sm p-4 bg-white h-100">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                        <i class="bi bi-person-vcard text-primary me-2"></i>Identitas Pasien
                    </h6>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="avatar-circle m-0" style="width:48px; height:48px; font-size:20px;">
                            <?= strtoupper(substr($k['nama_pasien'], 0, 1)) ?>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark"><?= escape($k['nama_pasien']) ?></h5>
                            <span class="badge bg-primary-subtle text-primary border border-primary">No. RM: <?= escape($k['no_rm']) ?></span>
                        </div>
                    </div>
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small text-muted">
                        <li><i class="bi bi-card-heading me-2"></i>NIK: <strong class="text-dark"><?= escape($k['nik'] ?? '-') ?></strong></li>
                        <li><i class="bi bi-gender-ambiguous me-2"></i>Jenis Kelamin: <strong class="text-dark"><?= $k['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></strong></li>
                        <li><i class="bi bi-calendar me-2"></i>Tanggal Lahir: <strong class="text-dark"><?= !empty($k['tanggal_lahir']) ? date('d M Y', strtotime($k['tanggal_lahir'])) : '-' ?></strong></li>
                        <li><i class="bi bi-shield-check me-2"></i>Asuransi: <strong class="text-success"><?= escape($k['nama_asuransi'] ?? 'Umum') ?> <?= !empty($k['no_kartu_asuransi']) ? '(' . escape($k['no_kartu_asuransi']) . ')' : '' ?></strong></li>
                        <li><i class="bi bi-telephone me-2"></i>Kontak: <strong class="text-dark"><?= escape($k['no_hp_pasien'] ?? '-') ?></strong></li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card border-0 rounded-4 shadow-sm p-4 bg-white h-100">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                        <i class="bi bi-clipboard2-pulse text-info me-2"></i>Informasi Pelayanan
                    </h6>
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Poliklinik Tujuan:</small>
                            <span class="fw-bold text-dark"><i class="bi bi-hospital me-1 text-primary"></i><?= escape($k['nama_poli']) ?></span>
                            <small class="text-muted d-block"><?= escape($k['lokasi_poli'] ?? '') ?></small>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Dokter Pemeriksa:</small>
                            <span class="fw-bold text-dark"><?= escape($k['nama_dokter']) ?></span>
                            <small class="text-primary d-block"><?= escape($k['spesialisasi'] ?? '') ?></small>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Tanggal Kunjungan:</small>
                            <span class="fw-bold text-dark"><i class="bi bi-calendar-event me-1"></i><?= date('l, d F Y', strtotime($k['tanggal_kunjungan'])) ?></span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Status Kunjungan:</small>
                            <span class="badge-status badge-<?= $k['status'] ?>">
                                <?= ucfirst($k['status']) ?>
                            </span>
                        </div>
                    </div>

                    <div class="p-3 rounded-3 bg-light border">
                        <small class="text-muted fw-bold d-block mb-1 text-uppercase" style="font-size: 11px;">Keluhan Pasien:</small>
                        <p class="mb-0 text-dark"><?= nl2br(escape($k['keluhan'] ?? 'Tidak ada keluhan tertulis.')) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Pemeriksaan Dokter ATAU View Hasil Rekam Medis -->
        <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-journal-medical text-primary me-2"></i>Hasil Diagnosis & Tindakan Medis</h5>

            <?php if ($error_msg): ?>
                <div class="alert alert-danger py-2 small mb-3"><?= escape($error_msg) ?></div>
            <?php endif; ?>

            <?php if ($role === 'dokter' || $role === 'admin'): ?>
                <!-- Dokter & Admin bisa mengisi/mengupdate form -->
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Diagnosis Medis <span class="text-danger">*</span></label>
                        <textarea name="diagnosis" class="form-control" rows="3" placeholder="Tuliskan hasil diagnosa dokter (Contoh: Gastritis Akut, Faringitis Akut, Hipertensi Grade 1)..." required><?= escape($k['diagnosis'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Tindakan / Terapi / Resep Obat <span class="text-danger">*</span></label>
                        <textarea name="tindakan" class="form-control" rows="3" placeholder="Tuliskan tindakan medis, resep obat, dosis, atau anjuran kontrol..." required><?= escape($k['tindakan'] ?? '') ?></textarea>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Status Pemeriksaan</label>
                            <select name="status" class="form-select">
                                <option value="menunggu" <?= $k['status'] === 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                                <option value="diperiksa" <?= $k['status'] === 'diperiksa' ? 'selected' : '' ?>>Sedang Diperiksa</option>
                                <option value="selesai" <?= $k['status'] === 'selesai' ? 'selected' : '' ?>>Selesai (Pemeriksaan Tuntas)</option>
                                <option value="batal" <?= $k['status'] === 'batal' ? 'selected' : '' ?>>Batal</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex gap-2 no-print">
                        <button type="submit" class="btn btn-primary px-4 fw-semibold">
                            <i class="bi bi-check2-circle me-1"></i> Simpan Rekam Medis
                        </button>
                        <a href="<?= BASE_URL ?>/modules/kunjungan/index.php" class="btn btn-light border px-4">Kembali</a>
                    </div>
                </form>
            <?php else: ?>
                <!-- Pasien hanya membaca hasil rekam medis miliknya -->
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 bg-light border h-100">
                            <small class="text-muted fw-bold d-block mb-1 text-uppercase" style="font-size: 11px;">Diagnosis:</small>
                            <h6 class="fw-bold text-dark mb-0"><?= escape($k['diagnosis'] ?? 'Belum ada diagnosis (menunggu pemeriksaan dokter).') ?></h6>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 bg-light border h-100">
                            <small class="text-muted fw-bold d-block mb-1 text-uppercase" style="font-size: 11px;">Tindakan / Resep Obat:</small>
                            <p class="mb-0 text-dark"><?= nl2br(escape($k['tindakan'] ?? 'Belum ada resep / instruksi tindakan.')) ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
