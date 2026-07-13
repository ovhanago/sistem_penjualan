<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>PHP Berjalan</h2>";

require_once "config/koneksi.php";

echo "<br>Koneksi Database Berhasil";

$result = mysqli_query($conn, "SHOW TABLES");

echo "<h3>Daftar Tabel</h3>";

while($row = mysqli_fetch_array($result)){
    echo $row[0]."<br>";
}