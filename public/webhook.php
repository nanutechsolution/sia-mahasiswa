<?php

/**
 * 🚀 SIMA-UNMARIS AUTOMATIC DEPLOYMENT WEBHOOK
 * Lokasi: /var/www/siaset/public/webhook.php
 */

// 1. PENGATURAN KEAMANAN (Ganti 'rahasia_anda' dengan kata sandi acak)
$secret = 'sima_unmaris_secret_123';
$path   = '/var/www/siaset'; // Path ke folder aplikasi

// 2. VALIDASI TANDA TANGAN DARI GITHUB
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';

if (!$signature) {
    http_response_code(403);
    die('Tidak ada tanda tangan keamanan.');
}

$payload = file_get_contents('php://input');
$hash = "sha256=" . hash_hmac('sha256', $payload, $secret);

if (!hash_equals($signature, $hash)) {
    http_response_code(403);
    die('Tanda tangan tidak cocok. Akses ditolak.');
}

// 3. EKSEKUSI PERINTAH DEPLOYMENT
echo "Sinyal diterima. Memulai update...\n";

// Jalankan skrip deploy.sh yang sudah kita buat sebelumnya
// 2>&1 digunakan agar error juga muncul di log
$output = shell_exec("cd {$path} && ./deploy.sh 2>&1");

// 4. CATAT LOG (Untuk pengecekan jika ada error)
file_put_contents("{$path}/storage/logs/webhook.log", "[" . date('Y-m-d H:i:s') . "]\n" . $output . "\n\n", FILE_APPEND);

echo "Update Selesai!";
