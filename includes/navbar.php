<?php
/**
 * Top Navbar Component
 */
$user = current_user();
?>
<nav class="app-navbar no-print">
    <div class="d-flex align-items-center gap-3">
        <button class="btn btn-light d-lg-none border" id="sidebarToggle" type="button">
            <i class="bi bi-list fs-5"></i>
        </button>
        <div>
            <h5 class="mb-0 fw-bold text-dark"><?= escape($page_title ?? 'Sistem Informasi Rumah Sakit') ?></h5>
            <small class="text-muted d-none d-sm-inline">Architecture: Server A (UI) &rarr; Server B (SOA Web Service)</small>
        </div>
    </div>

    <div class="d-flex align-items-center gap-3">
        <!-- Badge Server B -->
        <div class="d-none d-md-flex align-items-center gap-2 bg-light px-3 py-1 rounded-pill border">
            <span class="spinner-grow spinner-grow-sm text-success" style="width: 8px; height: 8px;" role="status"></span>
            <small class="fw-semibold text-secondary">API: <?= escape(str_replace(['http://', '/rs_api'], '', BASE_API)) ?></small>
        </div>

        <!-- Quick User Profile -->
        <div class="dropdown">
            <button class="btn btn-light border dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle fs-5 text-primary"></i>
                <span class="d-none d-md-inline fw-semibold"><?= escape($user['nama_lengkap'] ?? 'Akun') ?></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                <li class="px-3 py-2 border-bottom">
                    <p class="mb-0 fw-bold"><?= escape($user['nama_lengkap'] ?? '') ?></p>
                    <small class="text-muted">Role: <?= strtoupper($user['role'] ?? '') ?></small>
                </li>
                <li><a class="dropdown-item py-2" href="<?= BASE_URL ?>/dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                <?php if (current_role() === 'dokter'): ?>
                    <li><a class="dropdown-item py-2" href="<?= BASE_URL ?>/modules/dokter/profil.php"><i class="bi bi-person-vcard me-2"></i> Profil Dokter</a></li>
                <?php elseif (current_role() === 'pasien'): ?>
                    <li><a class="dropdown-item py-2" href="<?= BASE_URL ?>/modules/pasien/profil.php"><i class="bi bi-person-vcard me-2"></i> Profil Pasien</a></li>
                <?php endif; ?>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item py-2 text-danger" href="<?= BASE_URL ?>/logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>
