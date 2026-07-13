<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "config/koneksi.php";

echo "<h2>PHP Berjalan</h2>";

if ($conn) {
    echo "<h3 style='color:green'>Koneksi Database Berhasil</h3>";
} else {
    echo "<h3 style='color:red'>Koneksi Database Gagal</h3>";
    die(mysqli_connect_error());
}

$result = mysqli_query($conn, "SHOW TABLES");

if (!$result) {
    die(mysqli_error($conn));
}

echo "<h3>Daftar Tabel</h3>";

while ($row = mysqli_fetch_array($result)) {
    echo $row[0] . "<br>";
}

echo "<hr>Selesai";