<?php
require_once "config/koneksi.php";

$id = $_GET['id'];

mysqli_query($conn,"DELETE FROM produk WHERE id_produk='$id'");

header("Location: dashboard-admin.php?page=produk");
exit;
?>