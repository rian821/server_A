<?php
/**
 * Konfigurasi Utama Klien UI (Server A)
 * Sistem Informasi Rumah Sakit Berbasis SOA
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// IP Server B (API & Database Server)
// Default PRD: http://192.168.1.10/rs_api
// Jika menguji di 1 laptop yang sama sebelum dipisah, bisa ganti sementara ke http://localhost/rs_api
define('BASE_API', 'http://192.168.1.10/rs_api');

// Nama Aplikasi
define('APP_NAME', 'RS Sehat Medika');
define('APP_TAGLINE', 'Sistem Informasi Rumah Sakit Berbasis SOA');

// URL Base Klien
// Mendeteksi otomatis base URL folder rs_client
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$script_name = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

// Cari posisi folder rs_client dalam URL
$pos = strpos($script_name, '/rs_client');
if ($pos !== false) {
    $client_path = substr($script_name, 0, $pos + strlen('/rs_client'));
} else {
    $client_path = '/rs_client';
}

define('BASE_URL', rtrim($protocol . $host . $client_path, '/'));
