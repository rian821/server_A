<?php
/**
 * Helper Autentikasi & RBAC di Klien (Server A)
 */

require_once __DIR__ . '/../config/config.php';

function is_logged_in() {
    return !empty($_SESSION['token']) && !empty($_SESSION['user']);
}

function current_user() {
    return $_SESSION['user'] ?? null;
}

function current_role() {
    return $_SESSION['user']['role'] ?? null;
}

function require_login() {
    if (!is_logged_in()) {
        $_SESSION['flash_error'] = 'Silakan login terlebih dahulu untuk mengakses halaman ini.';
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

function require_role($allowed_roles = []) {
    require_login();
    $role = current_role();
    if (!in_array($role, $allowed_roles)) {
        header('Location: ' . BASE_URL . '/modules/akses_ditolak.php');
        exit;
    }
}

function set_flash($type, $message) {
    $_SESSION['flash_' . $type] = $message;
}

function get_flash($type) {
    $key = 'flash_' . $type;
    if (isset($_SESSION[$key])) {
        $msg = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $msg;
    }
    return null;
}

function escape($str) {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}
