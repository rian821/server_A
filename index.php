<?php
/**
 * Landing Page Publik
 * Menampilkan profil RS, daftar poliklinik dan dokter dari API Server B
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/helpers/api.php';
require_once __DIR__ . '/helpers/auth.php';

// Ambil data poliklinik dan dokter dari Web Service Server B
$poli_res   = call_api('GET', '/poliklinik/index.php');
$dokter_res = call_api('GET', '/dokter/index.php');

$poliklinik = $poli_res['status'] ? ($poli_res['data'] ?? []) : [];
$dokter     = $dokter_res['status'] ? ($dokter_res['data'] ?? []) : [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - Layanan Kesehatan Berbasis SOA</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <style>
        .navbar-landing {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        }
        .hero-banner {
            background: linear-gradient(135deg, #09203f 0%, #1e3a8a 50%, #0284c7 100%);
            color: #fff;
            padding: 100px 0 80px 0;
            position: relative;
        }
        .hero-badge {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.25);
            padding: 6px 16px;
            border-radius: 30px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
        }
        .poli-card {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            transition: all 0.3s;
            background: #fff;
            height: 100%;
        }
        .poli-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.08);
            border-color: var(--primary);
        }
        .doctor-card {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            background: #fff;
            overflow: hidden;
            transition: all 0.3s;
            height: 100%;
        }
        .doctor-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.08);
        }
        .avatar-circle {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0284c7, #0d9488);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: 700;
            margin: 0 auto 15px auto;
        }
    </style>
</head>
<body>

<!-- Navbar Landing -->
<nav class="navbar navbar-expand-lg navbar-landing sticky-top py-3">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="#">
            <div class="brand-icon" style="width:36px; height:36px; font-size:18px;">
                <i class="bi bi-hospital"></i>
            </div>
            <div>
                <span class="fw-bold text-dark fs-5"><?= APP_NAME ?></span>
            </div>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navLanding">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navLanding">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link fw-semibold px-3 text-dark" href="#beranda">Beranda</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold px-3 text-dark" href="#poliklinik">Poliklinik</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold px-3 text-dark" href="#dokter">Dokter Spesialis</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold px-3 text-dark" href="#keunggulan">Fasilitas</a></li>
            </ul>
            <div class="d-flex gap-2">
                <?php if (is_logged_in()): ?>
                    <a href="<?= BASE_URL ?>/dashboard.php" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="bi bi-grid-fill"></i> Buka Dashboard
                    </a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/login.php" class="btn btn-outline-primary fw-semibold px-4">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Masuk / Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section id="beranda" class="hero-banner">
    <div class="container">
        <div class="row align-items-center gy-5">
            <div class="col-lg-7">
                <div class="hero-badge mb-3">
                    <i class="bi bi-shield-check text-warning"></i>
                    <span>Arsitektur SOA (Server A &bull; Server B)</span>
                </div>
                <h1 class="display-4 fw-extrabold mb-3 lh-sm">
                    Pelayanan Kesehatan Modern, <br><span class="text-info">Cepat & Terintegrasi</span>
                </h1>
                <p class="lead text-light opacity-90 mb-4">
                    Memberikan pelayanan medis unggulan dengan kemudahan pendaftaran online, 
                    akses rekam medis instan, dan tenaga medis spesialis terbaik untuk Anda dan keluarga.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <?php if (is_logged_in()): ?>
                        <a href="<?= BASE_URL ?>/dashboard.php" class="btn btn-light btn-lg text-primary fw-bold px-4 py-3 shadow">
                            <i class="bi bi-speedometer2 me-2"></i> Ke Menu Utama
                        </a>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>/login.php" class="btn btn-light btn-lg text-primary fw-bold px-4 py-3 shadow">
                            <i class="bi bi-calendar-check me-2"></i> Buat Kunjungan / Login
                        </a>
                    <?php endif; ?>
                    <a href="#poliklinik" class="btn btn-outline-light btn-lg px-4 py-3">
                        <i class="bi bi-building me-2"></i> Lihat Poliklinik
                    </a>
                </div>
            </div>
            <div class="col-lg-5 text-center">
                <div class="bg-white p-4 rounded-4 shadow-lg text-dark text-start">
                    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
                        <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-hdd-network me-2"></i>Status Arsitektur SOA</h6>
                        <span class="badge bg-success-subtle text-success border border-success">Terhubung</span>
                    </div>
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
                        <li class="d-flex justify-content-between">
                            <span class="text-muted"><i class="bi bi-laptop me-1"></i> Server A (UI):</span>
                            <span class="fw-semibold">192.168.1.2</span>
                        </li>
                        <li class="d-flex justify-content-between">
                            <span class="text-muted"><i class="bi bi-server me-1"></i> Server B (API & DB):</span>
                            <span class="fw-semibold">192.168.1.10</span>
                        </li>
                        <li class="d-flex justify-content-between">
                            <span class="text-muted"><i class="bi bi-shield-lock me-1"></i> Autentikasi:</span>
                            <span class="fw-semibold text-success">Bearer Token (RBAC)</span>
                        </li>
                        <li class="d-flex justify-content-between">
                            <span class="text-muted"><i class="bi bi-database me-1"></i> Data API:</span>
                            <span class="fw-semibold text-primary"><?= count($poliklinik) ?> Poli &bull; <?= count($dokter) ?> Dokter</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section Poliklinik -->
<section id="poliklinik" class="py-5 bg-white">
    <div class="container py-4">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-semibold mb-2">Layanan Kami</span>
            <h2 class="fw-bold">Poliklinik Spesialis</h2>
            <p class="text-muted">Pilihan poli medis lengkap yang siap menangani keluhan kesehatan Anda dengan fasilitas terbaik.</p>
        </div>

        <div class="row g-4">
            <?php if (empty($poliklinik)): ?>
                <div class="col-12 text-center text-muted py-4">
                    <i class="bi bi-exclamation-circle fs-2 text-warning d-block mb-2"></i>
                    Belum dapat memuat data poliklinik dari Server B. Pastikan API Server B aktif.
                </div>
            <?php else: ?>
                <?php foreach ($poliklinik as $poli): ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="poli-card p-4">
                            <div class="feature-icon mb-3">
                                <i class="bi bi-hospital"></i>
                            </div>
                            <h5 class="fw-bold mb-1"><?= escape($poli['nama_poli']) ?></h5>
                            <p class="small text-primary fw-semibold mb-2">
                                <i class="bi bi-geo-alt me-1"></i><?= escape($poli['lokasi'] ?? 'Gedung Utama') ?>
                            </p>
                            <p class="text-muted small mb-3"><?= escape($poli['keterangan'] ?? 'Pelayanan poliklinik terpadu.') ?></p>
                            <div class="border-top pt-2 d-flex justify-content-between align-items-center">
                                <small class="text-muted"><i class="bi bi-person-badge me-1"></i><?= (int)($poli['total_dokter'] ?? 0) ?> Dokter</small>
                                <a href="<?= BASE_URL ?>/login.php" class="btn btn-sm btn-light text-primary fw-semibold">Daftar</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Section Dokter -->
<section id="dokter" class="py-5 bg-light">
    <div class="container py-4">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="badge bg-info-subtle text-info px-3 py-2 rounded-pill fw-semibold mb-2">Tim Medis</span>
            <h2 class="fw-bold">Dokter Berpengalaman</h2>
            <p class="text-muted">Ditangani langsung oleh dokter spesialis bersertifikasi dan berdedikasi tinggi.</p>
        </div>

        <div class="row g-4">
            <?php if (empty($dokter)): ?>
                <div class="col-12 text-center text-muted py-4">
                    Data dokter sedang tidak tersedia.
                </div>
            <?php else: ?>
                <?php foreach ($dokter as $dok): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="doctor-card p-4 text-center">
                            <div class="avatar-circle">
                                <?= strtoupper(substr($dok['nama_dokter'], 0, 1)) ?>
                            </div>
                            <h5 class="fw-bold mb-1"><?= escape($dok['nama_dokter']) ?></h5>
                            <span class="badge bg-primary-subtle text-primary mb-2"><?= escape($dok['spesialisasi'] ?? 'Spesialis') ?></span>
                            <p class="text-muted small mb-3">
                                <i class="bi bi-building me-1"></i><?= escape($dok['nama_poli'] ?? '-') ?>
                            </p>
                            <div class="bg-light p-2 rounded-3 text-start small mb-3">
                                <div><i class="bi bi-card-text text-muted me-2"></i>No. STR: <strong><?= escape($dok['no_str'] ?? '-') ?></strong></div>
                                <div><i class="bi bi-telephone text-muted me-2"></i>Kontak: <strong><?= escape($dok['no_hp'] ?? '-') ?></strong></div>
                            </div>
                            <a href="<?= BASE_URL ?>/login.php" class="btn btn-outline-primary btn-sm w-100 fw-semibold">
                                Buat Janji Konsultasi
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Section Keunggulan -->
<section id="keunggulan" class="py-5 bg-white">
    <div class="container py-4">
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="p-4 rounded-4 border bg-light h-100">
                    <i class="bi bi-shield-check text-primary fs-1 mb-3 d-inline-block"></i>
                    <h5 class="fw-bold">Jaminan Asuransi & BPJS</h5>
                    <p class="text-muted small mb-0">Mendukung integrasi BPJS Kesehatan, Prudential, Mandiri Inhealth, dan pasien umum tanpa antre panjang.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 rounded-4 border bg-light h-100">
                    <i class="bi bi-journal-medical text-success fs-1 mb-3 d-inline-block"></i>
                    <h5 class="fw-bold">Rekam Medis Terpadu</h5>
                    <p class="text-muted small mb-0">Riwayat keluhan, diagnosa, dan tindakan medis tersimpan rapi dan dapat diakses dengan keamanan tinggi.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 rounded-4 border bg-light h-100">
                    <i class="bi bi-clock-history text-warning fs-1 mb-3 d-inline-block"></i>
                    <h5 class="fw-bold">Antrean & Jadwal Cepat</h5>
                    <p class="text-muted small mb-0">Pendaftaran kunjungan mandiri untuk pasien melalui portal digital yang mudah dan transparan.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer Landing -->
<footer class="bg-dark text-white py-5">
    <div class="container">
        <div class="row g-4 mb-4">
            <div class="col-lg-5">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="brand-icon" style="width:36px; height:36px; font-size:18px;">
                        <i class="bi bi-hospital"></i>
                    </div>
                    <h5 class="mb-0 fw-bold"><?= APP_NAME ?></h5>
                </div>
                <p class="text-secondary small">
                    Sistem Informasi Rumah Sakit berbasis Service-Oriented Architecture (SOA). 
                    Pemisahan Server A (Klien UI) dan Server B (Web Service & Database) untuk kehandalan, keamanan, dan skalabilitas tinggi.
                </p>
            </div>
            <div class="col-lg-3 col-6">
                <h6 class="fw-bold mb-3 text-light">Tautan Cepat</h6>
                <ul class="list-unstyled text-secondary small d-flex flex-column gap-2">
                    <li><a href="#poliklinik" class="text-secondary text-decoration-none">Poliklinik</a></li>
                    <li><a href="#dokter" class="text-secondary text-decoration-none">Jadwal Dokter</a></li>
                    <li><a href="<?= BASE_URL ?>/login.php" class="text-secondary text-decoration-none">Portal Login</a></li>
                </ul>
            </div>
            <div class="col-lg-4 col-6">
                <h6 class="fw-bold mb-3 text-light">Informasi Jaringan</h6>
                <ul class="list-unstyled text-secondary small d-flex flex-column gap-2">
                    <li><i class="bi bi-pc-display me-2"></i> Server A UI: 192.168.1.2</li>
                    <li><i class="bi bi-hdd-rack me-2"></i> Server B API: 192.168.1.10</li>
                    <li><i class="bi bi-lock me-2"></i> Protokol: HTTP JSON REST SOA</li>
                </ul>
            </div>
        </div>
        <div class="border-top border-secondary pt-3 text-center text-secondary small">
            &copy; <?= date('Y') ?> <?= APP_NAME ?> &bull; Tugas Praktikum Service-Oriented Architecture (SOA).
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
