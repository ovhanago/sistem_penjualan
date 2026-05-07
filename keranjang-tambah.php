<?php
session_start();

if (isset($_GET['id'])) {
    require_once "config/koneksi.php";
    $id_produk = mysqli_real_escape_string($conn, $_GET['id']);

    // Cek stok tersedia
    $q_stok = mysqli_query($conn, "SELECT jumlah_produk FROM produk WHERE id_produk = '$id_produk'");
    $d_stok = mysqli_fetch_assoc($q_stok);
    $stok_tersedia = $d_stok['jumlah_produk'];

    if (!isset($_SESSION['keranjang'])) {
        $_SESSION['keranjang'] = [];
    }

    $jumlah_sekarang = isset($_SESSION['keranjang'][$id_produk]) ? $_SESSION['keranjang'][$id_produk] : 0;

    if ($jumlah_sekarang < $stok_tersedia) {
        if (isset($_SESSION['keranjang'][$id_produk])) {
            $_SESSION['keranjang'][$id_produk]++;
        } else {
            $_SESSION['keranjang'][$id_produk] = 1;
        }
        header("Location: index.php?status=success");
    } else {
        echo "<script>alert('Jumlah di keranjang sudah mencapai batas stok!'); window.location='index.php';</script>";
    }
} else {
    header("Location: index.php");
}
?>