<?php
/**
 * Halaman Akses Ditolak (403 Forbidden)
 */

$page_title = 'Akses Ditolak';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<div class="app-main">
    <?php require_once __DIR__ . '/../includes/navbar.php'; ?>
    <main class="app-content d-flex align-items-center justify-content-center" style="min-height: 80vh;">
        <div class="text-center p-5 bg-white rounded-4 shadow-sm border max-w-600 mx-auto">
            <div class="mb-4">
                <span class="badge bg-danger-subtle text-danger p-3 rounded-circle">
                    <i class="bi bi-shield-x fs-1"></i>
                </span>
            </div>
            <h3 class="fw-bold text-dark">403 - Akses Ditolak</h3>
            <p class="text-muted mb-4">
                Maaf, peran akun Anda (<strong><?= escape(current_role()) ?></strong>) tidak memiliki hak akses atau izin untuk membuka halaman ini.
            </p>
            <div class="d-flex justify-content-center gap-2">
                <a href="<?= BASE_URL ?>/dashboard.php" class="btn btn-primary px-4 fw-semibold">
                    <i class="bi bi-house me-1"></i> Kembali ke Dashboard
                </a>
                <a href="<?= BASE_URL ?>/logout.php" class="btn btn-outline-danger px-3">
                    <i class="bi bi-box-arrow-right me-1"></i> Ganti Akun
                </a>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
