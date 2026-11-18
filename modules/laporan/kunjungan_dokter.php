<?php
/**
 * Laporan: Kunjungan Pasien Per Dokter
 * Role: Admin
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role(['admin']);

$page_title = 'Laporan Kunjungan Per Dokter';

$res = call_api('GET', '/laporan/kunjungan_per_dokter.php');
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
                <h4 class="fw-bold mb-0">Laporan Beban Kerja Kunjungan Per Dokter</h4>
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
                <h5 class="fw-bold text-uppercase mt-2">Laporan Rekapitulasi Pelayanan Dokter Spesialis</h5>
                <small class="text-muted">Tanggal Cetak: <?= date('d F Y') ?></small>
            </div>
        </div>

        <div class="card-table">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-person-badge text-primary me-2"></i>Kunjungan Per Dokter</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-custom table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Nama Dokter & Gelar</th>
                            <th>Spesialisasi</th>
                            <th>Unit Poliklinik</th>
                            <th class="text-center">Selesai</th>
                            <th class="text-center">Menunggu</th>
                            <th class="text-center">Sedang Diperiksa</th>
                            <th class="text-center">Total Kunjungan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">Belum ada data kunjungan dokter.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($data as $item): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td class="fw-bold text-dark"><i class="bi bi-heart-pulse text-danger me-2"></i><?= escape($item['nama_dokter']) ?></td>
                                    <td><?= escape($item['spesialisasi'] ?? '-') ?></td>
                                    <td><span class="badge bg-light text-dark border"><?= escape($item['nama_poli']) ?></span></td>
                                    <td class="text-center fw-semibold text-success"><?= (int)$item['selesai'] ?></td>
                                    <td class="text-center fw-semibold text-warning"><?= (int)$item['menunggu'] ?></td>
                                    <td class="text-center fw-semibold text-info"><?= (int)$item['diperiksa'] ?></td>
                                    <td class="text-center fw-bold text-primary"><?= (int)$item['total_kunjungan'] ?> Pasien</td>
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
