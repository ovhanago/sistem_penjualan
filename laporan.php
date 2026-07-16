<?php
session_start();
require_once "config/koneksi.php";

// Ambil periode dari URL, default: all
$filter = isset($_GET['period']) ? $_GET['period'] : 'all';
$where_clause = "";
$label_periode = "Semua Waktu";

// Gunakan tanggal PHP untuk sinkronisasi
$today = date('Y-m-d');

if ($filter == 'today') {
    $where_clause = "WHERE DATE(pesanan.tanggal_pesanan) = '$today'";
    $label_periode = "Hari Ini (" . date('d M Y') . ")";
} elseif ($filter == 'weekly') {
    $last_week = date('Y-m-d', strtotime('-7 days'));
    $where_clause = "WHERE pesanan.tanggal_pesanan >= '$last_week'";
    $label_periode = "Mingguan (" . date('d M Y', strtotime('-7 days')) . " - " . date('d M Y') . ")";
} elseif ($filter == 'monthly') {
    $current_month = date('m');
    $current_year = date('Y');
    $where_clause = "WHERE MONTH(pesanan.tanggal_pesanan) = '$current_month' AND YEAR(pesanan.tanggal_pesanan) = '$current_year'";
    $label_periode = "Bulan Ini (" . date('F Y') . ")";
}

