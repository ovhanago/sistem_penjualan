<?php
session_name("SESS_PELANGGAN");
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
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card { border: none; border-radius: 15px; }
        .navbar { box-shadow: 0 2px 4px rgba(0,0,0,0.08); }
        .table thead { background-color: #f1f3f5; }
        .btn-action { width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; }
        
        .logout-link {
            background-color: #dc3545;
            color: white !important;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            padding: 6px 18px;
            border-radius: 50px;
            box-shadow: 0 4px 10px rgba(220, 53, 69, 0.2);
            transition: all 0.3s ease;
        }
        .logout-link:hover {
            background-color: #a71d2a;
            color: white !important;
            box-shadow: 0 6px 15px rgba(220, 53, 69, 0.4);
            transform: translateY(-2px);
        }
        
        /* Custom Styles for Banner & Stats */
        .welcome-banner {
            background: linear-gradient(45deg, #0d6efd, #0dcaf0);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.2);
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
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="fas fa-store me-2"></i>Toko Pakaian Adat
        </a>
        <div class="ms-auto d-flex gap-2">
            <a href="index.php" class="btn btn-light btn-sm rounded-pill px-3">
                <i class="fas fa-shopping-bag me-1"></i> Belanja
            </a>
            <a href="keranjang.php" class="btn btn-light btn-sm rounded-pill px-3">
                <i class="fas fa-shopping-cart me-1"></i> Keranjang
            </a>
            <a href="logout.php" class="logout-link ms-2">
                <i class="fas fa-sign-out-alt me-1"></i> Logout
            </a>
        </div>
    </div>
</nav>

<div class="container mt-4 mb-5">
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-pill px-4 shadow-sm mb-4 border-0" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?= $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-pill px-4 shadow-sm mb-4 border-0" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> <?= $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Banner Selamat Datang -->
    <div class="welcome-banner">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h3 class="fw-bold mb-1">Halo, <?= $nama_user ?>! 👋</h3>
                <p class="mb-0 opacity-75">Senang melihat Anda kembali. Pantau status pesanan pakaian adat Anda di sini.</p>
            </div>
            <div class="col-md-4 text-md-end d-none d-md-block">
                <i class="fas fa-user-circle fa-4x opacity-25"></i>
            </div>
        </div>
    </div>

    <!-- Baris Statistik -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon me-3">
                        <i class="fas fa-box"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 small text-uppercase fw-bold">Total Pesanan</h6>
                        <h4 class="mb-0 fw-bold"><?= $total_pesanan ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card stat-card shadow-sm h-100" style="border-left-color: #ffc107;">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon me-3" style="background: rgba(255, 193, 7, 0.1); color: #ffc107;">
                        <i class="fas fa-spinner"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 small text-uppercase fw-bold">Dalam Proses</h6>
                        <h4 class="mb-0 fw-bold"><?= $pesanan_proses ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card stat-card shadow-sm h-100" style="border-left-color: #198754;">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon me-3" style="background: rgba(25, 135, 84, 0.1); color: #198754;">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 small text-uppercase fw-bold">Selesai</h6>
                        <h4 class="mb-0 fw-bold"><?= $pesanan_selesai ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Pesanan -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm overflow-hidden border-0">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0 fw-bold text-primary"><i class="fas fa-receipt me-2"></i>Riwayat Pesanan</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-4">No. Pesanan</th>
                                    <th>Tanggal</th>
                                    <th>Total Tagihan</th>
                                    <th>Status Pesanan</th>
                                    <th class="pe-4 text-center">Detail</th>
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
                                    <td>
                                        <span class="badge rounded-pill <?= $badge ?> px-3 py-2" style="font-size: 11px;">
                                            <?= $r['status_pesanan'] ?>
                                        </span>
                                    </td>
                                    <td class="pe-4 text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="pesanan-detail.php?id=<?= $r['id_pesanan'] ?>" class="btn btn-light btn-sm rounded-circle text-primary shadow-sm btn-action" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if($status == 'Pending' || $status == 'Menunggu Pembayaran' || $status == 'Dikemas'): ?>
                                            <a href="batal-pesanan.php?id=<?= $r['id_pesanan'] ?>" class="btn btn-light btn-sm rounded-circle text-danger shadow-sm btn-action" title="Batalkan Pesanan" onclick="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini? Jika sudah dibayar, uang akan dikembalikan.')">
                                                <i class="fas fa-times"></i>
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php 
                                    endwhile; 
                                else: 
                                ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fas fa-history fa-3x mb-3 opacity-25"></i><br>
                                        Belum ada riwayat pesanan.
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>