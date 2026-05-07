<?php
session_start();
require_once "config/koneksi.php";

// Proteksi halaman Pengirim
if(!isset($_SESSION['id_user']) || $_SESSION['role_user'] != 'pengirim'){
    header("Location: login.php");
    exit;
}

// Logika Update Status
if(isset($_POST['update_status'])){
    $id_pesanan = mysqli_real_escape_string($conn, $_POST['id_pesanan']);
    $status_baru = mysqli_real_escape_string($conn, $_POST['status_baru']);
    
    mysqli_query($conn, "UPDATE pesanan SET status_pesanan = '$status_baru' WHERE id_pesanan = '$id_pesanan'");
    header("Location: dashboard-pengirim.php?msg=success");
    exit;
}

// Statistik Sederhana
$count_pending = mysqli_num_rows(mysqli_query($conn, "SELECT id_pesanan FROM pesanan WHERE status_pesanan = 'Pending'"));
$count_dikirim = mysqli_num_rows(mysqli_query($conn, "SELECT id_pesanan FROM pesanan WHERE status_pesanan = 'Dikirim'"));
$count_selesai = mysqli_num_rows(mysqli_query($conn, "SELECT id_pesanan FROM pesanan WHERE status_pesanan = 'Selesai'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengirim - Toko Adat</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary: #0d6efd; --bg: #f4f7f6; }
        body { background-color: var(--bg); font-family: 'Segoe UI', sans-serif; overflow-x: hidden; }
        .sidebar { 
            width: 260px; 
            height: 100vh; 
            background: #fff; 
            position: fixed; 
            box-shadow: 2px 0 10px rgba(0,0,0,0.05); 
            z-index: 1000;
            transition: all 0.3s;
        }
        .main-content { margin-left: 260px; padding: 30px; transition: all 0.3s; }
        
        @media (max-width: 992px) {
            .sidebar { left: -260px; }
            .sidebar.active { left: 0; }
            .main-content { margin-left: 0; padding: 15px; }
        }

        .card-custom { border: none; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
        .nav-link { color: #6c757d; font-weight: 500; padding: 12px 20px; border-radius: 10px; margin-bottom: 5px; }
        .nav-link.active { background: var(--primary); color: #fff !important; }
        .status-badge { font-size: 12px; padding: 6px 12px; border-radius: 20px; }
    </style>
</head>
<body>

<div class="sidebar d-flex flex-column p-4" id="sidebar">
    <div class="mb-5 text-center d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold text-primary mb-0"><i class="fas fa-truck me-2"></i>KURIR</h4>
            <small class="text-muted text-uppercase">Delivery Management</small>
        </div>
        <button class="btn d-lg-none" onclick="toggleSidebar()"><i class="fas fa-times"></i></button>
    </div>
    
    <nav class="nav flex-column">
        <a class="nav-link active" href="#"><i class="fas fa-tasks me-2"></i> Tugas Pengiriman</a>
        <a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
    </nav>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <button class="btn btn-primary btn-sm rounded-circle me-3 d-lg-none" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <div>
                <h3 class="fw-bold mb-0">Halo, <?= $_SESSION['nama_user'] ?>!</h3>
                <p class="text-muted small d-none d-sm-block">Kelola pengiriman paket pelanggan hari ini.</p>
            </div>
        </div>
        <div class="text-end">
            <span class="badge bg-white text-dark shadow-sm p-2 rounded-pill px-3">
                <i class="fas fa-calendar-alt me-2 text-primary"></i> <?= date('d M Y') ?>
            </span>
        </div>
    </div>


    <!-- Statistik Singkat -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card card-custom p-3 bg-white border-start border-warning border-4">
                <div class="d-flex align-items-center">
                    <div class="icon-box me-3 text-warning fs-3"><i class="fas fa-clock"></i></div>
                    <div>
                        <p class="text-muted mb-0 small">Perlu Diproses</p>
                        <h4 class="fw-bold mb-0"><?= $count_pending ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom p-3 bg-white border-start border-primary border-4">
                <div class="d-flex align-items-center">
                    <div class="icon-box me-3 text-primary fs-3"><i class="fas fa-shipping-fast"></i></div>
                    <div>
                        <p class="text-muted mb-0 small">Sedang Dikirim</p>
                        <h4 class="fw-bold mb-0"><?= $count_dikirim ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom p-3 bg-white border-start border-success border-4">
                <div class="d-flex align-items-center">
                    <div class="icon-box me-3 text-success fs-3"><i class="fas fa-check-circle"></i></div>
                    <div>
                        <p class="text-muted mb-0 small">Terkirim</p>
                        <h4 class="fw-bold mb-0"><?= $count_selesai ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Pesanan -->
    <div class="card card-custom shadow-sm overflow-hidden">
        <div class="card-header bg-white py-3 border-0">
            <h5 class="fw-bold mb-0 text-dark">Daftar Tugas Pengiriman</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">No</th>
                            <th>ID Pesanan</th>
                            <th>Pelanggan / Alamat</th>
                            <th>Total Bayar</th>
                            <th>Status</th>
                            <th class="text-center pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        $query = mysqli_query($conn, "SELECT pesanan.*, user.nama_user, user.alamat_user, user.telp_user 
                                                FROM pesanan 
                                                JOIN user ON pesanan.id_user = user.id_user 
                                                WHERE status_pesanan IN ('Pending', 'Dikemas', 'Dikirim')
                                                ORDER BY tanggal_pesanan DESC");
                        
                        if(mysqli_num_rows($query) > 0):
                            while($r = mysqli_fetch_assoc($query)):
                                $status = $r['status_pesanan'];
                                $badge_class = 'bg-warning text-dark';
                                if($status == 'Dikemas') $badge_class = 'bg-info text-white';
                                if($status == 'Dikirim') $badge_class = 'bg-primary text-white';
                        ?>
                        <tr>
                            <td class="ps-4"><?= $no++ ?></td>
                            <td><span class="fw-bold text-primary">#ORD-<?= $r['id_pesanan'] ?></span></td>
                            <td>
                                <div class="fw-bold small"><?= $r['nama_user'] ?></div>
                                <div class="text-muted extra-small" style="font-size: 12px; max-width: 250px;">
                                    <i class="fas fa-map-marker-alt text-danger me-1"></i> <?= $r['alamat_user'] ?>
                                    <br><i class="fas fa-phone-alt text-success me-1"></i> <?= $r['telp_user'] ?>
                                </div>
                            </td>
                            <td class="fw-bold text-dark">Rp <?= number_format($r['total_harga'], 0, ',', '.') ?></td>
                            <td><span class="status-badge <?= $badge_class ?>"><?= $status ?></span></td>
                            <td class="text-center pe-4">
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="id_pesanan" value="<?= $r['id_pesanan'] ?>">
                                    <select name="status_baru" class="form-select form-select-sm d-inline-block w-auto rounded-pill me-2" required>
                                        <option value="">Update Status</option>
                                        <?php if($status == 'Dikemas' || $status == 'Pending'): ?>
                                            <option value="Dikirim">Kirim Sekarang</option>
                                        <?php endif; ?>
                                        <?php if($status == 'Dikirim'): ?>
                                            <option value="Selesai">Pesanan Diterima (Selesai)</option>
                                        <?php endif; ?>
                                    </select>
                                    <button type="submit" name="update_status" class="btn btn-sm btn-dark rounded-pill px-3 shadow-sm">Update</button>
                                </form>
                            </td>
                        </tr>
                        <?php 
                            endwhile; 
                        else: 
                        ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted">Tidak ada tugas pengiriman saat ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('active');
    }
</script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>