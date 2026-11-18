<?php
/**
 * Laporan: Riwayat Rekam Medis & Kunjungan Lengkap Pasien
 * Role: Admin, Dokter, Pasien (miliknya sendiri)
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_login();

$user = current_user();
$role = current_role();

$id_pasien = (int)($_GET['id_pasien'] ?? 0);
if ($role === 'pasien') {
    $id_pasien = (int)($user['id_pasien'] ?? 0);
}

if (!$id_pasien) {
    set_flash('error', 'ID Pasien tidak ditemukan.');
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit;
}

$page_title = 'Riwayat Rekam Medis Pasien';

// Ambil riwayat dari API Server B
$res = call_api('GET', '/laporan/riwayat_pasien.php?id_pasien=' . $id_pasien);
if (!$res['status'] || empty($res['data'])) {
    set_flash('error', $res['message'] ?? 'Data riwayat pasien tidak ditemukan.');
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit;
}

$p = $res['data']['pasien'];
$riwayat = $res['data']['riwayat'] ?? [];

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="app-main">
    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>

    <main class="app-content">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 no-print">
            <div>
                <a href="javascript:history.back()" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-1 mb-2">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <h4 class="fw-bold mb-0">Berkas Rekam Medis Pasien</h4>
            </div>
            <div>
                <button type="button" class="btn btn-primary fw-semibold d-flex align-items-center gap-2" onclick="window.print()">
                    <i class="bi bi-printer"></i> Cetak Lembar Rekam Medis (PDF)
                </button>
            </div>
        </div>

        <div class="print-header d-none">
            <div class="text-center pb-3">
                <h3 class="fw-bold mb-1"><?= APP_NAME ?></h3>
                <p class="mb-1 small"><?= APP_TAGLINE ?></p>
                <h5 class="fw-bold text-uppercase mt-2">Lembar Riwayat Rekam Medis Pasien</h5>
                <small class="text-muted">Tanggal Cetak: <?= date('d F Y') ?></small>
            </div>
        </div>

        <!-- Kartu Identitas Pasien -->
        <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
            <div class="d-flex align-items-center gap-3 border-bottom pb-3 mb-3">
                <div class="avatar-circle m-0" style="width:50px; height:50px; font-size:22px;">
                    <?= strtoupper(substr($p['nama_pasien'], 0, 1)) ?>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark"><?= escape($p['nama_pasien']) ?></h5>
                    <span class="badge bg-primary-subtle text-primary border border-primary">No. RM: <?= escape($p['no_rm']) ?></span>
                </div>
            </div>

            <div class="row g-3 small">
                <div class="col-sm-6 col-md-3">
                    <span class="text-muted d-block">NIK:</span>
                    <strong><?= escape($p['nik'] ?? '-') ?></strong>
                </div>
                <div class="col-sm-6 col-md-3">
                    <span class="text-muted d-block">Jenis Kelamin / Lahir:</span>
                    <strong><?= $p['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?>, <?= !empty($p['tanggal_lahir']) ? date('d/m/Y', strtotime($p['tanggal_lahir'])) : '-' ?></strong>
                </div>
                <div class="col-sm-6 col-md-3">
                    <span class="text-muted d-block">Jaminan Asuransi:</span>
                    <strong class="text-success"><?= escape($p['nama_asuransi'] ?? 'Umum') ?> <?= !empty($p['no_kartu_asuransi']) ? '(' . escape($p['no_kartu_asuransi']) . ')' : '' ?></strong>
                </div>
                <div class="col-sm-6 col-md-3">
                    <span class="text-muted d-block">Kontak HP:</span>
                    <strong><?= escape($p['no_hp'] ?? '-') ?></strong>
                </div>
            </div>
        </div>

        <!-- Riwayat Pemeriksaan -->
        <div class="card-table">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Riwayat Pemeriksaan & Pengobatan</h5>
                <span class="badge bg-light text-dark border">Total: <?= count($riwayat) ?> Catatan</span>
            </div>
            <div class="table-responsive">
                <table class="table table-custom table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Tanggal</th>
                            <th>Poliklinik</th>
                            <th>Dokter</th>
                            <th>Keluhan Pasien</th>
                            <th>Diagnosis Dokter</th>
                            <th>Tindakan / Resep Obat</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($riwayat)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">Belum ada riwayat kunjungan rekam medis.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($riwayat as $rw): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td class="fw-semibold text-nowrap"><?= date('d/m/Y', strtotime($rw['tanggal_kunjungan'])) ?></td>
                                    <td><span class="badge bg-light text-primary border"><?= escape($rw['nama_poli']) ?></span></td>
                                    <td><?= escape($rw['nama_dokter']) ?></td>
                                    <td><small class="text-muted"><?= escape($rw['keluhan'] ?? '-') ?></small></td>
                                    <td class="fw-bold text-dark"><?= escape($rw['diagnosis'] ?? 'Belum ada diagnosis') ?></td>
                                    <td><small><?= nl2br(escape($rw['tindakan'] ?? '-')) ?></small></td>
                                    <td>
                                        <span class="badge-status badge-<?= $rw['status'] ?>">
                                            <?= ucfirst($rw['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
