<?php
require_once "config/koneksi.php";
$query = mysqli_query($conn, "SELECT id_pesanan, tanggal_pesanan, total_harga, status_pesanan FROM pesanan ORDER BY id_pesanan DESC LIMIT 5");
echo "Latest orders:\n";
while($row = mysqli_fetch_assoc($query)) {
    print_r($row);
}

$query_today = mysqli_query($conn, "SELECT COUNT(*) as count FROM pesanan WHERE DATE(tanggal_pesanan) = CURDATE()");
$data_today = mysqli_fetch_assoc($query_today);
echo "\nOrders matching CURDATE() (" . date('Y-m-d') . "): " . $data_today['count'] . "\n";

$query_server_time = mysqli_query($conn, "SELECT NOW() as now, CURDATE() as curdate");
$server_time = mysqli_fetch_assoc($query_server_time);
echo "\nMySQL Server Time (NOW()): " . $server_time['now'] . "\n";
echo "MySQL Server Date (CURDATE()): " . $server_time['curdate'] . "\n";
echo "PHP Server Time: " . date('Y-m-d H:i:s') . "\n";
?>