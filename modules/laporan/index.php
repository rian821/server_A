<?php
/**
 * Modul Laporan - Pusat Menu Laporan Rumah Sakit
 * Role: Admin, Dokter
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role(['admin', 'dokter']);

$page_title = 'Pusat Laporan';
$role = current_role();

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="app-main">
    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>

    <main class="app-content">
        <div class="mb-4">
            <h4 class="fw-bold mb-1">Pusat Laporan & Rekapitulasi Data</h4>
            <p class="text-muted small mb-0">Cetak dan analisis data operasional rumah sakit berbasis SOA</p>
        </div>

        <div class="row g-4">
            <!-- 1. Laporan Kunjungan Periode (Admin & Dokter) -->
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 rounded-4 shadow-sm p-4 bg-white h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="feature-icon mb-3 bg-primary-subtle text-primary">
                            <i class="bi bi-calendar2-range"></i>
                        </div>
                        <h5 class="fw-bold text-dark">Laporan Kunjungan Periode</h5>
                        <p class="text-muted small">Rekapitulasi riwayat kunjungan pasien berdasarkan rentang tanggal periksa.</p>
                    </div>
                    <a href="<?= BASE_URL ?>/modules/laporan/kunjungan_periode.php" class="btn btn-outline-primary btn-sm w-100 fw-semibold mt-3">
                        Buka Laporan <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>

            <?php if ($role === 'admin'): ?>
                <!-- 2. Laporan Pasien per Poli (Admin) -->
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 rounded-4 shadow-sm p-4 bg-white h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="feature-icon mb-3 bg-info-subtle text-info">
                                <i class="bi bi-building"></i>
                            </div>
                            <h5 class="fw-bold text-dark">Pasien Per Poliklinik</h5>
                            <p class="text-muted small">Statistik jumlah pasien dan kunjungan pada tiap-tiap unit poliklinik spesialis.</p>
                        </div>
                        <a href="<?= BASE_URL ?>/modules/laporan/pasien_poli.php" class="btn btn-outline-info btn-sm w-100 fw-semibold mt-3">
                            Buka Laporan <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- 3. Laporan Pasien Asuransi (Admin) -->
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 rounded-4 shadow-sm p-4 bg-white h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="feature-icon mb-3 bg-success-subtle text-success">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <h5 class="fw-bold text-dark">Pasien Berdasarkan Asuransi</h5>
                            <p class="text-muted small">Daftar klasifikasi pasien BPJS, asuransi swasta rekanan, maupun pasien umum.</p>
                        </div>
                        <a href="<?= BASE_URL ?>/modules/laporan/pasien_asuransi.php" class="btn btn-outline-success btn-sm w-100 fw-semibold mt-3">
                            Buka Laporan <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- 4. Laporan Kunjungan per Dokter (Admin) -->
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 rounded-4 shadow-sm p-4 bg-white h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="feature-icon mb-3 bg-warning-subtle text-warning">
                                <i class="bi bi-person-badge"></i>
                            </div>
                            <h5 class="fw-bold text-dark">Kunjungan Per Dokter</h5>
                            <p class="text-muted small">Beban kerja dan jumlah pasien yang ditangani oleh masing-masing dokter spesialis.</p>
                        </div>
                        <a href="<?= BASE_URL ?>/modules/laporan/kunjungan_dokter.php" class="btn btn-outline-warning btn-sm w-100 fw-semibold mt-3">
                            Buka Laporan <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
