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
    <title>Dashboard Saya - Bajawa Adat</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --accent-color: #0d6efd;
            --bg-light: #fcfcfc;
        }
        body { background-color: var(--bg-light); font-family: 'Inter', sans-serif; }
        
        .navbar { 
            background: white !important; 
            box-shadow: 0 2px 15px rgba(0,0,0,0.05); 
            padding: 15px 0;
        }
        .navbar-brand { font-family: 'Playfair Display', serif; font-weight: 700; color: #1a1a1a !important; }

        .dashboard-header {
            padding: 60px 0 40px;
        }
        .welcome-card {
            background: #1a1a1a;
            color: white;
            border-radius: 30px;
            padding: 40px;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .welcome-card::after {
            content: '\f008';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            right: -30px;
            bottom: -30px;
            font-size: 200px;
            opacity: 0.05;
        }

        .stat-card {
            background: white;
            border: 1px solid rgba(0,0,0,0.05);
            border-radius: 24px;
            padding: 25px;
            transition: all 0.3s ease;
            height: 100%;
        }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.05); }
        .stat-icon {
            width: 50px; height: 50px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
            margin-bottom: 15px;
        }

        .table-card {
            background: white;
            border-radius: 24px;
            border: 1px solid rgba(0,0,0,0.05);
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.02);
        }
        .table thead th {
            background: #f8f9fa;
            border: none;
            padding: 20px;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6c757d;
        }
        .table td { padding: 20px; border-bottom: 1px solid #f8f9fa; vertical-align: middle; }

        @media (max-width: 768px) {
            .dashboard-header { padding: 30px 0; }
            .welcome-card { padding: 30px 20px; border-radius: 20px; }
            .stat-card { border-radius: 18px; padding: 20px; }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container text-center text-lg-start">
        <a class="navbar-brand mx-auto ms-lg-0" href="index.php">
            Bajawa<span class="text-primary">Adat</span>
        </a>
        <div class="ms-auto d-none d-lg-block">
            <a href="index.php" class="btn btn-outline-dark rounded-pill px-4 me-2">Kembali Belanja</a>
            <a href="logout.php" class="btn btn-danger rounded-pill px-4 shadow-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="dashboard-header">
        <div class="welcome-card">
            <h2 class="fw-bold mb-2" style="font-family: 'Playfair Display', serif;">Selamat Datang, <?= $nama_user ?></h2>
            <p class="opacity-75 mb-0">Ini adalah ringkasan aktivitas belanja Anda di Toko Adat Bajawa.</p>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-4 col-md-4">
                <div class="stat-card">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="small text-muted mb-1 text-uppercase fw-bold d-none d-md-block" style="letter-spacing: 1px;">Total Pesanan</div>
                    <h3 class="fw-bold mb-0"><?= $total_pesanan ?></h3>
                    <div class="d-md-none small text-muted">Pesanan</div>
                </div>
            </div>
            <div class="col-4 col-md-4">
                <div class="stat-card">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="small text-muted mb-1 text-uppercase fw-bold d-none d-md-block" style="letter-spacing: 1px;">Dalam Proses</div>
                    <h3 class="fw-bold mb-0"><?= $pesanan_proses ?></h3>
                    <div class="d-md-none small text-muted">Proses</div>
                </div>
            </div>
            <div class="col-4 col-md-4">
                <div class="stat-card">
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <div class="small text-muted mb-1 text-uppercase fw-bold d-none d-md-block" style="letter-spacing: 1px;">Selesai</div>
                    <h3 class="fw-bold mb-0"><?= $pesanan_selesai ?></h3>
                    <div class="d-md-none small text-muted">Selesai</div>
                </div>
            </div>
        </div>

        <div class="table-card">
            <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Riwayat Transaksi</h5>
                <a href="index.php" class="btn btn-primary btn-sm rounded-pill px-3 d-lg-none">Belanja Lagi</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>No. Order</th>
                            <th>Tanggal</th>
                            <th>Total Tagihan</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
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
                            <td class="fw-bold">#ORD-<?= $r['id_pesanan'] ?></td>
                            <td class="small text-muted"><?= date('d M Y', strtotime($r['tanggal_pesanan'])) ?></td>
                            <td class="fw-bold text-primary">Rp <?= number_format($r['total_harga'], 0, ',', '.') ?></td>
                            <td><span class="badge rounded-pill <?= $badge ?> px-3 py-2" style="font-size: 0.7rem;"><?= $r['status_pesanan'] ?></span></td>
                            <td class="text-center">
                                <a href="pesanan-detail.php?id=<?= $r['id_pesanan'] ?>" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada transaksi</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="d-lg-none p-4 text-center">
    <a href="logout.php" class="btn btn-danger rounded-pill w-100 py-3 shadow">Logout</a>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>