<?php
/**
 * Modul Poliklinik - Daftar Poliklinik
 * Role: Admin (CRUD), Dokter & Pasien (Lihat)
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_login();

$page_title = 'Kelola Poliklinik';
$role = current_role();

// Ambil daftar poliklinik dari API Server B
$res = call_api('GET', '/poliklinik/index.php');
$poliklinik = $res['status'] ? ($res['data'] ?? []) : [];

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="app-main">
    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>

    <main class="app-content">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h4 class="fw-bold mb-1">Daftar Poliklinik</h4>
                <p class="text-muted small mb-0">Kelola dan lihat unit poliklinik pelayanan rumah sakit</p>
            </div>
            <?php if ($role === 'admin'): ?>
                <a href="<?= BASE_URL ?>/modules/poliklinik/tambah.php" class="btn btn-primary fw-semibold d-flex align-items-center gap-2">
                    <i class="bi bi-plus-lg"></i> Tambah Poliklinik
                </a>
            <?php endif; ?>
        </div>

        <div class="card-table">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-building text-primary me-2"></i>Unit Layanan Poliklinik</h5>
                <div class="text-muted small">Total: <strong><?= count($poliklinik) ?></strong> Poli</div>
            </div>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th style="width: 60px;">No</th>
                            <th>Nama Poliklinik</th>
                            <th>Lokasi Gedung / Lantai</th>
                            <th>Keterangan Pelayanan</th>
                            <th>Dokter Terdaftar</th>
                            <?php if ($role === 'admin'): ?>
                                <th class="text-end" style="width: 150px;">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($poliklinik)): ?>
                            <tr>
                                <td colspan="<?= $role === 'admin' ? 6 : 5 ?>" class="text-center py-4 text-muted">
                                    Belum ada data poliklinik atau gagal mengambil data dari API Server B.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($poliklinik as $poli): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td class="fw-bold text-dark">
                                        <i class="bi bi-hospital text-primary me-2"></i><?= escape($poli['nama_poli']) ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-secondary border">
                                            <i class="bi bi-geo-alt me-1"></i><?= escape($poli['lokasi'] ?? '-') ?>
                                        </span>
                                    </td>
                                    <td><small class="text-muted"><?= escape($poli['keterangan'] ?? '-') ?></small></td>
                                    <td>
                                        <span class="badge bg-info-subtle text-info border border-info">
                                            <?= (int)($poli['total_dokter'] ?? 0) ?> Dokter
                                        </span>
                                    </td>
                                    <?php if ($role === 'admin'): ?>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= BASE_URL ?>/modules/poliklinik/edit.php?id=<?= $poli['id_poli'] ?>" class="btn btn-outline-secondary" title="Edit">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-danger" title="Hapus" 
                                                        onclick="confirmDelete('<?= BASE_URL ?>/modules/poliklinik/hapus.php?id=<?= $poli['id_poli'] ?>', 'Poliklinik <?= escape($poli['nama_poli']) ?>')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    <?php endif; ?>
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
