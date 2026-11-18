<?php
/**
 * Modul Kunjungan - Daftar Kunjungan & Antrean Pasien
 * Role: Admin (Semua), Dokter (Pasiennya), Pasien (Milinya)
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_login();

$page_title = 'Kelola Kunjungan & Antrean';
$role = current_role();

// Ambil daftar kunjungan dari API Server B (otomatis disesuaikan oleh role di Server B)
$res = call_api('GET', '/kunjungan/index.php');
$kunjungan = $res['status'] ? ($res['data'] ?? []) : [];

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="app-main">
    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>

    <main class="app-content">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h4 class="fw-bold mb-1">
                    <?= $role === 'dokter' ? 'Antrean Pasien & Rekam Medis' : ($role === 'pasien' ? 'Riwayat Kunjungan Saya' : 'Daftar Kunjungan Pasien') ?>
                </h4>
                <p class="text-muted small mb-0">
                    <?= $role === 'dokter' ? 'Daftar pasien yang terdaftar di jadwal konsultasi Anda' : 'Data antrean poliklinik dan rekam medis pasien' ?>
                </p>
            </div>
            <?php if ($role === 'admin' || $role === 'pasien'): ?>
                <a href="<?= BASE_URL ?>/modules/kunjungan/daftar.php" class="btn btn-primary fw-semibold d-flex align-items-center gap-2">
                    <i class="bi bi-calendar-plus-fill"></i> <?= $role === 'pasien' ? 'Daftar Kunjungan Baru' : 'Catat Kunjungan Baru' ?>
                </a>
            <?php endif; ?>
        </div>

        <div class="card-table">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-clipboard2-pulse-fill text-primary me-2"></i>Data Antrean & Kunjungan</h5>
                <div class="text-muted small">Total: <strong><?= count($kunjungan) ?></strong> Kunjungan</div>
            </div>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>No. RM</th>
                            <th>Nama Pasien</th>
                            <th>Poliklinik</th>
                            <th>Dokter Penanggungjawab</th>
                            <th>Tanggal Periksa</th>
                            <th>Status</th>
                            <th class="text-end" style="width: 170px;">Tindakan / Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($kunjungan)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    Belum ada data kunjungan yang tercatat.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($kunjungan as $k): ?>
                                <tr>
                                    <td><span class="badge bg-light text-dark border fw-bold"><?= escape($k['no_rm']) ?></span></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= escape($k['nama_pasien']) ?></div>
                                        <small class="text-muted"><?= escape($k['nama_asuransi'] ?? 'Umum') ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-primary border">
                                            <?= escape($k['nama_poli']) ?>
                                        </span>
                                    </td>
                                    <td><?= escape($k['nama_dokter']) ?></td>
                                    <td><?= date('d M Y', strtotime($k['tanggal_kunjungan'])) ?></td>
                                    <td>
                                        <span class="badge-status badge-<?= $k['status'] ?>">
                                            <i class="bi bi-circle-fill" style="font-size: 6px;"></i> <?= ucfirst($k['status']) ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <?php if ($role === 'dokter'): ?>
                                            <a href="<?= BASE_URL ?>/modules/kunjungan/periksa.php?id=<?= $k['id_kunjungan'] ?>" class="btn btn-sm btn-primary">
                                                <i class="bi bi-stethoscope"></i> <?= $k['status'] === 'selesai' ? 'Lihat/Ubah' : 'Periksa' ?>
                                            </a>
                                        <?php elseif ($role === 'admin'): ?>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= BASE_URL ?>/modules/kunjungan/periksa.php?id=<?= $k['id_kunjungan'] ?>" class="btn btn-outline-primary" title="Detail / Rekam Medis">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?= BASE_URL ?>/modules/kunjungan/edit.php?id=<?= $k['id_kunjungan'] ?>" class="btn btn-outline-secondary" title="Edit Jadwal">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-danger" title="Hapus" 
                                                        onclick="confirmDelete('<?= BASE_URL ?>/modules/kunjungan/hapus.php?id=<?= $k['id_kunjungan'] ?>', 'Kunjungan pasien <?= escape($k['nama_pasien']) ?>')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        <?php elseif ($role === 'pasien'): ?>
                                            <a href="<?= BASE_URL ?>/modules/kunjungan/periksa.php?id=<?= $k['id_kunjungan'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-file-medical me-1"></i> Rekam Medis
                                            </a>
                                        <?php endif; ?>
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
