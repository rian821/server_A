<?php
/**
 * Modul Asuransi - Daftar Asuransi
 * Role: Admin (CRUD), Dokter & Pasien (Lihat)
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_login();

$page_title = 'Kelola Asuransi';
$role = current_role();

// Ambil data asuransi dari API Server B
$res = call_api('GET', '/asuransi/index.php');
$asuransi = $res['status'] ? ($res['data'] ?? []) : [];

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="app-main">
    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>

    <main class="app-content">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h4 class="fw-bold mb-1">Daftar Mitra Asuransi</h4>
                <p class="text-muted small mb-0">Kelola dan lihat jaminan kesehatan & asuransi rekanan</p>
            </div>
            <?php if ($role === 'admin'): ?>
                <a href="<?= BASE_URL ?>/modules/asuransi/tambah.php" class="btn btn-primary fw-semibold d-flex align-items-center gap-2">
                    <i class="bi bi-plus-lg"></i> Tambah Asuransi
                </a>
            <?php endif; ?>
        </div>

        <div class="card-table">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-shield-check text-primary me-2"></i>Daftar Asuransi Rekanan</h5>
                <div class="text-muted small">Total: <strong><?= count($asuransi) ?></strong> Mitra</div>
            </div>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th style="width: 60px;">No</th>
                            <th>Nama Asuransi</th>
                            <th>Jenis Lembaga</th>
                            <th>No. Kontak / Call Center</th>
                            <th>Keterangan Kerjasama</th>
                            <th>Pasien Terdaftar</th>
                            <?php if ($role === 'admin'): ?>
                                <th class="text-end" style="width: 150px;">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($asuransi)): ?>
                            <tr>
                                <td colspan="<?= $role === 'admin' ? 7 : 6 ?>" class="text-center py-4 text-muted">
                                    Belum ada data asuransi atau gagal menghubungi Server B.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($asuransi as $asu): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td class="fw-bold text-dark">
                                        <i class="bi bi-shield-fill-check text-success me-2"></i><?= escape($asu['nama_asuransi']) ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <?= escape($asu['jenis'] ?? 'Swasta') ?>
                                        </span>
                                    </td>
                                    <td><i class="bi bi-telephone text-muted me-1"></i><?= escape($asu['no_kontak'] ?? '-') ?></td>
                                    <td><small class="text-muted"><?= escape($asu['keterangan'] ?? '-') ?></small></td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border border-primary">
                                            <?= (int)($asu['total_pasien'] ?? 0) ?> Pasien
                                        </span>
                                    </td>
                                    <?php if ($role === 'admin'): ?>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= BASE_URL ?>/modules/asuransi/edit.php?id=<?= $asu['id_asuransi'] ?>" class="btn btn-outline-secondary" title="Edit">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-danger" title="Hapus" 
                                                        onclick="confirmDelete('<?= BASE_URL ?>/modules/asuransi/hapus.php?id=<?= $asu['id_asuransi'] ?>', 'Asuransi <?= escape($asu['nama_asuransi']) ?>')">
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
