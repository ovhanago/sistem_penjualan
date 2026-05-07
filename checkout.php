<?php
session_start();
require_once "config/koneksi.php";

// Proteksi halaman: harus login dan ada barang di keranjang
if(!isset($_SESSION['id_user']) || empty($_SESSION['keranjang'])){
    header("Location: index.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$user_query = mysqli_query($conn, "SELECT * FROM user WHERE id_user = '$id_user'");
$user = mysqli_fetch_assoc($user_query);

// Menghitung Total
$total_bayar = 0;
$items_list = [];
foreach($_SESSION['keranjang'] as $id => $qty){
    $q = mysqli_query($conn, "SELECT * FROM produk WHERE id_produk = '$id'");
    $prod = mysqli_fetch_assoc($q);
    $subtotal = ($prod['harga_produk'] * $qty);
    $total_bayar += $subtotal;
    
    $items_list[] = [
        'id' => $id,
        'nama' => $prod['nama_produk'],
        'harga' => $prod['harga_produk'],
        'qty' => $qty,
        'sub' => $subtotal,
        'gambar' => $prod['gambar_produk']
    ];
}

// Persiapan Midtrans Snap Token
$snapToken = "";
if(isset($_POST['bayar_midtrans'])){
    $tgl_pesanan = date("Y-m-d H:i:s");
    
    // 1. Simpan ke database 'pesanan' sebagai pending
    $sql_pesanan = "INSERT INTO pesanan (id_user, tanggal_pesanan, total_harga, status_pesanan) 
                    VALUES ('$id_user', '$tgl_pesanan', '$total_bayar', 'Pending')";
    $query_pesanan = mysqli_query($conn, $sql_pesanan);

    if($query_pesanan){
        $id_pesanan_baru = mysqli_insert_id($conn);

        // 2. Simpan Detail Transaksi dan Kurangi stok produk
        foreach($_SESSION['keranjang'] as $id => $qty){
            $q_p = mysqli_query($conn, "SELECT harga_produk FROM produk WHERE id_produk = '$id'");
            $p_data = mysqli_fetch_assoc($q_p);
            $harga_satuan = $p_data['harga_produk'];

            $sql_detail = "INSERT INTO pesanan_detail (id_transaksi, id_produk, jumlah, harga_satuan) 
                           VALUES ('$id_pesanan_baru', '$id', '$qty', '$harga_satuan')";
            mysqli_query($conn, $sql_detail);

            mysqli_query($conn, "UPDATE produk SET jumlah_produk = jumlah_produk - $qty WHERE id_produk = '$id'");
        }

        // 3. Buat Request ke Midtrans API
        $midtrans_order_id = "ORD-" . $id_pesanan_baru . "-" . time();

        $params = [
            'transaction_details' => [
                'order_id' => $midtrans_order_id,
                'gross_amount' => (int)$total_bayar,
            ],
            'customer_details' => [
                'first_name' => $user['nama_user'],
                'email' => $user['username_user'] . '@example.com', 
                'phone' => $user['telp_user'],
            ],
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            mysqli_query($conn, "UPDATE pesanan SET snap_token = '$snapToken', midtrans_order_id = '$midtrans_order_id' WHERE id_pesanan = '$id_pesanan_baru'");
            unset($_SESSION['keranjang']);
        } catch (Exception $e) {
            $error = "Midtrans Error: " . $e->getMessage();
        }
    } else {
        $error = "Gagal menyimpan pesanan: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selesaikan Pesanan - Toko Pakaian Adat</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <?php if(defined('MIDTRANS_CLIENT_KEY')): ?>
    <script type="text/javascript" src="<?= MIDTRANS_IS_PRODUCTION ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' ?>" data-client-key="<?= MIDTRANS_CLIENT_KEY ?>"></script>
    <?php endif; ?>

    <style>
        body { background-color: #fcfcfd; font-family: 'Plus Jakarta Sans', sans-serif; color: #2d3436; }
        .card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
        .checkout-header-card { background: linear-gradient(135deg, #0d6efd 0%, #0052cc 100%); color: white; border-radius: 20px; padding: 30px; margin-bottom: 30px; }
        .product-img { width: 60px; height: 60px; border-radius: 12px; object-fit: cover; background: #f8f9fa; }
        .btn-pay { padding: 15px; border-radius: 16px; font-weight: 700; background: #0d6efd; border: none; color: white; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white py-3 border-bottom sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="index.php"><i class="fas fa-store me-2"></i>Toko Adat</a>
    </div>
</nav>

<div class="container mt-4 mb-5">
    <div class="checkout-header-card">
        <h2 class="fw-bold mb-1">Konfirmasi Pesanan</h2>
        <p class="mb-0 opacity-75">Tinggal selangkah lagi untuk mendapatkan pakaian adat impianmu.</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card p-4 mb-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-map-marker-alt me-2 text-primary"></i>Alamat Pengiriman</h5>
                <p class="mb-1 fw-bold"><?= $user['nama_user'] ?></p>
                <p class="text-muted small mb-2"><?= $user['telp_user'] ?></p>
                <p><?= $user['alamat_user'] ?></p>
            </div>

            <div class="card p-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-box-open me-2 text-primary"></i>Daftar Belanja</h5>
                <?php foreach($items_list as $item): ?>
                <div class="d-flex align-items-center mb-3 border-bottom pb-3">
                    <img src="uploads/<?= $item['gambar'] ?: 'default.png' ?>" class="product-img me-3">
                    <div class="flex-grow-1">
                        <h6 class="fw-bold mb-0"><?= $item['nama'] ?></h6>
                        <p class="small text-muted mb-0"><?= $item['qty'] ?> x Rp <?= number_format($item['harga'], 0, ',', '.') ?></p>
                    </div>
                    <div class="text-end">
                        <span class="fw-bold">Rp <?= number_format($item['sub'], 0, ',', '.') ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card p-4">
                <h5 class="fw-bold mb-4">Ringkasan Biaya</h5>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span class="fw-medium">Rp <?= number_format($total_bayar, 0, ',', '.') ?></span>
                </div>
                <div class="d-flex justify-content-between mb-4 mt-2 border-top pt-2">
                    <span class="h6 fw-bold">Total Tagihan</span>
                    <span class="h5 fw-bold text-primary">Rp <?= number_format($total_bayar, 0, ',', '.') ?></span>
                </div>

                <?php if(isset($error)): ?>
                    <div class="alert alert-danger small"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST">
                    <button type="submit" name="bayar_midtrans" class="btn btn-primary w-100 btn-pay mb-3">
                        BAYAR SEKARANG <i class="fas fa-lock ms-2"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var snapToken = "<?= $snapToken ?>";
    if(snapToken != ""){
        window.snap.pay(snapToken, {
            onSuccess: function(result){ window.location.href = "dashboard-pelanggan.php?status=success"; },
            onPending: function(result){ window.location.href = "dashboard-pelanggan.php?status=pending"; },
            onError: function(result){ alert("Pembayaran gagal!"); }
        });
    }
</script>
</body>
</html>