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
    <title>Pesanan Saya - Toko Adat</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #0d6efd;
            --soft-bg: #f0f2f5;
        }
        body { background-color: var(--soft-bg); font-family: 'Segoe UI', sans-serif; padding-bottom: 80px; }
        .card { border: none; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .navbar { box-shadow: 0 2px 15px rgba(0,0,0,0.05); }
        
        /* Bottom Navigation for Mobile */
        .bottom-nav { position: fixed; bottom: 0; left: 0; right: 0; background: #fff; display: none; justify-content: space-around; padding: 12px 0; box-shadow: 0 -5px 15px rgba(0,0,0,0.05); z-index: 1030; border-top-left-radius: 25px; border-top-right-radius: 25px; }
        .bottom-nav-item { text-align: center; color: #6c757d; text-decoration: none; font-size: 11px; transition: 0.3s; }
        .bottom-nav-item i { font-size: 20px; display: block; margin-bottom: 4px; }
        .bottom-nav-item.active { color: var(--primary-color); font-weight: bold; }

        .welcome-banner {
            background: linear-gradient(135deg, #0d6efd, #0dcaf0);
            color: white;
            border-radius: 25px;
            padding: 40px 30px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }
        .welcome-banner::after {
            content: '\f007';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            right: -20px;
            bottom: -20px;
            font-size: 150px;
            opacity: 0.1;
        }
        .stat-card {
            border-radius: 20px;
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.03);
        }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
        .stat-icon {
            width: 55px;
            height: 55px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 15px;
        }
        
        @media (max-width: 768px) {
            .bottom-nav { display: flex; }
            .navbar { display: none; }
            .welcome-banner { padding: 30px 20px; border-radius: 20px; }
            .welcome-banner h3 { font-size: 1.5rem; }
            .stat-icon { width: 45px; height: 45px; font-size: 18px; margin-bottom: 10px; }
            .card-header h5 { font-size: 1.1rem; }
            .order-card-mobile { background: #fff; border-radius: 15px; padding: 15px; margin-bottom: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.03); }
        }
    </style>
</head>
<body>

<!-- Mobile Bottom Nav -->
<div class="bottom-nav">
    <a href="index.php" class="bottom-nav-item">
        <i class="fas fa-home"></i>
        <span>Beranda</span>
    </a>
    <a href="keranjang.php" class="bottom-nav-item">
        <i class="fas fa-shopping-cart"></i>
        <span>Keranjang</span>
    </a>
    <a href="dashboard-pelanggan.php" class="bottom-nav-item active">
        <i class="fas fa-list-alt"></i>
        <span>Pesanan</span>
    </a>
    <a href="logout.php" class="bottom-nav-item text-danger">
        <i class="fas fa-sign-out-alt"></i>
        <span>Keluar</span>
    </a>
</div>

<!-- NAVBAR (Desktop Only) -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top d-none d-lg-block">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="fas fa-store-alt me-2"></i>Toko Adat Bajawa
        </a>
        <div class="ms-auto d-flex gap-2 align-items-center">
            <a href="index.php" class="btn btn-light btn-sm rounded-pill px-3">
                <i class="fas fa-shopping-bag me-1"></i> Belanja
            </a>
            <div class="vr mx-2 text-white opacity-50"></div>
            <span class="text-white small me-3"><i class="fas fa-user-circle me-1"></i> <?= $nama_user ?></span>
            <a href="logout.php" class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm">
                <i class="fas fa-sign-out-alt me-1"></i> Logout
            </a>
        </div>
    </div>
</nav>

<div class="container mt-lg-4 mt-3">
    <div class="d-lg-none mb-3 d-flex justify-content-between align-items-center">
        <h4 class="fw-bold mb-0">Akun Saya</h4>
        <a href="index.php" class="btn btn-primary btn-sm rounded-pill px-3">
            <i class="fas fa-arrow-left me-1"></i> Toko
        </a>
    </div>

    <!-- Banner Selamat Datang -->
    <div class="welcome-banner shadow-sm">
        <h3 class="fw-bold mb-1">Halo, <?= $nama_user ?>! 👋</h3>
        <p class="mb-0 opacity-90 small">Pantau status pesanan pakaian adat Bajawa Anda dengan mudah di sini.</p>
    </div>

    <!-- Baris Statistik -->
    <div class="row mb-4">
        <div class="col-4 col-md-4">
            <div class="card stat-card h-100 text-center p-2 p-md-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary mx-auto">
                    <i class="fas fa-box"></i>
                </div>
                <h6 class="text-muted mb-1 x-small fw-bold text-uppercase d-none d-md-block">Total</h6>
                <h4 class="mb-0 fw-bold"><?= $total_pesanan ?></h4>
                <span class="small text-muted d-md-none">Total</span>
            </div>
        </div>
        <div class="col-4 col-md-4">
            <div class="card stat-card h-100 text-center p-2 p-md-3">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning mx-auto">
                    <i class="fas fa-spinner"></i>
                </div>
                <h6 class="text-muted mb-1 x-small fw-bold text-uppercase d-none d-md-block">Proses</h6>
                <h4 class="mb-0 fw-bold"><?= $pesanan_proses ?></h4>
                <span class="small text-muted d-md-none">Proses</span>
            </div>
        </div>
        <div class="col-4 col-md-4">
            <div class="card stat-card h-100 text-center p-2 p-md-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success mx-auto">
                    <i class="fas fa-check-double"></i>
                </div>
                <h6 class="text-muted mb-1 x-small fw-bold text-uppercase d-none d-md-block">Selesai</h6>
                <h4 class="mb-0 fw-bold"><?= $pesanan_selesai ?></h4>
                <span class="small text-muted d-md-none">Selesai</span>
            </div>
        </div>
    </div>

    <!-- Tabel Pesanan -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm overflow-hidden">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-receipt me-2 text-primary"></i>Riwayat Pesanan</h5>
                </div>
                
                <!-- Desktop Table -->
                <div class="card-body p-0 d-none d-md-block">
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
                                    <td><?= date('d M Y', strtotime($r['tanggal_pesanan'])) ?></td>
                                    <td class="fw-bold text-primary">Rp <?= number_format($r['total_harga'], 0, ',', '.') ?></td>
                                    <td><span class="badge rounded-pill <?= $badge ?> px-3 py-2"><?= $r['status_pesanan'] ?></span></td>
                                    <td class="pe-4 text-center">
                                        <a href="pesanan-detail.php?id=<?= $r['id_pesanan'] ?>" class="btn btn-light btn-sm rounded-circle text-primary shadow-sm btn-action">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; else: ?>
                                <tr><td colspan="5" class="text-center py-5">Belum ada pesanan</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile List -->
                <div class="card-body p-3 d-md-none bg-light bg-opacity-50">
                    <?php 
                    mysqli_data_seek($res, 0);
                    if(mysqli_num_rows($res) > 0):
                        while($r = mysqli_fetch_assoc($res)):
                            $badge = 'bg-warning text-dark';
                            $status = $r['status_pesanan'];
                            if($status == 'Dikirim') $badge = 'bg-info text-white';
                            if($status == 'Selesai') $badge = 'bg-success text-white';
                            if($status == 'Dibatalkan' || $status == 'Batal') $badge = 'bg-danger text-white';
                    ?>
                    <div class="order-card-mobile">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-dark">#ORD-<?= $r['id_pesanan'] ?></span>
                            <span class="badge rounded-pill <?= $badge ?> px-2 py-1" style="font-size: 10px;"><?= $r['status_pesanan'] ?></span>
                        </div>
                        <div class="text-muted small mb-3"><?= date('d M Y', strtotime($r['tanggal_pesanan'])) ?></div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-primary">Rp <?= number_format($r['total_harga'], 0, ',', '.') ?></span>
                            <a href="pesanan-detail.php?id=<?= $r['id_pesanan'] ?>" class="btn btn-primary btn-sm rounded-pill px-3 py-1 shadow-sm">
                                Detail <i class="fas fa-chevron-right ms-1" style="font-size: 10px;"></i>
                            </a>
                        </div>
                    </div>
                    <?php endwhile; else: ?>
                    <div class="text-center py-4 text-muted">Belum ada pesanan</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>