<?php
/**
 * Halaman Login Pengguna
 * Server A (Klien UI)
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/helpers/api.php';
require_once __DIR__ . '/helpers/auth.php';

// Jika sudah login, langsung ke dashboard
if (is_logged_in()) {
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit;
}

$error_msg = get_flash('error');
$success_msg = get_flash('success');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error_msg = 'Username dan password wajib diisi.';
    } else {
        // Panggil endpoint login di Server B via SOA cURL
        $response = call_api('POST', '/auth/login.php', [
            'username' => $username,
            'password' => $password
        ]);

        if ($response['status'] && !empty($response['data']['token'])) {
            // Simpan sesi autentikasi RBAC di Server A
            $_SESSION['token'] = $response['data']['token'];
            $_SESSION['user']  = $response['data']['user'];
            $_SESSION['flash_success'] = 'Selamat datang, ' . $response['data']['user']['nama_lengkap'] . '!';

            header('Location: ' . BASE_URL . '/dashboard.php');
            exit;
        } else {
            $error_msg = $response['message'] ?? 'Login gagal. Periksa kembali username dan password Anda.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= APP_NAME ?></title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 50%, #f8fafc 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .login-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 40px -15px rgba(2, 132, 199, 0.15);
            border: 1px solid #e2e8f0;
            overflow: hidden;
            max-width: 900px;
            width: 100%;
        }
        .login-banner {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 50%, #0f172a 100%);
            color: #ffffff;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .demo-chip {
            cursor: pointer;
            transition: all 0.2s;
            font-size: 12px;
        }
        .demo-chip:hover {
            transform: scale(1.03);
            filter: brightness(0.95);
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="row g-0">
        <!-- Banner Kolom Kiri -->
        <div class="col-lg-5 login-banner d-none d-lg-flex">
            <div>
                <a href="<?= BASE_URL ?>/index.php" class="text-white text-decoration-none d-inline-flex align-items-center gap-2 mb-4">
                    <i class="bi bi-arrow-left"></i>
                    <span>Kembali ke Beranda</span>
                </a>
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="brand-icon" style="background: rgba(255,255,255,0.2);">
                        <i class="bi bi-hospital"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 fw-bold"><?= APP_NAME ?></h4>
                        <small class="opacity-75">Sistem Informasi SOA</small>
                    </div>
                </div>
                <h3 class="fw-bold mt-4">Akses Terintegrasi Layanan Medis</h3>
                <p class="opacity-80 small">
                    Sistem portal RBAC terpadu untuk Administrator Rumah Sakit, Dokter Spesialis, dan Pasien.
                </p>
            </div>

            <div class="bg-white bg-opacity-10 p-3 rounded-3 small">
                <div class="d-flex align-items-center gap-2 text-warning mb-1">
                    <i class="bi bi-hdd-network"></i>
                    <strong class="text-white">Arsitektur Jaringan SOA:</strong>
                </div>
                <div>Server A (Klien UI): <strong>192.168.1.2</strong></div>
                <div>Server B (Web Service): <strong>192.168.1.10</strong></div>
            </div>
        </div>

        <!-- Form Kolom Kanan -->
        <div class="col-lg-7 p-4 p-md-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Masuk ke Akun</h3>
                    <p class="text-muted small mb-0">Masukkan username dan password Anda</p>
                </div>
                <a href="<?= BASE_URL ?>/index.php" class="btn btn-sm btn-outline-secondary d-lg-none">
                    <i class="bi bi-arrow-left"></i>
                </a>
            </div>

            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger d-flex align-items-center gap-2 py-2 small" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <div><?= escape($error_msg) ?></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($success_msg)): ?>
                <div class="alert alert-success d-flex align-items-center gap-2 py-2 small" role="alert">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                    <div><?= escape($success_msg) ?></div>
                </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/login.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label fw-semibold small">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username" required autofocus>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label fw-semibold small">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm">
                    <i class="bi bi-box-arrow-in-right"></i> Masuk Sekarang
                </button>
            </form>

            <!-- Akun Uji Coba Cepat (Mudah untuk Pengujian Dosen) -->
            <div class="mt-4 pt-4 border-top">
                <small class="text-muted fw-bold d-block mb-2 text-uppercase" style="font-size: 11px;">
                    <i class="bi bi-lightning-charge text-warning"></i> Akun Demo / Uji Coba (Klik untuk mengisi):
                </small>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-danger-subtle text-danger border border-danger demo-chip py-2 px-3" onclick="fillLogin('admin', 'admin123')">
                        <i class="bi bi-shield-lock me-1"></i> Admin (admin / admin123)
                    </span>
                    <span class="badge bg-info-subtle text-info border border-info demo-chip py-2 px-3" onclick="fillLogin('dr_andi', 'dokter123')">
                        <i class="bi bi-heart-pulse me-1"></i> Dokter (dr_andi / dokter123)
                    </span>
                    <span class="badge bg-success-subtle text-success border border-success demo-chip py-2 px-3" onclick="fillLogin('ahmad', 'pasien123')">
                        <i class="bi bi-person me-1"></i> Pasien (ahmad / pasien123)
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function fillLogin(user, pass) {
    document.getElementById('username').value = user;
    document.getElementById('password').value = pass;
}
</script>

</body>
</html>
