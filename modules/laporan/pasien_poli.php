<?php
/**
 * Laporan: Jumlah Pasien Per Poliklinik
 * Role: Admin
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role(['admin']);

$page_title = 'Laporan Pasien Per Poliklinik';

$res = call_api('GET', '/laporan/pasien_per_poli.php');
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
                <h4 class="fw-bold mb-0">Laporan Jumlah Pasien Per Poliklinik</h4>
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
                <h5 class="fw-bold text-uppercase mt-2">Laporan Rekapitulasi Pasien Per Poliklinik</h5>
                <small class="text-muted">Tanggal Cetak: <?= date('d F Y') ?></small>
            </div>
        </div>

        <div class="card-table">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-building text-primary me-2"></i>Distribusi Pasien Poliklinik</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-custom table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 60px;">No</th>
                            <th>Nama Poliklinik</th>
                            <th>Lokasi Gedung / Ruang</th>
                            <th class="text-center">Total Pasien Unik</th>
                            <th class="text-center">Total Riwayat Kunjungan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada data kunjungan poliklinik.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($data as $item): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td class="fw-bold text-dark"><i class="bi bi-hospital text-primary me-2"></i><?= escape($item['nama_poli']) ?></td>
                                    <td><?= escape($item['lokasi'] ?? '-') ?></td>
                                    <td class="text-center fw-bold text-primary"><?= (int)$item['total_pasien_unik'] ?> Pasien</td>
                                    <td class="text-center fw-bold text-success"><?= (int)$item['total_kunjungan'] ?> Kunjungan</td>
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
