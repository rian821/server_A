<?php
/**
 * Modul Dokter - Daftar Dokter
 * Role: Admin (CRUD), Dokter & Pasien (Lihat)
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_login();

$page_title = 'Data Dokter';
$role = current_role();

// Ambil data dokter dari API Server B
$res = call_api('GET', '/dokter/index.php');
$dokter = $res['status'] ? ($res['data'] ?? []) : [];

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="app-main">
    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>

    <main class="app-content">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h4 class="fw-bold mb-1">Data Dokter & Tenaga Medis</h4>
                <p class="text-muted small mb-0">Daftar dokter spesialis dan penugasan poliklinik</p>
            </div>
            <?php if ($role === 'admin'): ?>
                <a href="<?= BASE_URL ?>/modules/dokter/tambah.php" class="btn btn-primary fw-semibold d-flex align-items-center gap-2">
                    <i class="bi bi-person-plus-fill"></i> Tambah Dokter Baru
                </a>
            <?php endif; ?>
        </div>

        <div class="card-table">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-heart-pulse-fill text-primary me-2"></i>Tenaga Medis Aktif</h5>
                <div class="text-muted small">Total: <strong><?= count($dokter) ?></strong> Dokter</div>
            </div>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th style="width: 60px;">No</th>
                            <th>Nama Dokter & Spesialisasi</th>
                            <th>Poliklinik Bertugas</th>
                            <th>No. STR</th>
                            <th>No. HP</th>
                            <th>Akun Sistem</th>
                            <?php if ($role === 'admin'): ?>
                                <th class="text-end" style="width: 150px;">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($dokter)): ?>
                            <tr>
                                <td colspan="<?= $role === 'admin' ? 7 : 6 ?>" class="text-center py-4 text-muted">
                                    Belum ada data dokter atau gagal mengambil dari API Server B.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($dokter as $d): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-circle m-0" style="width:36px; height:36px; font-size:15px;">
                                                <?= strtoupper(substr($d['nama_dokter'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark"><?= escape($d['nama_dokter']) ?></div>
                                                <small class="text-primary fw-semibold"><?= escape($d['spesialisasi'] ?? '-') ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-building me-1 text-primary"></i><?= escape($d['nama_poli']) ?>
                                        </span>
                                    </td>
                                    <td><small class="text-muted"><?= escape($d['no_str'] ?? '-') ?></small></td>
                                    <td><small><i class="bi bi-telephone text-muted me-1"></i><?= escape($d['no_hp'] ?? '-') ?></small></td>
                                    <td>
                                        <?php if (!empty($d['username'])): ?>
                                            <span class="badge bg-success-subtle text-success border border-success">
                                                <i class="bi bi-person-check me-1"></i><?= escape($d['username']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-subtle text-muted">Belum ada akun</span>
                                        <?php endif; ?>
                                    </td>
                                    <?php if ($role === 'admin'): ?>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= BASE_URL ?>/modules/dokter/edit.php?id=<?= $d['id_dokter'] ?>" class="btn btn-outline-secondary" title="Edit">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-danger" title="Hapus" 
                                                        onclick="confirmDelete('<?= BASE_URL ?>/modules/dokter/hapus.php?id=<?= $d['id_dokter'] ?>', 'Dokter <?= escape($d['nama_dokter']) ?>')">
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
