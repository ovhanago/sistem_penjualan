<?php
$host = "autorack.proxy.rlwy.net";
$user = "root";
$pass = "fRZeyFbOpGLeEpFeKoCbbKzbFxMJmmGv";
$db   = "railway";
$port = 11251;

date_default_timezone_set('Asia/Jakarta');

$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// 1. Panggil Library Midtrans PHP SDK
require_once dirname(__FILE__) . '/../midtrans/Midtrans.php';

// 2. Konfigurasi Midtrans (SANDBOX / TESTING)
define('MIDTRANS_SERVER_KEY', 'Mid-server-TLS78JA1HFZfVSRUIZwVvw7n');
define('MIDTRANS_CLIENT_KEY', 'Mid-client-x79PhR-jL9fUkJdY');
define('MIDTRANS_IS_PRODUCTION', false);

// 3. Inisialisasi Konfigurasi SDK
\Midtrans\Config::$serverKey    = MIDTRANS_SERVER_KEY;
\Midtrans\Config::$clientKey    = MIDTRANS_CLIENT_KEY;
\Midtrans\Config::$isProduction = MIDTRANS_IS_PRODUCTION;
\Midtrans\Config::$isSanitized  = true;
\Midtrans\Config::$is3ds        = true;

\Midtrans\Config::$curlOptions = [CURLOPT_SSL_VERIFYPEER => false];
?>