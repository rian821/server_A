<?php
/**
 * Dashboard Utama (Multi-Role RBAC: Admin, Dokter, Pasien)
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/helpers/api.php';
require_once __DIR__ . '/helpers/auth.php';

require_login();

$user = current_user();
$role = current_role();
$page_title = 'Dashboard ' . ucfirst($role);

// Ambil data statistik dari Web Service Server B
$stat_res = call_api('GET', '/laporan/statistik_dashboard.php');
$stats = $stat_res['status'] ? ($stat_res['data'] ?? []) : [];

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<div class="app-main">
    <?php require_once __DIR__ . '/includes/navbar.php'; ?>

    <main class="app-content">
        <!-- Banner Selamat Datang -->
        <div class="card border-0 rounded-4 shadow-sm mb-4" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 60%, #0f172a 100%); color: #fff;">
            <div class="card-body p-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-1 mb-2">
                        <i class="bi bi-person-badge me-1"></i> Role: <?= strtoupper($role) ?>
                    </span>
                    <h3 class="fw-bold mb-1">Selamat Datang, <?= escape($user['nama_lengkap']) ?>!</h3>
                    <p class="mb-0 opacity-80 small">Sistem Informasi Rumah Sakit Berbasis SOA terhubung ke Server B (192.168.1.10).</p>
                </div>
                <div>
                    <?php if ($role === 'pasien'): ?>
                        <a href="<?= BASE_URL ?>/modules/kunjungan/daftar.php" class="btn btn-light fw-bold text-primary px-4 py-2 shadow-sm">
                            <i class="bi bi-calendar-plus me-1"></i> Daftar Kunjungan Baru
                        </a>
                    <?php elseif ($role === 'dokter'): ?>
                        <a href="<?= BASE_URL ?>/modules/kunjungan/index.php" class="btn btn-light fw-bold text-primary px-4 py-2 shadow-sm">
                            <i class="bi bi-clipboard2-pulse me-1"></i> Buka Antrean Pasien
                        </a>
                    <?php elseif ($role === 'admin'): ?>
                        <a href="<?= BASE_URL ?>/modules/kunjungan/index.php" class="btn btn-light fw-bold text-primary px-4 py-2 shadow-sm">
                            <i class="bi bi-plus-circle me-1"></i> Catat Kunjungan
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- DASHBOARD ADMIN -->
        <!-- ============================================================== -->
        <?php if ($role === 'admin'): ?>
            <!-- Baris Kartu Metrik KPI -->
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div>
                            <div class="stat-title">Total Pasien</div>
                            <div class="stat-value"><?= (int)($stats['total_pasien'] ?? 0) ?></div>
                            <small class="text-muted"><i class="bi bi-people text-primary me-1"></i>Terdaftar di sistem</small>
                        </div>
                        <div class="stat-icon bg-primary-subtle text-primary">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div>
                            <div class="stat-title">Dokter Spesialis</div>
                            <div class="stat-value"><?= (int)($stats['total_dokter'] ?? 0) ?></div>
                            <small class="text-muted"><i class="bi bi-hospital text-info me-1"></i>Aktif bertugas</small>
                        </div>
                        <div class="stat-icon bg-info-subtle text-info">
                            <i class="bi bi-heart-pulse-fill"></i>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div>
                            <div class="stat-title">Poliklinik</div>
                            <div class="stat-value"><?= (int)($stats['total_poli'] ?? 0) ?></div>
                            <small class="text-muted"><i class="bi bi-building text-warning me-1"></i>Unit pelayanan</small>
                        </div>
                        <div class="stat-icon bg-warning-subtle text-warning">
                            <i class="bi bi-building-fill"></i>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div>
                            <div class="stat-title">Kunjungan Hari Ini</div>
                            <div class="stat-value text-success"><?= (int)($stats['kunjungan_hari_ini'] ?? 0) ?></div>
                            <small class="text-muted">Total: <?= (int)($stats['total_kunjungan'] ?? 0) ?> riwayat</small>
                        </div>
                        <div class="stat-icon bg-success-subtle text-success">
                            <i class="bi bi-calendar-check-fill"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Baris Grafik Tren & Status -->
            <div class="row g-4 mb-4">
                <div class="col-lg-8">
                    <div class="card border-0 rounded-4 shadow-sm p-4 h-100 bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-dark mb-0"><i class="bi bi-graph-up text-primary me-2"></i>Tren Kunjungan 7 Hari Terakhir</h6>
                            <span class="badge bg-light text-muted border">Realtime SOA</span>
                        </div>
                        <div style="height: 280px;">
                            <canvas id="chartTrenKunjungan"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card border-0 rounded-4 shadow-sm p-4 h-100 bg-white">
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-pie-chart text-secondary me-2"></i>Status Kunjungan</h6>
                        <div style="height: 240px; position: relative;">
                            <canvas id="chartStatusKunjungan"></canvas>
                        </div>
                        <div class="d-flex justify-content-around mt-3 small text-center">
                            <div><span class="badge bg-warning mb-1"><?= (int)($stats['status_kunjungan']['menunggu'] ?? 0) ?></span><div class="text-muted">Menunggu</div></div>
                            <div><span class="badge bg-info mb-1"><?= (int)($stats['status_kunjungan']['diperiksa'] ?? 0) ?></span><div class="text-muted">Diperiksa</div></div>
                            <div><span class="badge bg-success mb-1"><?= (int)($stats['status_kunjungan']['selesai'] ?? 0) ?></span><div class="text-muted">Selesai</div></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Kunjungan Terbaru -->
            <div class="card-table">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Kunjungan Pasien Terbaru</h5>
                    <a href="<?= BASE_URL ?>/modules/kunjungan/index.php" class="btn btn-sm btn-outline-primary fw-semibold">
                        Lihat Semua Kunjungan <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>No. RM</th>
                                <th>Pasien</th>
                                <th>Poliklinik</th>
                                <th>Dokter</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($stats['kunjungan_terbaru'])): ?>
                                <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada kunjungan tercatat.</td></tr>
                            <?php else: ?>
                                <?php foreach ($stats['kunjungan_terbaru'] as $kj): ?>
                                    <tr>
                                        <td><span class="badge bg-light text-dark border"><?= escape($kj['no_rm']) ?></span></td>
                                        <td class="fw-semibold"><?= escape($kj['nama_pasien']) ?></td>
                                        <td><?= escape($kj['nama_poli']) ?></td>
                                        <td><?= escape($kj['nama_dokter']) ?></td>
                                        <td><?= date('d M Y', strtotime($kj['tanggal_kunjungan'])) ?></td>
                                        <td>
                                            <span class="badge-status badge-<?= $kj['status'] ?>">
                                                <i class="bi bi-circle-fill" style="font-size: 6px;"></i> <?= ucfirst($kj['status']) ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="<?= BASE_URL ?>/modules/kunjungan/periksa.php?id=<?= $kj['id_kunjungan'] ?>" class="btn btn-sm btn-light border text-primary">
                                                <i class="bi bi-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <!-- ============================================================== -->
        <!-- DASHBOARD DOKTER -->
        <!-- ============================================================== -->
        <?php elseif ($role === 'dokter'): ?>
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div>
                            <div class="stat-title">Pasien Saya Hari Ini</div>
                            <div class="stat-value text-primary"><?= (int)($stats['kunjungan_hari_ini'] ?? 0) ?></div>
                            <small class="text-muted">Jadwal pemeriksaan</small>
                        </div>
                        <div class="stat-icon bg-primary-subtle text-primary">
                            <i class="bi bi-calendar2-day"></i>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div>
                            <div class="stat-title">Menunggu Diperiksa</div>
                            <div class="stat-value text-warning"><?= (int)($stats['kunjungan_menunggu'] ?? 0) ?></div>
                            <small class="text-muted">Dalam antrean</small>
                        </div>
                        <div class="stat-icon bg-warning-subtle text-warning">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div>
                            <div class="stat-title">Selesai Ditangani</div>
                            <div class="stat-value text-success"><?= (int)($stats['kunjungan_selesai'] ?? 0) ?></div>
                            <small class="text-muted">Rekam medis terisi</small>
                        </div>
                        <div class="stat-icon bg-success-subtle text-success">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div>
                            <div class="stat-title">Total Pasien Unik</div>
                            <div class="stat-value text-info"><?= (int)($stats['pasien_ditangani'] ?? 0) ?></div>
                            <small class="text-muted">Riwayat penanganan</small>
                        </div>
                        <div class="stat-icon bg-info-subtle text-info">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Antrean Pasien Dokter -->
            <div class="card-table">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-people-fill text-primary me-2"></i>Daftar Antrean & Pemeriksaan Pasien</h5>
                    <a href="<?= BASE_URL ?>/modules/kunjungan/index.php" class="btn btn-sm btn-primary fw-semibold">
                        Lihat Semua Pasien <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>No. RM</th>
                                <th>Nama Pasien</th>
                                <th>Poliklinik</th>
                                <th>Tanggal</th>
                                <th>Keluhan</th>
                                <th>Status</th>
                                <th class="text-end">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($stats['antrean_pasien'])): ?>
                                <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada pasien yang dijadwalkan untuk Anda.</td></tr>
                            <?php else: ?>
                                <?php foreach ($stats['antrean_pasien'] as $ant): ?>
                                    <tr>
                                        <td><span class="badge bg-light text-dark border"><?= escape($ant['no_rm']) ?></span></td>
                                        <td class="fw-semibold"><?= escape($ant['nama_pasien']) ?></td>
                                        <td><?= escape($ant['nama_poli']) ?></td>
                                        <td><?= date('d M Y', strtotime($ant['tanggal_kunjungan'])) ?></td>
                                        <td><small class="text-muted"><?= escape(substr($ant['keluhan'] ?? '-', 0, 40)) ?>...</small></td>
                                        <td>
                                            <span class="badge-status badge-<?= $ant['status'] ?>">
                                                <?= ucfirst($ant['status']) ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="<?= BASE_URL ?>/modules/kunjungan/periksa.php?id=<?= $ant['id_kunjungan'] ?>" class="btn btn-sm btn-primary">
                                                <i class="bi bi-stethoscope me-1"></i> Periksa / Rekam Medis
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <!-- ============================================================== -->
        <!-- DASHBOARD PASIEN -->
        <!-- ============================================================== -->
        <?php elseif ($role === 'pasien'): ?>
            <?php $p = $stats['profil_pasien'] ?? []; ?>
            <div class="row g-4 mb-4">
                <div class="col-lg-4">
                    <div class="card border-0 rounded-4 shadow-sm p-4 bg-white h-100">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="avatar-circle m-0" style="width:55px; height:55px; font-size:22px;">
                                <?= strtoupper(substr($p['nama_pasien'] ?? 'P', 0, 1)) ?>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0"><?= escape($p['nama_pasien'] ?? $user['nama_lengkap']) ?></h5>
                                <span class="badge bg-primary-subtle text-primary border border-primary"><?= escape($p['no_rm'] ?? 'RM-Baru') ?></span>
                            </div>
                        </div>
                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small text-muted border-top pt-3">
                            <li><i class="bi bi-card-heading me-2"></i>NIK: <strong class="text-dark"><?= escape($p['nik'] ?? '-') ?></strong></li>
                            <li><i class="bi bi-gender-ambiguous me-2"></i>Jenis Kelamin: <strong class="text-dark"><?= ($p['jenis_kelamin'] ?? '') === 'L' ? 'Laki-laki' : 'Perempuan' ?></strong></li>
                            <li><i class="bi bi-shield-check me-2"></i>Asuransi: <strong class="text-success"><?= escape($p['nama_asuransi'] ?? 'Umum') ?></strong></li>
                            <li><i class="bi bi-telephone me-2"></i>Kontak HP: <strong class="text-dark"><?= escape($p['no_hp'] ?? '-') ?></strong></li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="stat-card">
                                <div>
                                    <div class="stat-title">Kunjungan Aktif</div>
                                    <div class="stat-value text-warning"><?= (int)($stats['kunjungan_aktif'] ?? 0) ?></div>
                                    <small class="text-muted">Menunggu / Sedang Diperiksa</small>
                                </div>
                                <div class="stat-icon bg-warning-subtle text-warning"><i class="bi bi-hourglass-split"></i></div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="stat-card">
                                <div>
                                    <div class="stat-title">Total Riwayat Periksa</div>
                                    <div class="stat-value text-success"><?= (int)($stats['total_kunjungan'] ?? 0) ?></div>
                                    <small class="text-muted">Konsultasi dokter yang tuntas</small>
                                </div>
                                <div class="stat-icon bg-success-subtle text-success"><i class="bi bi-clipboard2-check"></i></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 p-4 rounded-4 bg-light border d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div>
                            <h6 class="fw-bold mb-1"><i class="bi bi-info-circle text-primary me-2"></i>Ingin berobat atau periksa ke dokter?</h6>
                            <p class="text-muted small mb-0">Daftarkan kunjungan poliklinik Anda secara mandiri dari portal ini.</p>
                        </div>
                        <a href="<?= BASE_URL ?>/modules/kunjungan/daftar.php" class="btn btn-primary fw-semibold px-3 py-2">
                            <i class="bi bi-plus-circle me-1"></i> Daftar Kunjungan Baru
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tabel Riwayat Kunjungan Pasien -->
            <div class="card-table">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-medical text-primary me-2"></i>Riwayat Pemeriksaan & Rekam Medis Anda</h5>
                    <a href="<?= BASE_URL ?>/modules/kunjungan/index.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Poliklinik</th>
                                <th>Dokter</th>
                                <th>Keluhan</th>
                                <th>Diagnosis</th>
                                <th>Status</th>
                                <th class="text-end">Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($stats['riwayat_terakhir'])): ?>
                                <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat kunjungan.</td></tr>
                            <?php else: ?>
                                <?php foreach ($stats['riwayat_terakhir'] as $rw): ?>
                                    <tr>
                                        <td class="fw-semibold"><?= date('d M Y', strtotime($rw['tanggal_kunjungan'])) ?></td>
                                        <td><?= escape($rw['nama_poli']) ?></td>
                                        <td><?= escape($rw['nama_dokter']) ?></td>
                                        <td><small class="text-muted"><?= escape($rw['keluhan'] ?? '-') ?></small></td>
                                        <td><strong><?= escape($rw['diagnosis'] ?? 'Belum ada') ?></strong></td>
                                        <td>
                                            <span class="badge-status badge-<?= $rw['status'] ?>">
                                                <?= ucfirst($rw['status']) ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="<?= BASE_URL ?>/modules/kunjungan/periksa.php?id=<?= $rw['id_kunjungan'] ?>" class="btn btn-sm btn-light border text-primary">
                                                <i class="bi bi-file-text"></i> Rekam Medis
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </main>
</div>

<!-- Script Chart.js untuk Admin -->
<?php if ($role === 'admin'): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Line Chart Tren Kunjungan
    const rawGrafik = <?= json_encode($stats['grafik_kunjungan'] ?? []) ?>;
    const labels = rawGrafik.map(item => item.tanggal_kunjungan);
    const dataValues = rawGrafik.map(item => parseInt(item.jumlah));

    const ctxTren = document.getElementById('chartTrenKunjungan');
    if (ctxTren) {
        new Chart(ctxTren, {
            type: 'line',
            data: {
                labels: labels.length ? labels : ['Hari Ini'],
                datasets: [{
                    label: 'Jumlah Pasien',
                    data: dataValues.length ? dataValues : [<?= (int)($stats['kunjungan_hari_ini'] ?? 0) ?>],
                    borderColor: '#0284c7',
                    backgroundColor: 'rgba(2, 132, 199, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#0284c7',
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }

    // 2. Donut Chart Status Kunjungan
    const ctxStatus = document.getElementById('chartStatusKunjungan');
    if (ctxStatus) {
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['Menunggu', 'Diperiksa', 'Selesai', 'Batal'],
                datasets: [{
                    data: [
                        <?= (int)($stats['status_kunjungan']['menunggu'] ?? 0) ?>,
                        <?= (int)($stats['status_kunjungan']['diperiksa'] ?? 0) ?>,
                        <?= (int)($stats['status_kunjungan']['selesai'] ?? 0) ?>,
                        <?= (int)($stats['status_kunjungan']['batal'] ?? 0) ?>
                    ],
                    backgroundColor: ['#f59e0b', '#06b6d4', '#10b981', '#ef4444'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                },
                cutout: '70%'
            }
        });
    }
});
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
