<?php
/**
 * Laporan: Kunjungan Pasien Per Periode Tanggal
 * Role: Admin, Dokter
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role(['admin', 'dokter']);

$page_title = 'Laporan Kunjungan Periode';
$role = current_role();

$dari   = trim($_GET['dari'] ?? date('Y-m-01'));
$sampai = trim($_GET['sampai'] ?? date('Y-m-d'));

// Panggil endpoint laporan dari API Server B
$res = call_api('GET', "/laporan/kunjungan_periode.php?dari=$dari&sampai=$sampai");
$list = $res['status'] ? ($res['data']['list'] ?? []) : [];
$total = count($list);

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
                <h4 class="fw-bold mb-0">Laporan Kunjungan Per Periode</h4>
            </div>
            <div>
                <button type="button" class="btn btn-primary fw-semibold d-flex align-items-center gap-2" onclick="window.print()">
                    <i class="bi bi-printer"></i> Cetak Laporan (PDF)
                </button>
            </div>
        </div>

        <!-- Filter Tanggal (Disembunyikan saat cetak) -->
        <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4 no-print">
            <form action="" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Dari Tanggal</label>
                    <input type="date" name="dari" class="form-control" value="<?= escape($dari) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Sampai Tanggal</label>
                    <input type="date" name="sampai" class="form-control" value="<?= escape($sampai) ?>" required>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary px-4 w-100 fw-semibold">
                        <i class="bi bi-filter me-1"></i> Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Kop Laporan Saat Print -->
        <div class="print-header d-none">
            <div class="text-center pb-3">
                <h3 class="fw-bold mb-1"><?= APP_NAME ?></h3>
                <p class="mb-1 small"><?= APP_TAGLINE ?></p>
                <h5 class="fw-bold text-uppercase mt-2">Laporan Rekapitulasi Kunjungan Pasien</h5>
                <small class="text-muted">Periode: <?= date('d M Y', strtotime($dari)) ?> s/d <?= date('d M Y', strtotime($sampai)) ?></small>
            </div>
        </div>

        <!-- Tabel Laporan -->
        <div class="card-table">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-table text-primary me-2"></i>Data Kunjungan: <?= date('d/m/Y', strtotime($dari)) ?> - <?= date('d/m/Y', strtotime($sampai)) ?>
                </h5>
                <span class="badge bg-primary-subtle text-primary border border-primary">Total: <?= $total ?> Pasien</span>
            </div>
            <div class="table-responsive">
                <table class="table table-custom table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Tanggal</th>
                            <th>No. RM</th>
                            <th>Nama Pasien</th>
                            <th>Jaminan Asuransi</th>
                            <th>Poliklinik</th>
                            <th>Dokter</th>
                            <th>Diagnosis & Tindakan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($list)): ?>
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    Tidak ada data kunjungan pada rentang periode ini.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($list as $item): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= date('d/m/Y', strtotime($item['tanggal_kunjungan'])) ?></td>
                                    <td class="font-monospace fw-bold"><?= escape($item['no_rm']) ?></td>
                                    <td class="fw-semibold"><?= escape($item['nama_pasien']) ?></td>
                                    <td><small><?= escape($item['nama_asuransi'] ?? 'Umum') ?></small></td>
                                    <td><?= escape($item['nama_poli']) ?></td>
                                    <td><small><?= escape($item['nama_dokter']) ?></small></td>
                                    <td>
                                        <div class="small fw-semibold"><?= escape($item['diagnosis'] ?? '-') ?></div>
                                        <small class="text-muted"><?= escape($item['tindakan'] ?? '') ?></small>
                                    </td>
                                    <td>
                                        <span class="badge-status badge-<?= $item['status'] ?>">
                                            <?= ucfirst($item['status']) ?>
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
