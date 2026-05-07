<?php
session_start();
require_once "config/koneksi.php";

// Proteksi: harus login sebagai pelanggan
if(!isset($_SESSION['id_user']) || $_SESSION['role_user'] != 'pelanggan'){
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$id_pesanan = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : '';

if($id_pesanan == ''){
    $_SESSION['error'] = "ID Pesanan tidak valid.";
    header("Location: dashboard-pelanggan.php");
    exit;
}

// 1. Cari data pesanan
$query = mysqli_query($conn, "SELECT * FROM pesanan WHERE id_pesanan = '$id_pesanan' AND id_user = '$id_user'");
$pesanan = mysqli_fetch_assoc($query);

if(!$pesanan){
    $_SESSION['error'] = "Pesanan tidak ditemukan atau Anda tidak memiliki akses.";
    header("Location: dashboard-pelanggan.php");
    exit;
}

// Cek status yang boleh dibatalkan
$status_skrg = $pesanan['status_pesanan'];
$allowed_status = ['Pending', 'Menunggu Pembayaran', 'Dikemas'];
if(!in_array($status_skrg, $allowed_status)){
    $_SESSION['error'] = "Pesanan dengan status $status_skrg tidak dapat dibatalkan.";
    header("Location: dashboard-pelanggan.php");
    exit;
}

// 2. Batalkan di Midtrans (jika ada midtrans_order_id)
$midtrans_id = $pesanan['midtrans_order_id'] ?? '';
$refund_success = false;
$midtrans_error = "";

if(!empty($midtrans_id)){
    try {
        $status_midtrans = \Midtrans\Transaction::status($midtrans_id);
        $transaction_status = $status_midtrans->transaction_status;

        if ($transaction_status == 'pending') {
            // Jika masih pending, batalkan (void/cancel)
            \Midtrans\Transaction::cancel($midtrans_id);
        } else if ($transaction_status == 'capture' || $transaction_status == 'settlement') {
            // Jika sudah bayar, coba refund (Hanya jika metode mendukung, e.g. Credit Card)
            // VA/Gopay biasanya membutuhkan langkah manual atau support khusus
            $params = array(
                'refund_key' => 'refund-' . $id_pesanan . '-' . time(),
                'amount' => (int)$pesanan['total_harga'],
                'reason' => 'Permintaan pembatalan oleh pelanggan'
            );
            try {
                \Midtrans\Transaction::refund($midtrans_id, $params);
                $refund_success = true;
            } catch (Exception $e) {
                $midtrans_error = "Pembayaran sudah diterima, pembatalan dilanjutkan namun refund otomatis gagal: " . $e->getMessage();
            }
        }
    } catch (Throwable $e) {
        // Abaikan jika transaksi tidak ditemukan di Midtrans (misal belum dibuka snapnya)
        $midtrans_error = "Info Midtrans: " . $e->getMessage();
    }
}

// 3. Update Status di Database dan Kembalikan Stok
mysqli_begin_transaction($conn);

try {
    // Update Status
    $update = mysqli_query($conn, "UPDATE pesanan SET status_pesanan = 'Dibatalkan' WHERE id_pesanan = '$id_pesanan'");
    if(!$update) throw new Exception("Gagal update status pesanan.");

    // Kembalikan Stok Produk
    // Mencari detail pesanan (mendukung format id integer atau format string #ORD-ID)
    $detail_res = mysqli_query($conn, "SELECT * FROM pesanan_detail WHERE id_transaksi = '$id_pesanan' OR id_transaksi = '#ORD-$id_pesanan'");
    
    while($item = mysqli_fetch_assoc($detail_res)){
        $id_p = $item['id_produk'];
        $qty = $item['jumlah'];
        $upd_stok = mysqli_query($conn, "UPDATE produk SET jumlah_produk = jumlah_produk + $qty WHERE id_produk = '$id_p'");
        if(!$upd_stok) throw new Exception("Gagal mengembalikan stok produk ID: $id_p");
    }

    mysqli_commit($conn);
    
    $msg = "Pesanan #ORD-$id_pesanan berhasil dibatalkan.";
    if($refund_success) {
        $msg .= " Uang akan dikembalikan melalui Midtrans.";
    } else if($status_skrg != 'Pending' && $status_skrg != 'Menunggu Pembayaran') {
        $msg .= " Silakan hubungi admin untuk proses pengembalian dana manual.";
    }
    
    $_SESSION['success'] = $msg;
} catch (Exception $e) {
    mysqli_rollback($conn);
    $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
}

header("Location: dashboard-pelanggan.php");
exit;
?>