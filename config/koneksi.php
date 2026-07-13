<?php
date_default_timezone_set('Asia/Jakarta');

// ===========================
// KONEKSI DATABASE
// ===========================

// Jika di Railway gunakan Environment Variable
$host = getenv('MYSQLHOST') ?: 'tokaido.proxy.rlwy.net';
$user = getenv('MYSQLUSER') ?: 'root';
$pass = getenv('MYSQLPASSWORD') ?: 'dozzTICemZamDMVwHRACFmtcpxThDAFd';
$db   = getenv('MYSQLDATABASE') ?: 'railway';
$port = getenv('MYSQLPORT') ?: '54520';

$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    die("Koneksi Database Gagal : " . mysqli_connect_error());
}

// ===========================
// MIDTRANS CONFIG
// ===========================

// Pastikan folder midtrans ada
require_once __DIR__ . '/../midtrans/Midtrans.php';

// Sandbox
define('MIDTRANS_SERVER_KEY', 'Mid-server-TLS78JA1HFZfVSRUIZwVvw7n');
define('MIDTRANS_CLIENT_KEY', 'Mid-client-x79PhR-jL9fUkJdY');
define('MIDTRANS_IS_PRODUCTION', false);

// Konfigurasi Midtrans
\Midtrans\Config::$serverKey = MIDTRANS_SERVER_KEY;
\Midtrans\Config::$clientKey = MIDTRANS_CLIENT_KEY;
\Midtrans\Config::$isProduction = MIDTRANS_IS_PRODUCTION;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// Mengatasi SSL Sandbox
\Midtrans\Config::$curlOptions = [
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false
];