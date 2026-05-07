<?php
session_start();
require_once "config/koneksi.php";

// Proteksi halaman: harus login
if(!isset($_SESSION['id_user'])){
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$role = $_SESSION['role_user'];
$id_pesanan = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : '';

if($id_pesanan == ''){
    header("Location: index.php");
    exit;
}

// Query Data Pesanan (Tabel Pesanan)
$query_str = "SELECT pesanan.*, user.nama_user, user.alamat_user, user.telp_user 
              FROM pesanan 
              JOIN user ON pesanan.id_user = user.id_user 
              WHERE pesanan.id_pesanan = '$id_pesanan'";

if($role == 'pelanggan'){
    $query_str .= " AND pesanan.id_user = '$id_user'";
}

$q_pesanan = mysqli_query($conn, $query_str);
$pesanan = mysqli_fetch_assoc($q_pesanan);

if(!$pesanan){
    echo "<div class='container mt-5 text-center'><h3>Pesanan tidak ditemukan atau Anda tidak memiliki akses.</h3><a href='index.php' class='btn btn-primary'>Kembali</a></div>";
    exit;
}

// Query Detail Produk (Logika Asli)
$q_detail = mysqli_query($conn, "SELECT pesanan_detail.*, produk.nama_produk, produk.gambar_produk
                                FROM pesanan_detail
                                JOIN produk ON pesanan_detail.id_produk = produk.id_produk
                                WHERE pesanan_detail.id_transaksi = '$id_pesanan'
                                OR pesanan_detail.id_transaksi = '#ORD-$id_pesanan'
                                OR pesanan_detail.id_transaksi = (SELECT id_pesanan FROM pesanan WHERE id_pesanan = '$id_pesanan')");?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan #<?= $pesanan['id_pesanan'] ?> - Toko Adat</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0d6efd;
            --bg-body: #f4f7fe;
        }
        body { 
            background-color: var(--bg-body); 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            color: #2d3436;
        }
        .main-container { padding: 40px 0; }
        .card { 
            border: none; 
            border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.03); 
            background: #fff;
            margin-bottom: 24px;
        }
        .status-badge {
            padding: 8px 18px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-pending { background: #fff8e1; color: #ffa000; }
        .status-selesai { background: #e8f5e9; color: #2e7d32; }
        .status-batal { background: #ffebee; color: #c62828; }
        
        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        .section-title i {
            margin-right: 12px;
            color: var(--primary-color);
            background: rgba(13, 110, 253, 0.1);
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
        }
        
        .info-group { margin-bottom: 15px; }
        .info-label { 
            font-size: 0.8rem; 
            color: #b2bec3; 
            font-weight: 600; 
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .info-value { font-weight: 600; font-size: 1rem; }
        
        .product-card {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 1px solid #f1f3f5;
            border-radius: 15px;
            margin-bottom: 15px;
            transition: all 0.2s;
        }
        .product-card:hover { border-color: var(--primary-color); }
        .product-img {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            object-fit: cover;
            margin-right: 20px;
        }
        .product-info h6 { font-weight: 700; margin-bottom: 5px; }
        .product-qty { font-size: 0.9rem; color: #636e72; }
        
        .total-section {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .total-label { font-weight: 500; color: #636e72; }
        .total-value { font-weight: 700; }
        .grand-total {
            font-size: 1.3rem;
            color: var(--primary-color);
            border-top: 1px dashed #dee2e6;
            margin-top: 10px;
            padding-top: 10px;
        }

        .btn-action {
            border-radius: 12px;
            padding: 12px 25px;
            font-weight: 700;
            transition: all 0.3s;
        }
        .btn-print { background: #fff; border: 2px solid #f1f3f5; }
        .btn-print:hover { background: #f1f3f5; }

        @media print {
            .btn-action, nav { display: none !important; }
            .card { box-shadow: none; border: 1px solid #eee; }
        }
    </style>
</head>
<body>

<div class="container main-container">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            
            <!-- Header Nav -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="dashboard-pelanggan.php" class="btn btn-light rounded-pill shadow-sm px-4">
                        <i class="fas fa-arrow-left me-2"></i> Kembali
                    </a>
                    <div class="d-flex gap-2">
                        <?php 
                            $status_val = $pesanan['status_pesanan'];
                            if($status_val == 'Pending' || $status_val == 'Menunggu Pembayaran' || $status_val == 'Dikemas'): 
                        ?>
                        <a href="batal-pesanan.php?id=<?= $pesanan['id_pesanan'] ?>" class="btn btn-danger btn-action shadow-sm" onclick="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini? Jika sudah dibayar, uang akan dikembalikan.')">
                            <i class="fas fa-times me-2"></i> Batalkan Pesanan
                        </a>
                        <?php endif; ?>
                        <button onclick="window.print()" class="btn btn-print btn-action shadow-sm">
                            <i class="fas fa-print me-2"></i> Cetak Invoice
                        </button>
                    </div>
                </div>

            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h3 class="fw-bold mb-1">Pesanan #<?= $pesanan['id_pesanan'] ?></h3>
                        <p class="text-muted small"><i class="far fa-calendar-alt me-1"></i> Dibuat pada <?= date('d F Y, H:i', strtotime($pesanan['tanggal_pesanan'])) ?></p>
                    </div>
                    <?php 
                        $status = strtolower($pesanan['status_pesanan']);
                        $badge_class = 'status-pending';
                        if($status == 'selesai') $badge_class = 'status-selesai';
                        if($status == 'dibatalkan' || $status == 'batal') $badge_class = 'status-batal';
                    ?>
                    <span class="status-badge <?= $badge_class ?>">
                        <i class="fas fa-circle me-1" style="font-size: 8px;"></i> <?= $pesanan['status_pesanan'] ?>
                    </span>
                </div>

                <div class="row g-4">
                    <!-- Info Pembeli -->
                    <div class="col-md-6">
                        <div class="section-title"><i class="fas fa-user"></i> Detail Pembeli</div>
                        <div class="info-group">
                            <div class="info-label">Nama Lengkap</div>
                            <div class="info-value"><?= $pesanan['nama_user'] ?></div>
                        </div>
                        <div class="info-group">
                            <div class="info-label">Nomor Telepon</div>
                            <div class="info-value"><?= $pesanan['telp_user'] ?></div>
                        </div>
                    </div>
                    <!-- Info Alamat -->
                    <div class="col-md-6">
                        <div class="section-title"><i class="fas fa-map-marker-alt"></i> Alamat Pengiriman</div>
                        <div class="info-group">
                            <div class="info-label">Alamat Lengkap</div>
                            <div class="info-value fw-normal"><?= $pesanan['alamat_user'] ?></div>
                        </div>
                    </div>
                </div>

                <hr class="my-4" style="opacity: 0.1;">

                <div class="section-title"><i class="fas fa-shopping-bag"></i> Item Pesanan</div>
                <div class="product-list mb-4">
                    <?php 
                    $total_item_bayar = 0;
                    mysqli_data_seek($q_detail, 0); // Reset pointer
                    while($item = mysqli_fetch_assoc($q_detail)): 
                        $sub = $item['jumlah'] * $item['harga_satuan'];
                        $total_item_bayar += $sub;
                    ?>
                    <div class="product-card">
                        <img src="uploads/<?= $item['gambar_produk'] ?: 'default.png' ?>" class="product-img shadow-sm">
                        <div class="flex-grow-1 product-info">
                            <h6><?= $item['nama_produk'] ?></h6>
                            <div class="product-qty"><?= $item['jumlah'] ?> x Rp <?= number_format($item['harga_satuan'], 0, ',', '.') ?></div>
                        </div>
                        <div class="text-end fw-bold">
                            Rp <?= number_format($sub, 0, ',', '.') ?>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>

                <div class="total-section">
                    <div class="total-row">
                        <span class="total-label">Subtotal Produk</span>
                        <span class="total-value">Rp <?= number_format($total_item_bayar, 0, ',', '.') ?></span>
                    </div>
                    <div class="total-row">
                        <span class="total-label">Biaya Pengiriman</span>
                        <span class="total-value text-success">Gratis</span>
                    </div>
                    <div class="total-row grand-total">
                        <span class="fw-bold">Total Pembayaran</span>
                        <span class="fw-bold">Rp <?= number_format($pesanan['total_harga'], 0, ',', '.') ?></span>
                    </div>
                </div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>