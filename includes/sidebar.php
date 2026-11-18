<?php
/**
 * Sidebar Navigation (Berbasis RBAC: Admin, Dokter, Pasien)
 */
$role = current_role();
$user = current_user();

// Helper untuk menandai menu aktif
$curr_uri = $_SERVER['REQUEST_URI'];
function is_active($keyword) {
    global $curr_uri;
    return strpos($curr_uri, $keyword) !== false ? 'active' : '';
}
?>
<aside class="app-sidebar no-print">
    <!-- Brand -->
    <a href="<?= BASE_URL ?>/dashboard.php" class="sidebar-brand">
        <div class="brand-icon">
            <i class="bi bi-hospital"></i>
        </div>
        <div class="brand-text">
            <h5><?= APP_NAME ?></h5>
            <span>SOA Client App</span>
        </div>
    </a>

    <!-- User Profile Badge -->
    <div class="sidebar-user">
        <div class="user-avatar">
            <?= strtoupper(substr($user['nama_lengkap'] ?? 'U', 0, 1)) ?>
        </div>
        <div class="user-info">
            <div class="name" title="<?= escape($user['nama_lengkap'] ?? '') ?>"><?= escape($user['nama_lengkap'] ?? 'User') ?></div>
            <span class="role-badge role-<?= $role ?>"><?= escape($role) ?></span>
        </div>
    </div>

    <!-- Navigation Menus -->
    <div class="sidebar-nav">
        <div class="nav-header">Menu Utama</div>
        <ul class="sidebar-menu">
            <li>
                <a href="<?= BASE_URL ?>/dashboard.php" class="<?= is_active('dashboard.php') ?>">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- MENU KHUSUS ADMIN -->
            <?php if ($role === 'admin'): ?>
                <div class="nav-header">Master Data</div>
                <li>
                    <a href="<?= BASE_URL ?>/modules/poliklinik/index.php" class="<?= is_active('/poliklinik/') ?>">
                        <i class="bi bi-building"></i>
                        <span>Poliklinik</span>
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/modules/dokter/index.php" class="<?= is_active('/dokter/') ?>">
                        <i class="bi bi-person-badge"></i>
                        <span>Data Dokter</span>
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/modules/pasien/index.php" class="<?= is_active('/pasien/') ?>">
                        <i class="bi bi-people-fill"></i>
                        <span>Data Pasien</span>
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/modules/asuransi/index.php" class="<?= is_active('/asuransi/') ?>">
                        <i class="bi bi-shield-check"></i>
                        <span>Data Asuransi</span>
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/modules/pengguna/index.php" class="<?= is_active('/pengguna/') ?>">
                        <i class="bi bi-person-gear"></i>
                        <span>Kelola Pengguna</span>
                    </a>
                </li>

                <div class="nav-header">Pelayanan & Laporan</div>
                <li>
                    <a href="<?= BASE_URL ?>/modules/kunjungan/index.php" class="<?= is_active('/kunjungan/') ?>">
                        <i class="bi bi-clipboard2-pulse-fill"></i>
                        <span>Kelola Kunjungan</span>
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/modules/laporan/index.php" class="<?= is_active('/laporan/') ?>">
                        <i class="bi bi-file-earmark-bar-graph-fill"></i>
                        <span>Pusat Laporan</span>
                    </a>
                </li>

            <!-- MENU KHUSUS DOKTER -->
            <?php elseif ($role === 'dokter'): ?>
                <div class="nav-header">Layanan Medis</div>
                <li>
                    <a href="<?= BASE_URL ?>/modules/kunjungan/index.php" class="<?= is_active('/kunjungan/') ?>">
                        <i class="bi bi-clipboard2-pulse"></i>
                        <span>Antrean & Pemeriksaan</span>
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/modules/pasien/index.php" class="<?= is_active('/pasien/') ?>">
                        <i class="bi bi-person-lines-fill"></i>
                        <span>Pasien Saya</span>
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/modules/poliklinik/index.php" class="<?= is_active('/poliklinik/') ?>">
                        <i class="bi bi-building"></i>
                        <span>Daftar Poliklinik</span>
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/modules/dokter/profil.php" class="<?= is_active('/dokter/profil.php') ?>">
                        <i class="bi bi-person-bounding-box"></i>
                        <span>Profil Dokter</span>
                    </a>
                </li>
                <div class="nav-header">Laporan</div>
                <li>
                    <a href="<?= BASE_URL ?>/modules/laporan/kunjungan_periode.php" class="<?= is_active('kunjungan_periode.php') ?>">
                        <i class="bi bi-calendar2-range"></i>
                        <span>Laporan Kunjungan</span>
                    </a>
                </li>

            <!-- MENU KHUSUS PASIEN -->
            <?php elseif ($role === 'pasien'): ?>
                <div class="nav-header">Pasien Mandiri</div>
                <li>
                    <a href="<?= BASE_URL ?>/modules/kunjungan/daftar.php" class="<?= is_active('/kunjungan/daftar.php') ?>">
                        <i class="bi bi-plus-circle-fill"></i>
                        <span>Daftar Kunjungan</span>
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/modules/kunjungan/index.php" class="<?= is_active('/kunjungan/index.php') ?>">
                        <i class="bi bi-clock-history"></i>
                        <span>Riwayat Kunjungan</span>
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/modules/pasien/profil.php" class="<?= is_active('/pasien/profil.php') ?>">
                        <i class="bi bi-person-vcard"></i>
                        <span>Profil Pasien</span>
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/modules/dokter/index.php" class="<?= is_active('/dokter/') ?>">
                        <i class="bi bi-people"></i>
                        <span>Jadwal Dokter</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Footer Logout -->
    <div class="sidebar-footer">
        <a href="<?= BASE_URL ?>/logout.php" class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-box-arrow-right"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>