// Query Laporan Penjualan dengan Filter
$query_penjualan = mysqli_query($conn, "SELECT pesanan.*, user.nama_user 
                                        FROM pesanan 
                                        JOIN user ON pesanan.id_user = user.id_user 
                                        $where_clause
                                        ORDER BY tanggal_pesanan DESC");

// Hitung Total Pendapatan & Jumlah Pesanan untuk Ringkasan
$total_pendapatan = 0;
$jumlah_pesanan = 0;
$data_penjualan = [];
while($row = mysqli_fetch_assoc($query_penjualan)) {
    $data_penjualan[] = $row;
    // Mendukung status 'Success' atau 'Selesai'
    if(strtolower($row['status_pesanan']) == 'success' || strtolower($row['status_pesanan']) == 'selesai') {
        $total_pendapatan += $row['total_harga'];
    }
    $jumlah_pesanan++;
}

// Query Laporan Produk dengan Filter (Produk yang terjual pada periode tersebut)
if ($filter != 'all') {
    $query_produk = mysqli_query($conn, "SELECT DISTINCT produk.* FROM produk 
                                         JOIN pesanan_detail ON produk.id_produk = pesanan_detail.id_produk
                                         JOIN pesanan ON pesanan_detail.id_transaksi = pesanan.id_pesanan
                                         $where_clause
                                         ORDER BY nama_produk ASC");
} else {
    $query_produk = mysqli_query($conn, "SELECT * FROM produk ORDER BY nama_produk ASC");
}

// Query Laporan Pelanggan dengan Filter (Pelanggan yang melakukan transaksi pada periode tersebut)
if ($filter != 'all') {
    $query_pelanggan = mysqli_query($conn, "SELECT DISTINCT user.* FROM user 
                                            JOIN pesanan ON user.id_user = pesanan.id_user 
                                            $where_clause
                                            AND user.role_user = 'pelanggan'
                                            ORDER BY nama_user ASC");
} else {
    $query_pelanggan = mysqli_query($conn, "SELECT * FROM user WHERE role_user = 'pelanggan' ORDER BY nama_user ASC");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Admin - Sistem Penjualan</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; }
        .card-report { border: none; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        .nav-tabs { border-bottom: none; margin-bottom: 20px; }
        .nav-link { border: none !important; color: #6c757d; font-weight: 600; padding: 12px 25px; border-radius: 10px !important; margin-right: 10px; transition: all 0.3s; }
        .nav-link.active { background-color: #0d6efd !important; color: white !important; box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3); }
        .table thead th { background-color: #f8f9fa; border: none; color: #495057; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; padding: 15px; }
        .table td { padding: 15px; vertical-align: middle; border-bottom: 1px solid #f1f1f1; font-size: 14px; }
        .badge-status { font-weight: 500; padding: 6px 12px; border-radius: 8px; }
        .stat-card { border-radius: 12px; border: 1px solid #edf2f7; transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-5px); }
        
        @media print {

    .badge-status{
        color:#000 !important;
        background:none !important;
        border:none !important;
        font-weight:bold;
    }

    .bg-success,
    .bg-danger,
    .bg-warning,
    .bg-secondary{
        background:transparent !important;
    }

    .text-white,
    .text-dark{
        color:#000 !important;
    }
}
    </style>
</head>
<body>

<div class="container py-5">
    <!-- Header Khusus Cetak (Logo & Info Toko) -->
    <div class="d-none d-print-block text-center mb-4">
        <h1 class="fw-bold text-primary mb-1" style="font-size: 36px;">TOKO ADAT BAJAWA</h1>
        <p class="mb-3 fs-5">Koleksi Pakaian Adat Bajawa NTT Berkualitas</p>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <form method="GET" class="d-flex gap-2 filter-section">
            <select name="period" class="form-select rounded-pill shadow-sm" style="width: 200px;" onchange="this.form.submit()">
                <option value="all" <?= $filter == 'all' ? 'selected' : '' ?>>Semua Waktu</option>
                <option value="today" <?= $filter == 'today' ? 'selected' : '' ?>>Hari Ini</option>
                <option value="weekly" <?= $filter == 'weekly' ? 'selected' : '' ?>>Mingguan</option>
                <option value="monthly" <?= $filter == 'monthly' ? 'selected' : '' ?>>Bulanan</option>
            </select>
        </form>
        <button onclick="window.print()" class="btn btn-outline-dark btn-print rounded-pill px-4 shadow-sm">
            <i class="fas fa-print me-2"></i> Cetak Laporan
        </button>
    </div>

    <!-- Navigasi Tab -->
    <ul class="nav nav-tabs no-print" id="reportTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales" type="button">
                <i class="fas fa-shopping-cart me-2"></i> Penjualan
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="products-tab" data-bs-toggle="tab" data-bs-target="#products" type="button">
                <i class="fas fa-box me-2"></i> Produk
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="customers-tab" data-bs-toggle="tab" data-bs-target="#customers" type="button">
                <i class="fas fa-users me-2"></i> Pelanggan
            </button>
        </li>
    </ul>

    <div class="tab-content" id="reportTabsContent">
        
        <!-- Tab Laporan Penjualan -->
        <div class="tab-pane fade show active" id="sales" role="tabpanel">
            <!-- Judul & Periode Cetak -->
            <div class="d-none d-print-block text-center mb-4 py-3 border-top border-bottom" style="border-width: 2px !important; border-color: #000 !important;">
                <h3 class="fw-bold text-uppercase mb-1" style="color: #000 !important;">LAPORAN PENJUALAN</h3>
                <h5 class="text-muted mb-0"><?= strtoupper($label_periode) ?></h5>
            </div>

            <div class="card card-report bg-white p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 no-print">
                    <h5 class="fw-bold mb-0">Laporan Penjualan (<?= $label_periode ?>)</h5>
                </div>

                <!-- Ringkasan Statistik -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="stat-card p-3 bg-light">
                            <span class="text-muted small d-block">Total Transaksi (<?= $label_periode ?>)</span>
                            <h4 class="fw-bold mb-0"><?= $jumlah_pesanan ?> Pesanan</h4>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="stat-card p-3 bg-primary text-white">
                            <span class="text-white-50 small d-block">Total Pendapatan (Status Success/Selesai)</span>
                            <h4 class="fw-bold mb-0">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></h4>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tanggal</th>
                                <th>Pelanggan</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($data_penjualan)) : ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Tidak ada data untuk periode ini.</td>
                                </tr>
                            <?php else : ?>
                                <?php foreach($data_penjualan as $row) : ?>
                                <tr>
                                    <td class="fw-bold text-muted">#<?= $row['id_pesanan'] ?></td>
                                    <td><?= date('d M Y, H:i', strtotime($row['tanggal_pesanan'])) ?></td>
                                    <td><?= $row['nama_user'] ?></td>
                                    <td class="fw-bold text-primary">Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                                    <td>
                                        <?php 
                                        $status_class = 'bg-secondary text-white';
                                        if($row['status_pesanan'] == 'Success' || $row['status_pesanan'] == 'Selesai') $status_class = 'bg-success text-white';
                                        elseif($row['status_pesanan'] == 'Pending') $status_class = 'bg-warning text-dark';
                                        elseif($row['status_pesanan'] == 'Failed') $status_class = 'bg-danger text-white';
                                        ?>
                                        <span class="badge badge-status <?= $status_class ?>"><?= $row['status_pesanan'] ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tanda Tangan Khusus Cetak -->
            <div class="d-none d-print-block mt-5">
                <div class="row">
                    <div class="col-8"></div>
                    <div class="col-4 text-center">
                        <p class="mb-5">Bajawa, <?= date('d F Y') ?></p>
                        <br><br>
                        <h6 class="fw-bold mb-0">Tiara Radho</h6>
                        <hr class="mx-auto" style="width: 150px; opacity: 1; border-top: 2px solid black;">
                        <p class="small text-muted">Pemilik Toko</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Laporan Produk -->
        <div class="tab-pane fade" id="products" role="tabpanel">
            <!-- Judul & Periode Cetak -->
            <div class="d-none d-print-block text-center mb-4 py-3 border-top border-bottom">
                <h3 class="fw-bold text-uppercase mb-2">LAPORAN STOK PRODUK</h3>
                <h5 class="text-muted mb-0"><?= strtoupper($label_periode) ?> </h5>
            </div>

            <div class="card card-report bg-white p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 no-print">
                    <h5 class="fw-bold mb-0">Laporan Stok Produk (<?= $label_periode ?>)</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama Produk</th>
                                <th>Stok</th>
                                <th>Harga</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($query_produk) == 0) : ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Tidak ada data produk untuk periode ini.</td>
                                </tr>
                            <?php else : ?>
                                <?php while($row = mysqli_fetch_assoc($query_produk)) : ?>
                                <tr>
                                    <td class="fw-bold"><?= $row['nama_produk'] ?></td>
                                    <td><?= $row['jumlah_produk'] ?> Pcs</td>
                                    <td class="text-primary">Rp <?= number_format($row['harga_produk'], 0, ',', '.') ?></td>
                                    <td>
                                        <?php 
                                        $stok_status = $row['jumlah_produk'] > 0 ? 'Tersedia' : 'Habis';
                                        $stok_badge = $row['jumlah_produk'] > 0 ? 'bg-success' : 'bg-danger';
                                        ?>
                                        <span class="badge badge-status <?= $stok_badge ?> text-white">
                                            <?= $stok_status ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tanda Tangan Khusus Cetak -->
            <div class="d-none d-print-block mt-5">
                <div class="row">
                    <div class="col-8"></div>
                    <div class="col-4 text-center">
                        <p class="mb-5">Bajawa, <?= date('d F Y') ?></p>
                        <br><br>
                        <h6 class="fw-bold mb-0">Tiara Radho</h6>
                        <hr class="mx-auto" style="width: 150px; opacity: 1; border-top: 2px solid black;">
                        <p class="small text-muted">Pemilik Toko</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Laporan Pelanggan -->
        <div class="tab-pane fade" id="customers" role="tabpanel">
            <!-- Judul & Periode Cetak -->
            <div class="d-none d-print-block text-center mb-4 py-3 border-top border-bottom">
                <h3 class="fw-bold text-uppercase mb-2">LAPORAN PELANGGAN</h3>
                <h5 class="text-muted mb-0"><?= strtoupper($label_periode) ?> (UPDATE: <?= date('d M Y') ?>)</h5>
            </div>

            <div class="card card-report bg-white p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 no-print">
                    <h5 class="fw-bold mb-0">Laporan Pelanggan (<?= $label_periode ?>)</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>No. Telp</th>
                                <th>Alamat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($query_pelanggan) == 0) : ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Tidak ada data pelanggan untuk periode ini.</td>
                                </tr>
                            <?php else : ?>
                                <?php while($row = mysqli_fetch_assoc($query_pelanggan)) : ?>
                                <tr>
                                    <td class="fw-bold"><?= $row['nama_user'] ?></td>
                                    <td class="text-muted"><?= $row['username_user'] ?></td>
                                    <td><?= $row['telp_user'] ?></td>
                                    <td class="small text-muted"><?= $row['alamat_user'] ?></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tanda Tangan Khusus Cetak -->
            <div class="d-none d-print-block mt-5">
                <div class="row">
                    <div class="col-8"></div>
                    <div class="col-4 text-center">
                        <p class="mb-5">Bajawa, <?= date('d F Y') ?></p>
                        <br><br>
                        <h6 class="fw-bold mb-0">Tiara Radho</h6>
                        <hr class="mx-auto" style="width: 150px; opacity: 1; border-top: 2px solid black;">
                        <p class="small text-muted">Pemilik Toko</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Navigasi Kembali ke Dashboard -->
    <div class="mt-4 text-center no-print back-nav">
        <?php
        $back_link = "dashboard-admin.php";
        if(isset($_SESSION['role_user']) && $_SESSION['role_user'] != 'admin') $back_link = "dashboard-" . $_SESSION['role_user'] . ".php";
        ?>
        <a href="<?= $back_link ?>" class="btn btn-link text-decoration-none text-muted">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
    </div>

</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>