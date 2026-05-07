<?php
session_start();
require_once "config/koneksi.php";

// Proteksi halaman Pelanggan
if(!isset($_SESSION['id_user']) || $_SESSION['role_user'] != 'pelanggan'){
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$nama_user = $_SESSION['nama_user'] ?? 'Pelanggan';

// Statistik Pesanan
$total_pesanan = mysqli_num_rows(mysqli_query($conn, "SELECT id_pesanan FROM pesanan WHERE id_user = '$id_user'"));
$pesanan_proses = mysqli_num_rows(mysqli_query($conn, "SELECT id_pesanan FROM pesanan WHERE id_user = '$id_user' AND status_pesanan NOT IN ('Selesai', 'Dibatalkan', 'Batal')"));
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
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .navbar { box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        
        .welcome-banner {
            background: linear-gradient(45deg, #0d6efd, #0dcaf0);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
        }
        .stat-card {
            transition: transform 0.3s;
            border-left: 5px solid #0d6efd;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-icon {
            width: 50px;
            height: 50px;
            background: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        @media (max-width: 768px) {
            .navbar-brand { font-size: 1.1rem !important; }
            .welcome-banner { padding: 20px; }
            .welcome-banner h3 { font-size: 1.4rem; }
        }
    </style>
</head>
<body>

<!-- NAVBAR (Sama dengan PC) -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="fas fa-store me-2"></i>Toko Pakaian Adat
        </a>
        
        <button class="navbar-toggler shadow-none border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a href="index.php" class="nav-link text-white me-lg-3"><i class="fas fa-home me-1"></i> Beranda</a>
                </li>
                <li class="nav-item">
                    <a href="keranjang.php" class="nav-link text-white me-lg-3"><i class="fas fa-shopping-cart me-1"></i> Keranjang</a>
                </li>
                <li class="nav-item dropdown mt-3 mt-lg-0 w-100 text-center text-lg-start">
                    <a class="btn btn-light btn-sm rounded-pill px-3 dropdown-toggle w-100" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i> <?= $nama_user ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow mt-2">
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4 mb-5">
    <!-- Banner Selamat Datang -->
    <div class="welcome-banner shadow-sm">
        <div class="row align-items-center">
            <div class="col-12 col-md-8">
                <h3 class="fw-bold mb-1">Halo, <?= $nama_user ?>! 👋</h3>
                <p class="mb-0 opacity-75">Pantau status pesanan pakaian adat Anda di sini.</p>
            </div>
            <div class="col-md-4 text-end d-none d-md-block">
                <i class="fas fa-user-shield fa-4x opacity-25"></i>
            </div>
        </div>
    </div>

    <!-- Baris Statistik -->
    <div class="row mb-4">
        <div class="col-4 col-md-4 mb-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex flex-column flex-md-row align-items-center text-center text-md-start">
                    <div class="stat-icon mb-2 mb-md-0 me-md-3">
                        <i class="fas fa-box"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 small text-uppercase fw-bold d-none d-md-block">Total</h6>
                        <h4 class="mb-0 fw-bold"><?= $total_pesanan ?></h4>
                        <span class="d-md-none small text-muted">Total</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-4 col-md-4 mb-3">
            <div class="card stat-card shadow-sm h-100" style="border-left-color: #ffc107;">
                <div class="card-body d-flex flex-column flex-md-row align-items-center text-center text-md-start">
                    <div class="stat-icon mb-2 mb-md-0 me-md-3" style="background: rgba(255, 193, 7, 0.1); color: #ffc107;">
                        <i class="fas fa-spinner"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 small text-uppercase fw-bold d-none d-md-block">Proses</h6>
                        <h4 class="mb-0 fw-bold"><?= $pesanan_proses ?></h4>
                        <span class="d-md-none small text-muted">Proses</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-4 col-md-4 mb-3">
            <div class="card stat-card shadow-sm h-100" style="border-left-color: #198754;">
                <div class="card-body d-flex flex-column flex-md-row align-items-center text-center text-md-start">
                    <div class="stat-icon mb-2 mb-md-0 me-md-3" style="background: rgba(25, 135, 84, 0.1); color: #198754;">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 small text-uppercase fw-bold d-none d-md-block">Selesai</h6>
                        <h4 class="mb-0 fw-bold"><?= $pesanan_selesai ?></h4>
                        <span class="d-md-none small text-muted">Selesai</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Pesanan (Tetap gunakan tabel agar sama dengan PC) -->
    <div class="card shadow-sm overflow-hidden">
        <div class="card-header bg-white py-3 border-0">
            <h5 class="mb-0 fw-bold text-primary"><i class="fas fa-receipt me-2"></i>Riwayat Pesanan</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4">No. Pesanan</th>
                            <th>Tanggal</th>
                            <th>Total Tagihan</th>
                            <th>Status</th>
                            <th class="pe-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $res = mysqli_query($conn, "SELECT * FROM pesanan WHERE id_user = '$id_user' ORDER BY tanggal_pesanan DESC");
                        if(mysqli_num_rows($res) > 0):
                            while($r = mysqli_fetch_assoc($res)):
                                $badge = 'bg-warning text-dark';
                                $status = $r['status_pesanan'];
                                if($status == 'Dikirim') $badge = 'bg-info text-white';
                                if($status == 'Selesai') $badge = 'bg-success text-white';
                                if($status == 'Dibatalkan' || $status == 'Batal') $badge = 'bg-danger text-white';
                        ?>
                        <tr>
                            <td class="ps-4 fw-bold">#ORD-<?= $r['id_pesanan'] ?></td>
                            <td class="small"><?= date('d/m/Y', strtotime($r['tanggal_pesanan'])) ?></td>
                            <td class="fw-bold text-primary small">Rp <?= number_format($r['total_harga'], 0, ',', '.') ?></td>
                            <td><span class="badge rounded-pill <?= $badge ?>" style="font-size: 10px;"><?= $r['status_pesanan'] ?></span></td>
                            <td class="pe-4 text-center">
                                <a href="pesanan-detail.php?id=<?= $r['id_pesanan'] ?>" class="btn btn-primary btn-sm rounded-pill shadow-sm">
                                    <i class="fas fa-eye d-md-none"></i><span class="d-none d-md-inline">Detail</span>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada pesanan</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>