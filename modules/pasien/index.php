<?php
/**
 * Modul Pasien - Daftar Pasien
 * Role: Admin (CRUD), Dokter (Lihat Pasien)
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_login();

// Jika role pasien, arahkan langsung ke profil pasiennya sendiri
if (current_role() === 'pasien') {
    header('Location: ' . BASE_URL . '/modules/pasien/profil.php');
    exit;
}

$page_title = 'Data Pasien';
$role = current_role();

// Ambil data pasien dari API Server B
$res = call_api('GET', '/pasien/index.php');
$pasien = $res['status'] ? ($res['data'] ?? []) : [];

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="app-main">
    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>

    <main class="app-content">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h4 class="fw-bold mb-1">Data Pasien Terdaftar</h4>
                <p class="text-muted small mb-0">Database identitas rekam medis dan asuransi pasien</p>
            </div>
            <?php if ($role === 'admin'): ?>
                <a href="<?= BASE_URL ?>/modules/pasien/tambah.php" class="btn btn-primary fw-semibold d-flex align-items-center gap-2">
                    <i class="bi bi-person-plus-fill"></i> Tambah Pasien Baru
                </a>
            <?php endif; ?>
        </div>

        <div class="card-table">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-people-fill text-primary me-2"></i>Daftar Pasien</h5>
                <div class="text-muted small">Total: <strong><?= count($pasien) ?></strong> Pasien</div>
            </div>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>No. RM</th>
                            <th>Nama Pasien & NIK</th>
                            <th>Gender / Usia</th>
                            <th>Kontak & Alamat</th>
                            <th>Jaminan Asuransi</th>
                            <th>Akun Pengguna</th>
                            <th class="text-end" style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pasien)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    Belum ada data pasien atau gagal memuat data dari Server B.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pasien as $p): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-light text-dark border fw-bold"><?= escape($p['no_rm']) ?></span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= escape($p['nama_pasien']) ?></div>
                                        <small class="text-muted"><i class="bi bi-card-heading me-1"></i><?= escape($p['nik'] ?? '-') ?></small>
                                    </td>
                                    <td>
                                        <div>
                                            <span class="badge bg-<?= $p['jenis_kelamin'] === 'L' ? 'primary-subtle text-primary' : 'danger-subtle text-danger' ?> py-1 px-2">
                                                <?= $p['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?>
                                            </span>
                                        </div>
                                        <small class="text-muted">
                                            <?= !empty($p['tanggal_lahir']) ? date('d/m/Y', strtotime($p['tanggal_lahir'])) : '-' ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div><i class="bi bi-telephone text-muted me-1"></i><small><?= escape($p['no_hp'] ?? '-') ?></small></div>
                                        <small class="text-muted" title="<?= escape($p['alamat'] ?? '') ?>"><?= escape(substr($p['alamat'] ?? '-', 0, 25)) ?></small>
                                    </td>
                                    <td>
                                        <?php if (!empty($p['nama_asuransi'])): ?>
                                            <span class="badge bg-success-subtle text-success border border-success">
                                                <i class="bi bi-shield-check me-1"></i><?= escape($p['nama_asuransi']) ?>
                                            </span>
                                            <?php if (!empty($p['no_kartu_asuransi'])): ?>
                                                <div class="small text-muted font-monospace"><?= escape($p['no_kartu_asuransi']) ?></div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-subtle text-secondary">Pasien Umum</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($p['username'])): ?>
                                            <span class="badge bg-info-subtle text-info border border-info">
                                                <i class="bi bi-person me-1"></i><?= escape($p['username']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-muted border">Belum ada akun</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= BASE_URL ?>/modules/laporan/riwayat_pasien.php?id_pasien=<?= $p['id_pasien'] ?>" class="btn btn-outline-info" title="Lihat Rekam Medis">
                                                <i class="bi bi-journal-medical"></i>
                                            </a>
                                            <?php if ($role === 'admin'): ?>
                                                <a href="<?= BASE_URL ?>/modules/pasien/edit.php?id=<?= $p['id_pasien'] ?>" class="btn btn-outline-secondary" title="Edit">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-danger" title="Hapus" 
                                                        onclick="confirmDelete('<?= BASE_URL ?>/modules/pasien/hapus.php?id=<?= $p['id_pasien'] ?>', 'Pasien <?= escape($p['nama_pasien']) ?>')">
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
