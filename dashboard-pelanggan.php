<?php
session_start();
require_once "config/koneksi.php";

if(!isset($_SESSION['id_user']) || $_SESSION['role_user'] != 'pelanggan'){
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$nama_user = $_SESSION['nama_user'] ?? 'Pelanggan';

// Statistik Pesanan
$total_pesanan = mysqli_num_rows(mysqli_query($conn, "SELECT id_pesanan FROM pesanan WHERE id_user = '$id_user'"));
$pesanan_proses = mysqli_num_rows(mysqli_query($conn, "SELECT id_pesanan FROM pesanan WHERE id_user = '$id_user' AND status_pesanan NOT IN ('Selesai', 'Dibatalkan')"));
$pesanan_selesai = mysqli_num_rows(mysqli_query($conn, "SELECT id_pesanan FROM pesanan WHERE id_user = '$id_user' AND status_pesanan = 'Selesai'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pelanggan - Toko Adat</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
        .navbar { background: #fff !important; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .navbar-brand { font-weight: 800; color: #0d6efd !important; }
        
        .welcome-card {
            background: linear-gradient(135deg, #0d6efd, #0099ff);
            color: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 20px rgba(13, 110, 253, 0.15);
        }
        
        .stat-card {
            background: #fff;
            border-radius: 15px;
            padding: 20px;
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            transition: transform 0.3s;
            height: 100%;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 15px;
        }
        
        .card-order {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            overflow: hidden;
            border: none;
        }
        .table thead th {
            background: #f8f9fa;
            border: none;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 15px;
        }
        .table td { padding: 15px; vertical-align: middle; }
        
        .btn-custom { border-radius: 10px; font-weight: 600; padding: 8px 20px; transition: all 0.3s; }
        
        @media (max-width: 768px) {
            .welcome-card { padding: 20px; }
            .stat-card { padding: 15px; }
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light sticky-top py-3">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-store me-2"></i>TOKO ADAT
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center gap-2">
                <li class="nav-item">
                    <a class="nav-link fw-bold text-dark" href="index.php">Katalog</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-bold text-dark" href="keranjang.php">Keranjang</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-bold text-primary active" href="dashboard-pelanggan.php">Akun Saya</a>
                </li>
                <li class="nav-item ms-lg-3">
                    <a href="logout.php" class="btn btn-outline-danger btn-custom btn-sm px-4">
                        <i class="fas fa-sign-out-alt me-1"></i> Keluar
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-4 py-lg-5">
    <!-- WELCOME -->
    <div class="welcome-card">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="fw-bold mb-2">Halo, <?= $nama_user ?>! 👋</h2>
                <p class="mb-0 opacity-75">Senang melihatmu kembali. Cek status pesanan atau lanjut belanja pakaian adat favoritmu.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="index.php" class="btn btn-light btn-custom shadow-sm text-primary">
                    <i class="fas fa-shopping-bag me-2"></i> Belanja Lagi
                </a>
            </div>
        </div>
    </div>

    <!-- STATS -->
    <div class="row g-3 g-lg-4 mb-4">
        <div class="col-4">
            <div class="stat-card">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="fas fa-shopping-basket"></i>
                </div>
                <p class="text-muted small fw-bold mb-1">TOTAL</p>
                <h4 class="fw-bold m-0"><?= $total_pesanan ?></h4>
            </div>
        </div>
        <div class="col-4">
            <div class="stat-card">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="fas fa-spinner"></i>
                </div>
                <p class="text-muted small fw-bold mb-1">PROSES</p>
                <h4 class="fw-bold m-0"><?= $pesanan_proses ?></h4>
            </div>
        </div>
        <div class="col-4">
            <div class="stat-card">
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <p class="text-muted small fw-bold mb-1">SELESAI</p>
                <h4 class="fw-bold m-0"><?= $pesanan_selesai ?></h4>
            </div>
        </div>
    </div>

    <!-- RECENT ORDERS -->
    <div class="card-order">
        <div class="card-header bg-white py-3 border-0">
            <h5 class="m-0 fw-bold">Riwayat Pesanan</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID Pesanan</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $q_pes = mysqli_query($conn, "SELECT * FROM pesanan WHERE id_user = '$id_user' ORDER BY id_pesanan DESC");
                    if(mysqli_num_rows($q_pes) > 0):
                        while($r = mysqli_fetch_assoc($q_pes)):
                    ?>
                    <tr>
                        <td class="fw-bold text-primary">#ORD-<?= $r['id_pesanan'] ?></td>
                        <td class="text-muted small"><?= date('d M Y', strtotime($r['tanggal_pesanan'])) ?></td>
                        <td class="fw-bold">Rp<?= number_format($r['total_harga'], 0, ',', '.') ?></td>
                        <td>
                            <?php 
                                $badge = 'bg-warning';
                                if($r['status_pesanan'] == 'Selesai') $badge = 'bg-success';
                                if($r['status_pesanan'] == 'Dibatalkan') $badge = 'bg-danger';
                            ?>
                            <span class="badge rounded-pill <?= $badge ?> px-3"><?= $r['status_pesanan'] ?></span>
                        </td>
                        <td class="text-center">
                            <a href="pesanan-detail.php?id=<?= $r['id_pesanan'] ?>" class="btn btn-light btn-sm rounded-pill border px-3">
                                Detail
                            </a>
                        </td>
                    </tr>
                    <?php 
                        endwhile; 
                    else:
                    ?>
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <img src="https://illustrations.popsy.co/blue/shopping-cart.svg" width="120" class="mb-3 opacity-50">
                            <p class="text-muted">Kamu belum memiliki pesanan.</p>
                            <a href="index.php" class="btn btn-primary btn-custom btn-sm">Mulai Belanja</a>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>