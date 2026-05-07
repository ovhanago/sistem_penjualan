<?php
session_name("SESS_ADMIN");
session_start();
require_once "config/koneksi.php";

if(!isset($_SESSION['id_user']) || $_SESSION['role_user'] != 'admin'){
    header("Location: login.php");
    exit;
}

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Toko Adat</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #0d6efd;
            --sidebar-bg: #ffffff;
            --main-bg: #f8f9fa;
        }
        body {
            background-color: var(--main-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }
        /* SIDEBAR */
        .sidebar {
            width: 260px;
            height: 100vh;
            background: var(--sidebar-bg);
            position: fixed;
            left: 0;
            top: 0;
            box-shadow: 4px 0 10px rgba(0,0,0,0.03);
            z-index: 1000;
            transition: all 0.3s;
        }
        .sidebar-header {
            padding: 30px 25px;
            border-bottom: 1px solid #f1f1f1;
        }
        .nav-list {
            padding: 20px 15px;
        }
        .nav-item {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: #6c757d;
            text-decoration: none;
            border-radius: 12px;
            margin-bottom: 5px;
            transition: all 0.2s;
        }
        .nav-item i {
            width: 25px;
            font-size: 18px;
            margin-right: 12px;
        }
        .nav-item:hover, .nav-item.active {
            background-color: rgba(13, 110, 253, 0.1);
            color: var(--primary-color);
            font-weight: 600;
        }
        /* MAIN CONTENT */
        .main-content {
            margin-left: 260px;
            padding: 30px;
            min-height: 100vh;
            transition: all 0.3s;
        }
        .top-navbar {
            background: #fff;
            padding: 15px 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        /* RESPONSIVE */
        @media (max-width: 992px) {
            .sidebar {
                left: -260px;
            }
            .sidebar.active {
                left: 0;
            }
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
            .top-navbar {
                padding: 10px 15px;
            }
        }
        /* CARDS */
        .stat-card {
            border: none;
            border-radius: 20px;
            padding: 20px;
            background: #fff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            transition: transform 0.3s;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .icon-box {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        /* TABLE */
        .card-table {
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            overflow: hidden;
        }
        .table thead { background-color: #f8f9fa; }
        .table th { border: none; padding: 15px; font-weight: 600; font-size: 14px; color: #6c757d; }
        .table td { padding: 15px; border-bottom: 1px solid #f8f9fa; vertical-align: middle; }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header d-flex justify-content-between align-items-center">
        <h4 class="fw-bold text-primary mb-0"><i class="fas fa-store me-2"></i>Admin Panel</h4>
        <button class="btn d-lg-none" onclick="toggleSidebar()"><i class="fas fa-times"></i></button>
    </div>
    <div class="nav-list">
        <a href="dashboard-admin.php?page=dashboard" class="nav-item <?= $page == 'dashboard' ? 'active' : '' ?>">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <a href="dashboard-admin.php?page=produk" class="nav-item <?= $page == 'produk' ? 'active' : '' ?>">
            <i class="fas fa-box"></i> Data Produk
        </a>
        <a href="dashboard-admin.php?page=pesanan" class="nav-item <?= $page == 'pesanan' ? 'active' : '' ?>">
            <i class="fas fa-shopping-bag"></i> Pesanan
        </a>
        <a href="dashboard-admin.php?page=pelanggan" class="nav-item <?= $page == 'pelanggan' ? 'active' : '' ?>">
            <i class="fas fa-users"></i> Pelanggan
        </a>
        <div class="mt-4 pt-4 border-top">
            <a href="logout.php" class="nav-item text-danger">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">
    
    <!-- TOP NAVBAR -->
    <div class="top-navbar">
        <div class="d-flex align-items-center">
            <button class="btn btn-primary btn-sm rounded-circle me-3 d-lg-none" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <h5 class="fw-bold mb-0 d-none d-sm-block"><?= ucfirst($page) ?> Overview</h5>
        </div>
        <div class="d-flex align-items-center">
            <div class="text-end me-3 d-none d-sm-block">
                <small class="text-muted d-block">Selamat datang,</small>
                <span class="fw-bold"><?= $_SESSION['nama_user'] ?></span>
            </div>
            <img src="https://ui-avatars.com/api/?name=<?= $_SESSION['nama_user'] ?>&background=0D6EFD&color=fff" class="rounded-circle" width="40">
        </div>
    </div>

    <?php if($page == 'dashboard'): ?>
        <!-- DASHBOARD STATS -->
        <div class="row g-3 g-md-4 mb-4">
            <div class="col-6 col-md-3">
                <div class="stat-card h-100">
                    <div class="d-flex justify-content-between flex-column flex-xl-row">
                        <div>
                            <p class="text-muted mb-1 small">Total Produk</p>
                            <h3 class="fw-bold mb-0">
                                <?php
                                $q_total_produk = mysqli_query($conn, "SELECT id_produk FROM produk");
                                echo $q_total_produk ? mysqli_num_rows($q_total_produk) : 0;
                                ?>
                            </h3>
                        </div>
                        <div class="icon-box bg-primary text-white mt-2 mt-xl-0">
                            <i class="fas fa-box"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card h-100">
                    <div class="d-flex justify-content-between flex-column flex-xl-row">
                        <div>
                            <p class="text-muted mb-1 small">Pesanan Baru</p>
                            <h3 class="fw-bold mb-0">
                                <?php
                                // Cek apakah tabel pesanan ada
                                $check_table = mysqli_query($conn, "SHOW TABLES LIKE 'pesanan'");
                                if(mysqli_num_rows($check_table) > 0) {
                                    $q_pesanan_baru = mysqli_query($conn, "SELECT id_pesanan FROM pesanan WHERE status_pesanan = 'Pending'");
                                    if(!$q_pesanan_baru) {
                                        $q_pesanan_baru = mysqli_query($conn, "SELECT id_pesanan FROM pesanan WHERE status = 'Pending'");
                                    }
                                    echo $q_pesanan_baru ? mysqli_num_rows($q_pesanan_baru) : 0;
                                } else {
                                    echo "0";
                                }
                                ?>
                            </h3>
                        </div>
                        <div class="icon-box bg-warning text-white mt-2 mt-xl-0">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card h-100">
                    <div class="d-flex justify-content-between flex-column flex-xl-row">
                        <div>
                            <p class="text-muted mb-1 small">Total Pelanggan</p>
                            <h3 class="fw-bold mb-0">
                                <?php
                                $q_total_pelanggan = mysqli_query($conn, "SELECT id_user FROM user WHERE role_user = 'pelanggan'");
                                echo $q_total_pelanggan ? mysqli_num_rows($q_total_pelanggan) : 0;
                                ?>
                            </h3>
                        </div>
                        <div class="icon-box bg-success text-white mt-2 mt-xl-0">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card h-100">
                    <div class="d-flex justify-content-between flex-column flex-xl-row">
                        <div>
                            <p class="text-muted mb-1 small">Pendapatan</p>
                            <?php 
                                $total_income = 0;
                                $check_table_income = mysqli_query($conn, "SHOW TABLES LIKE 'pesanan'");
                                if(mysqli_num_rows($check_table_income) > 0) {
                                    $income_q = mysqli_query($conn, "SELECT SUM(total_harga) as total FROM pesanan WHERE status_pesanan != 'Dibatalkan'");
                                    if(!$income_q) {
                                        $income_q = mysqli_query($conn, "SELECT SUM(total_harga) as total FROM pesanan WHERE status != 'Dibatalkan'");
                                    }
                                    $res_income = $income_q ? mysqli_fetch_assoc($income_q) : null;
                                    $total_income = $res_income['total'] ?? 0;
                                }
                            ?>
                            <h3 class="fw-bold mb-0 small" style="font-size: 1.1rem;">Rp <?= number_format($total_income, 0, ',', '.') ?></h3>
                        </div>
                        <div class="icon-box bg-info text-white mt-2 mt-xl-0">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>


    <?php if($page == 'produk'): ?>
        <div class="card card-table shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Manajemen Produk</h5>
                <a href="tambah-produk.php" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm">
                    <i class="fas fa-plus me-1"></i> Tambah Produk
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">No</th>
                                <th>Info Produk</th>
                                <th>Stok</th>
                                <th>Harga</th>
                                <th class="text-center pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $data_produk = mysqli_query($conn, "SELECT * FROM produk ORDER BY id_produk DESC");
                            $no = 1;
                            if(mysqli_num_rows($data_produk) > 0) :
                                while($row = mysqli_fetch_assoc($data_produk)) : 
                                    $path = "uploads/".$row['gambar_produk'];
                            ?>
                            <tr>
                                <td class="ps-4 text-muted"><?= $no++ ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if(file_exists($path) && $row['gambar_produk'] != ""): ?>
                                            <img src="<?= $path ?>" width="45" height="45" class="rounded me-3 shadow-sm" style="object-fit:cover;">
                                        <?php else: ?>
                                            <div style="width:45px; height:45px;" class="rounded me-3 bg-light d-flex align-items-center justify-content-center text-muted">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="fw-bold text-dark"><?= $row['nama_produk'] ?></div>
                                            <small class="text-muted">ID: #<?= $row['id_produk'] ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= $row['jumlah_produk'] ?> Pcs</span></td>
                                <td class="fw-bold text-primary">Rp <?= number_format($row['harga_produk'], 0, ',', '.') ?></td>
                                <td class="text-center pe-4">
                                    <a href="edit-produk.php?id=<?= $row['id_produk'] ?>" class="btn btn-light btn-sm rounded-circle me-1 text-warning shadow-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="hapus-produk.php?id=<?= $row['id_produk'] ?>" class="btn btn-light btn-sm rounded-circle text-danger shadow-sm" onclick="return confirm('Hapus produk ini?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php 
                                endwhile; 
                            else:
                            ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Belum ada data produk tersedia.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if($page == 'pelanggan'): ?>
        <div class="card card-table shadow-sm">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="mb-0 fw-bold text-primary"><i class="fas fa-users me-2"></i>Daftar Pelanggan Terdaftar</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">No</th>
                                <th>Nama Lengkap</th>
                                <th>Username</th>
                                <th>Kontak</th>
                                <th class="pe-4">Alamat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $query_pelanggan = mysqli_query($conn, "SELECT * FROM user WHERE role_user = 'pelanggan' ORDER BY nama_user ASC");
                            $no = 1;
                            if(mysqli_num_rows($query_pelanggan) > 0):
                                while($row = mysqli_fetch_assoc($query_pelanggan)):
                            ?>
                            <tr>
                                <td class="ps-4 text-muted"><?= $no++ ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= $row['nama_user'] ?></div>
                                    <small class="text-muted">ID: #<?= $row['id_user'] ?></small>
                                </td>
                                <td><span class="badge bg-light text-dark border fw-normal"><?= $row['username_user'] ?></span></td>
                                <td><i class="fas fa-phone-alt me-1 small text-muted"></i> <?= $row['telp_user'] ?></td>
                                <td class="pe-4"><small class="text-muted"><?= $row['alamat_user'] ?></small></td>
                            </tr>
                            <?php 
                                endwhile; 
                            else: 
                            ?>
                            <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada pelanggan yang mendaftar.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if($page == 'pesanan'): ?>
        <?php
        // Logika Update Status
        if(isset($_POST['update_status'])){
            $id_p = $_POST['id_pesanan'];
            $stt = $_POST['status_baru'];
            mysqli_query($conn, "UPDATE pesanan SET status_pesanan = '$stt' WHERE id_pesanan = '$id_p'");
            echo "<script>window.location='dashboard-admin.php?page=pesanan';</script>";
        }
        ?>
        <div class="card card-table shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold text-primary"><i class="fas fa-shopping-bag me-2"></i>Riwayat Pesanan Masuk</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">No</th>
                                <th>Pelanggan</th>
                                <th>ID Pesanan</th>
                                <th>Tgl Pesanan</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th class="pe-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no=1; 
                            $q = mysqli_query($conn, "SELECT pesanan.*, user.nama_user FROM pesanan JOIN user ON pesanan.id_user = user.id_user ORDER BY id_pesanan DESC");
                            if($q && mysqli_num_rows($q) > 0):
                                while($r=mysqli_fetch_assoc($q)):
                            ?>
                            <tr>
                                <td class="ps-4"><?= $no++ ?></td>
                                <td><?= $r['nama_user'] ?></td>
                                <td><span class="fw-bold">#ORD-<?= $r['id_pesanan'] ?></span></td>
                                <td><?= date('d M Y', strtotime($r['tanggal_pesanan'])) ?></td>
                                <td class="fw-bold text-primary">Rp <?= number_format($r['total_harga'], 0, ',', '.') ?></td>
                                <td>
                                    <?php 
                                        $badge = 'bg-warning';
                                        if($r['status_pesanan'] == 'Selesai') $badge = 'bg-success';
                                        if($r['status_pesanan'] == 'Dibatalkan') $badge = 'bg-danger';
                                    ?>
                                    <span class="badge rounded-pill <?= $badge ?>"><?= $r['status_pesanan'] ?></span>
                                </td>
                                <td class="pe-4 text-center">
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="id_pesanan" value="<?= $r['id_pesanan'] ?>">
                                        <select name="status_baru" onchange="this.form.submit()" class="form-select form-select-sm d-inline-block w-auto rounded-pill">
                                            <option value="">Update Status</option>
                                            <option value="Pending">Pending</option>
                                            <option value="Dikemas">Dikemas</option>
                                            <option value="Selesai">Selesai</option>
                                            <option value="Dibatalkan">Dibatalkan</option>
                                        </select>
                                        <input type="hidden" name="update_status" value="1">
                                    </form>
                                </td>
                            </tr>
                            <?php 
                                    endwhile; 
                                else: 
                                    echo '<tr><td colspan="7" class="text-center py-5 text-muted">Belum ada pesanan masuk</td></tr>';
                                endif;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('active');
    }
</script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>