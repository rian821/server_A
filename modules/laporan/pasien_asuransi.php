<?php
/**
 * Laporan: Daftar Pasien Berdasarkan Asuransi
 * Role: Admin
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role(['admin']);

$page_title = 'Laporan Pasien Asuransi';

$res = call_api('GET', '/laporan/pasien_asuransi.php');
$data = $res['status'] ? ($res['data'] ?? []) : [];

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="app-main">
    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>

    <main class="app-content">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 no-print">
            <div>
                <a href="<?= BASE_URL ?>/modules/laporan/index.php" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-1 mb-2">
                    <i class="bi bi-arrow-left"></i> Kembali ke Pusat Laporan
                </a>
                <h4 class="fw-bold mb-0">Laporan Pasien Berdasarkan Asuransi</h4>
            </div>
            <div>
                <button type="button" class="btn btn-primary fw-semibold d-flex align-items-center gap-2" onclick="window.print()">
                    <i class="bi bi-printer"></i> Cetak Laporan (PDF)
                </button>
            </div>
        </div>

        <div class="print-header d-none">
            <div class="text-center pb-3">
                <h3 class="fw-bold mb-1"><?= APP_NAME ?></h3>
                <p class="mb-1 small"><?= APP_TAGLINE ?></p>
                <h5 class="fw-bold text-uppercase mt-2">Daftar Pasien Berdasarkan Asuransi & Jaminan Kesehatan</h5>
                <small class="text-muted">Tanggal Cetak: <?= date('d F Y') ?></small>
            </div>
        </div>

        <div class="card-table">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-shield-check text-primary me-2"></i>Klasifikasi Asuransi Pasien</h5>
                <span class="badge bg-primary-subtle text-primary border border-primary">Total: <?= count($data) ?> Pasien</span>
            </div>
            <div class="table-responsive">
                <table class="table table-custom table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>No. RM</th>
                            <th>Nama Pasien</th>
                            <th>NIK</th>
                            <th>Gender</th>
                            <th>Nama Jaminan Asuransi</th>
                            <th>No. Polis / Kartu</th>
                            <th>No. HP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">Belum ada data pasien terdaftar.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($data as $item): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td class="font-monospace fw-bold"><?= escape($item['no_rm']) ?></td>
                                    <td class="fw-semibold"><?= escape($item['nama_pasien']) ?></td>
                                    <td><small class="text-muted"><?= escape($item['nik'] ?? '-') ?></small></td>
                                    <td><?= $item['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                                    <td>
                                        <span class="badge bg-<?= $item['nama_asuransi'] === 'Umum / Mandiri' ? 'secondary' : 'success' ?>-subtle text-<?= $item['nama_asuransi'] === 'Umum / Mandiri' ? 'secondary' : 'success' ?> border">
                                            <?= escape($item['nama_asuransi']) ?>
                                        </span>
                                    </td>
                                    <td class="font-monospace"><small><?= escape($item['no_kartu_asuransi'] ?? '-') ?></small></td>
                                    <td><small><?= escape($item['no_hp'] ?? '-') ?></small></td>
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
