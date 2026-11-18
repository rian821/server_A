<?php
/**
 * Helper cURL Terpusat untuk Komunikasi SOA ke Server B
 * Server A tidak mengakses database langsung, melainkan lewat helper ini.
 */

require_once __DIR__ . '/../config/config.php';

function call_api($method, $endpoint, $data = null, $custom_token = null) {
    $method = strtoupper($method);
    $url = rtrim(BASE_API, '/') . '/' . ltrim($endpoint, '/');

    // Jika metode GET dan ada parameter array $data
    if ($method === 'GET' && !empty($data) && is_array($data)) {
        $queryString = http_build_query($data);
        $url .= (strpos($url, '?') !== false ? '&' : '?') . $queryString;
    }

    $ch = curl_init();

    $headers = [
        'Content-Type: application/json',
        'Accept: application/json'
    ];

    // Ambil token dari sesi atau parameter khusus
    $token = $custom_token ?? ($_SESSION['token'] ?? null);
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout 10 detik
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

    switch ($method) {
        case 'POST':
            curl_setopt($ch, CURLOPT_POST, true);
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
            break;

        case 'PUT':
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
            break;

        case 'DELETE':
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
            break;

        case 'GET':
        default:
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            break;
    }

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    // Jika server B tidak dapat dijangkau / koneksi timeout
    if ($response === false) {
        return [
            'status'    => false,
            'message'   => 'Server layanan (Server B: ' . BASE_API . ') tidak dapat dihubungi. Pastikan Apache & jaringan Server B aktif. Error: ' . $curl_error,
            'data'      => null,
            'http_code' => 503
        ];
    }

    $decoded = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'status'    => false,
            'message'   => 'Respon dari Server B bukan format JSON yang valid. Respon mentah: ' . strip_tags(substr($response, 0, 200)),
            'data'      => null,
            'http_code' => $http_code
        ];
    }

    // Jika token kedaluwarsa atau ditolak (401), arahkan ke login
    if ($http_code === 401 && !empty($_SESSION['token'])) {
        unset($_SESSION['token'], $_SESSION['user']);
        $_SESSION['flash_error'] = 'Sesi Anda telah kedaluwarsa. Silakan login kembali.';
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }

    $decoded['http_code'] = $http_code;
    return $decoded;
}
