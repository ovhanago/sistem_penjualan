<?php
session_start();
require_once "config/koneksi.php";

// Proteksi halaman Pemilik
if(!isset($_SESSION['id_user']) || $_SESSION['role_user'] != 'pemilik'){
    header("Location: login.php");
    exit;
}

// Statistik Ringkasan
$total_produk = mysqli_num_rows(mysqli_query($conn, "SELECT id_produk FROM produk"));
$total_pelanggan = mysqli_num_rows(mysqli_query($conn, "SELECT id_user FROM user WHERE role_user = 'pelanggan'"));
$total_pesanan = mysqli_num_rows(mysqli_query($conn, "SELECT id_pesanan FROM pesanan"));

$income_q = mysqli_query($conn, "SELECT SUM(total_harga) as total FROM pesanan WHERE status_pesanan = 'Selesai'");
$res_income = mysqli_fetch_assoc($income_q);
$total_pendapatan = $res_income['total'] ?? 0;

// Data untuk Grafik Sederhana (Pesanan per Bulan)
$monthly_sales = [];
for($i=1; $i<=12; $i++){
    $month = str_pad($i, 2, "0", STR_PAD_LEFT);
    $year = date('Y');
    $q = mysqli_query($conn, "SELECT SUM(total_harga) as total FROM pesanan WHERE status_pesanan = 'Selesai' AND MONTH(tanggal_pesanan) = '$month' AND YEAR(tanggal_pesanan) = '$year'");
    $r = mysqli_fetch_assoc($q);
    $monthly_sales[] = $r['total'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pemilik - Toko Adat</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root { --primary: #6610f2; --secondary: #6f42c1; --bg: #f4f7f6; }
        body { background-color: var(--bg); font-family: 'Segoe UI', sans-serif; }
        .sidebar { width: 260px; height: 100vh; background: #fff; position: fixed; box-shadow: 2px 0 10px rgba(0,0,0,0.05); }
        .main-content { margin-left: 260px; padding: 30px; }
        .card-stat { border: none; border-radius: 15px; transition: transform 0.3s; overflow: hidden; }
        .card-stat:hover { transform: translateY(-5px); }
        .icon-box { width: 45px; height: 45px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        .nav-link { color: #6c757d; font-weight: 500; padding: 12px 20px; border-radius: 10px; margin-bottom: 5px; }
        .nav-link.active { background: var(--primary); color: #fff !important; }
        .nav-link:hover:not(.active) { background: rgba(102, 16, 242, 0.05); color: var(--primary); }
    </style>
</head>
<body>

<div class="sidebar d-flex flex-column p-4">
    <div class="mb-5 text-center">
        <h4 class="fw-bold text-primary"><i class="fas fa-crown me-2"></i>OWNER</h4>
        <small class="text-muted text-uppercase letter-spacing-1">Laporan Bussines</small>
    </div>
    
    <nav class="nav flex-column">
        <a class="nav-link active" href="laporan.php"><i class="fas fa-file-invoice-dollar me-2"></i> Laporan</a>
    </nav>

    <div class="mt-auto">
        <a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
    </div>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Selamat Datang, <?= $_SESSION['nama_user'] ?>!</h3>
            <p class="text-muted small">Berikut adalah Laporan Toko Pakaian Adat hari ini.</p>
        </div>
        <div class="text-end">
            <span class="badge bg-white text-dark shadow-sm p-2 rounded-pill px-3">
                <i class="fas fa-calendar-alt me-2 text-primary"></i> <?= date('d M Y') ?>
            </span>
        </div>
    </div>

    <div class="row g-4">
        <!-- Grafik Penjualan -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-4 rounded-4">
                <h5 class="fw-bold mb-4">Grafik Pendapatan Tahun <?= date('Y') ?></h5>
                <canvas id="salesChart" height="150"></canvas>
            </div>
        </div>
        
        <!-- Pesanan Terbaru -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 rounded-4">
                <h5 class="fw-bold mb-4">Transaksi Terakhir</h5>
                <div class="table-responsive">
                    <table class="table table-borderless align-middle">
                        <tbody>
                            <?php 
                            $q_recent = mysqli_query($conn, "SELECT pesanan.*, user.nama_user FROM pesanan JOIN user ON pesanan.id_user = user.id_user ORDER BY tanggal_pesanan DESC LIMIT 5");
                            while($r = mysqli_fetch_assoc($q_recent)):
                            ?>
                            <tr>
                                <td style="width: 45px;">
                                    <div class="icon-box bg-light text-primary"><i class="fas fa-user small"></i></div>
                                </td>
                                <td>
                                    <div class="fw-bold small"><?= $r['nama_user'] ?></div>
                                    <small class="text-muted"><?= date('d M', strtotime($r['tanggal_pesanan'])) ?></small>
                                </td>
                                <td class="text-end fw-bold small">
                                    Rp <?= number_format($r['total_harga'], 0, ',', '.') ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <a href="laporan.php" class="btn btn-light w-100 rounded-pill btn-sm mt-3">Lihat Semua Laporan</a>
            </div>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: <?= json_encode($monthly_sales) ?>,
                borderColor: '#6610f2',
                backgroundColor: 'rgba(102, 16, 242, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#6610f2'
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { display: false } },
                x: { grid: { display: false } }
            }
        }
    });
</script>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>