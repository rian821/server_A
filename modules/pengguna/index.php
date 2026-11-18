<?php
/**
 * Modul Pengguna - Kelola Akun & Hak Akses (RBAC)
 * Role: Admin
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role(['admin']);

$page_title = 'Kelola Pengguna';

// Ambil daftar pengguna dari API Server B
$res = call_api('GET', '/pengguna/index.php');
$pengguna = $res['status'] ? ($res['data'] ?? []) : [];

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="app-main">
    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>

    <main class="app-content">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h4 class="fw-bold mb-1">Kelola Pengguna & RBAC</h4>
                <p class="text-muted small mb-0">Manajemen akun pengguna sistem: Admin, Dokter, dan Pasien</p>
            </div>
            <a href="<?= BASE_URL ?>/modules/pengguna/tambah.php" class="btn btn-primary fw-semibold d-flex align-items-center gap-2">
                <i class="bi bi-person-plus-fill"></i> Tambah Pengguna Baru
            </a>
        </div>

        <div class="card-table">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-people-fill text-primary me-2"></i>Daftar Akun Pengguna</h5>
                <div class="text-muted small">Total: <strong><?= count($pengguna) ?></strong> Akun</div>
            </div>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th style="width: 60px;">No</th>
                            <th>Nama Lengkap</th>
                            <th>Username</th>
                            <th>Peran (Role)</th>
                            <th>Status Akun</th>
                            <th>Terdaftar Pada</th>
                            <th class="text-end" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pengguna)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Belum ada akun pengguna atau gagal terhubung ke Server B.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($pengguna as $u): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td class="fw-bold text-dark">
                                        <i class="bi bi-person-circle text-primary me-2"></i><?= escape($u['nama_lengkap']) ?>
                                    </td>
                                    <td><code><?= escape($u['username']) ?></code></td>
                                    <td>
                                        <span class="role-badge role-<?= $u['role'] ?> px-2 py-1">
                                            <?= strtoupper($u['role']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $u['status'] === 'aktif' ? 'success' : 'danger' ?>-subtle text-<?= $u['status'] === 'aktif' ? 'success' : 'danger' ?> border">
                                            <i class="bi bi-circle-fill me-1" style="font-size: 6px;"></i><?= ucfirst($u['status']) ?>
                                        </span>
                                    </td>
                                    <td><small class="text-muted"><?= date('d M Y H:i', strtotime($u['created_at'])) ?></small></td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= BASE_URL ?>/modules/pengguna/edit.php?id=<?= $u['id_pengguna'] ?>" class="btn btn-outline-secondary" title="Edit / Reset Password">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <?php if ((int)$u['id_pengguna'] !== (int)current_user()['id_pengguna']): ?>
                                                <button type="button" class="btn btn-outline-danger" title="Hapus" 
                                                        onclick="confirmDelete('<?= BASE_URL ?>/modules/pengguna/hapus.php?id=<?= $u['id_pengguna'] ?>', 'Akun <?= escape($u['username']) ?>')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
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
