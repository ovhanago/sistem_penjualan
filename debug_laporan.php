<?php
require_once "config/koneksi.php";
$today = date('Y-m-d');
$query_str = "SELECT pesanan.*, user.nama_user 
              FROM pesanan 
              JOIN user ON pesanan.id_user = user.id_user 
              WHERE DATE(pesanan.tanggal_pesanan) = '$today'
              ORDER BY tanggal_pesanan DESC";

echo "DEBUG INFO:\n";
echo "PHP Today Date: $today\n";
echo "SQL Query: $query_str\n\n";

$result = mysqli_query($conn, $query_str);
if (!$result) {
    echo "Query Error: " . mysqli_error($conn) . "\n";
} else {
    echo "Found: " . mysqli_num_rows($result) . " rows\n";
    while($row = mysqli_fetch_assoc($result)) {
        echo "ID: " . $row['id_pesanan'] . " | Date: " . $row['tanggal_pesanan'] . " | Status: " . $row['status_pesanan'] . "\n";
    }
}

// Cek semua data tanpa filter untuk perbandingan
echo "\nAll Data (Last 5):\n";
$all = mysqli_query($conn, "SELECT id_pesanan, tanggal_pesanan FROM pesanan ORDER BY id_pesanan DESC LIMIT 5");
while($row = mysqli_fetch_assoc($all)) {
    echo "ID: " . $row['id_pesanan'] . " | Date: " . $row['tanggal_pesanan'] . " (DATE() says: " . substr($row['tanggal_pesanan'], 0, 10) . ")\n";
}
?>