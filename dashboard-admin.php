<?php
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
            z-index: 1050;
            transition: all 0.3s ease;
        }
        .sidebar-header {
            padding: 25px;
            border-bottom: 1px solid #f1f1f1;
            background: #fff;
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
            padding: 20px;
            min-height: 100vh;
            transition: all 0.3s ease;
        }
        .top-navbar {
            background: #fff;
            padding: 15px 25px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        /* MOBILE ADJUSTMENTS */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
                width: 280px;
            }
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
            .top-navbar {
                padding: 10px 15px;
                margin-bottom: 20px;
            }
        }

        /* CARDS & TABLES */
        .stat-card {
            border: none;
            border-radius: 20px;
            padding: 20px;
            background: #fff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            transition: transform 0.3s;
            height: 100%;
        }
        .icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        .card-table {
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            overflow: hidden;
            background: #fff;
        }
        .table thead th {
            background-color: #f8f9fa;
            border: none;
            padding: 15px;
            font-weight: 600;
            font-size: 13px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .table td {
            padding: 15px;
            vertical-align: middle;
            border-bottom: 1px solid #f8f9fa;
        }
        .btn-action {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header d-flex justify-content-between align-items-center">
        <h4 class="m-0 fw-bold text-primary"><i class="fas fa-store me-2"></i>Admin</h4>
        <button class="btn d-lg-none" onclick="toggleSidebar()"><i class="fas fa-times"></i></button>
    </div>
    <div class="nav-list">
        <a href="dashboard-admin.php?page=dashboard" class="nav-item <?= $page=='dashboard'?'active':'' ?>">
            <i class="fas fa-th-large"></i> Dashboard
        </a>
        <a href="dashboard-admin.php?page=produk" class="nav-item <?= $page=='produk'?'active':'' ?>">
            <i class="fas fa-box"></i> Data Produk
        </a>
        <a href="dashboard-admin.php?page=pesanan" class="nav-item <?= $page=='pesanan'?'active':'' ?>">
            <i class="fas fa-shopping-bag"></i> Pesanan
        </a>
        <a href="dashboard-admin.php?page=pelanggan" class="nav-item <?= $page=='pelanggan'?'active':'' ?>">
            <i class="fas fa-users"></i> Pelanggan
        </a>
        <a href="dashboard-admin.php?page=laporan" class="nav-item <?= $page=='laporan'?'active':'' ?>">
            <i class="fas fa-file-alt"></i> Laporan
        </a>
        <hr class="mx-3 opacity-10">
        <a href="logout.php" class="nav-item text-danger">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">
    <!-- TOP NAVBAR -->
    <div class="top-navbar">
        <div class="d-flex align-items-center">
            <button class="btn btn-light d-lg-none me-3 rounded-pill" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <h5 class="m-0 fw-bold d-none d-sm-block text-dark">
                <?= ucfirst($page) ?> Overview
            </h5>
        </div>
        <div class="d-flex align-items-center">
            <div class="text-end me-3 d-none d-md-block">
                <p class="m-0 fw-bold lh-1"><?= $_SESSION['nama_user'] ?></p>
                <small class="text-muted">Administrator</small>
            </div>
            <img src="https://ui-avatars.com/api/?name=<?= $_SESSION['nama_user'] ?>&background=0D6EFD&color=fff" class="rounded-circle shadow-sm" width="40" height="40">
        </div>
    </div>

    <!-- PAGE CONTENT -->
    <div class="container-fluid p-0">
        <?php if($page == 'dashboard'): ?>
            <!-- STATS -->
            <div class="row g-4 mb-4">
                <div class="col-6 col-lg-3">
                    <div class="stat-card">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary mb-3">
                            <i class="fas fa-box"></i>
                        </div>
                        <p class="text-muted mb-1 small fw-bold">PRODUK</p>
                        <h3 class="fw-bold m-0">
                            <?php echo mysqli_num_rows(mysqli_query($conn, "SELECT id_produk FROM produk")); ?>
                        </h3>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card">
                        <div class="icon-box bg-warning bg-opacity-10 text-warning mb-3">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <p class="text-muted mb-1 small fw-bold">PESANAN</p>
                        <h3 class="fw-bold m-0">
                            <?php echo mysqli_num_rows(mysqli_query($conn, "SELECT id_pesanan FROM pesanan WHERE status_pesanan != 'Selesai'")); ?>
                        </h3>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card">
                        <div class="icon-box bg-success bg-opacity-10 text-success mb-3">
                            <i class="fas fa-users"></i>
                        </div>
                        <p class="text-muted mb-1 small fw-bold">USER</p>
                        <h3 class="fw-bold m-0">
                            <?php echo mysqli_num_rows(mysqli_query($conn, "SELECT id_user FROM user WHERE role_user = 'pelanggan'")); ?>
                        </h3>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card">
                        <div class="icon-box bg-info bg-opacity-10 text-info mb-3">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <p class="text-muted mb-1 small fw-bold">PENDAPATAN</p>
                        <?php 
                            $q_inc = mysqli_query($conn, "SELECT SUM(total_harga) as total FROM pesanan WHERE status_pesanan = 'Selesai'");
                            $r_inc = mysqli_fetch_assoc($q_inc);
                        ?>
                        <h4 class="fw-bold m-0">Rp<?= number_format($r_inc['total'] ?? 0, 0, ',', '.') ?></h4>
                    </div>
                </div>
            </div>
            
            <!-- RECENT ORDERS -->
            <div class="card card-table">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold">Pesanan Terbaru</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Pelanggan</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $q_recent = mysqli_query($conn, "SELECT pesanan.*, user.nama_user FROM pesanan JOIN user ON pesanan.id_user = user.id_user ORDER BY id_pesanan DESC LIMIT 5");
                            while($r = mysqli_fetch_assoc($q_recent)):
                            ?>
                            <tr>
                                <td class="fw-bold">#<?= $r['id_pesanan'] ?></td>
                                <td><?= $r['nama_user'] ?></td>
                                <td class="fw-bold text-primary">Rp<?= number_format($r['total_harga'], 0, ',', '.') ?></td>
                                <td><span class="badge rounded-pill bg-light text-dark border"><?= $r['status_pesanan'] ?></span></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif($page == 'produk'): ?>
            <div class="card card-table">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold">Daftar Produk</h6>
                    <a href="tambah-produk.php" class="btn btn-primary btn-sm rounded-pill px-3">
                        <i class="fas fa-plus me-1"></i> Tambah
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Stok</th>
                                <th>Harga</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $q_prod = mysqli_query($conn, "SELECT * FROM produk ORDER BY id_produk DESC");
                            while($row = mysqli_fetch_assoc($q_prod)):
                                $img = "uploads/".$row['gambar_produk'];
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="<?= file_exists($img) && $row['gambar_produk'] ? $img : 'assets/img/no-image.png' ?>" width="40" height="40" class="rounded me-3 shadow-sm" style="object-fit:cover;">
                                        <div>
                                            <div class="fw-bold text-dark"><?= $row['nama_produk'] ?></div>
                                            <small class="text-muted">ID: #<?= $row['id_produk'] ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= $row['jumlah_produk'] ?></span></td>
                                <td class="fw-bold text-primary">Rp<?= number_format($row['harga_produk'], 0, ',', '.') ?></td>
                                <td class="text-center">
                                    <a href="edit-produk.php?id=<?= $row['id_produk'] ?>" class="btn btn-light btn-action text-warning me-1 border">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="hapus-produk.php?id=<?= $row['id_produk'] ?>" class="btn btn-light btn-action text-danger border" onclick="return confirm('Hapus produk?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif($page == 'pesanan'): ?>
            <div class="card card-table">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold">Semua Pesanan</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Pelanggan</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th class="text-center">Update</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if(isset($_POST['up_status'])){
                                $idp = $_POST['idp'];
                                $stts = $_POST['stts'];
                                mysqli_query($conn, "UPDATE pesanan SET status_pesanan='$stts' WHERE id_pesanan='$idp'");
                                echo "<script>window.location='dashboard-admin.php?page=pesanan';</script>";
                            }
                            $q_pes = mysqli_query($conn, "SELECT pesanan.*, user.nama_user FROM pesanan JOIN user ON pesanan.id_user = user.id_user ORDER BY id_pesanan DESC");
                            while($r = mysqli_fetch_assoc($q_pes)):
                            ?>
                            <tr>
                                <td class="fw-bold">#<?= $r['id_pesanan'] ?></td>
                                <td><?= $r['nama_user'] ?></td>
                                <td class="fw-bold text-primary">Rp<?= number_format($r['total_harga'], 0, ',', '.') ?></td>
                                <td>
                                    <?php 
                                        $clr = 'bg-warning';
                                        if($r['status_pesanan'] == 'Selesai') $clr = 'bg-success';
                                        if($r['status_pesanan'] == 'Dibatalkan') $clr = 'bg-danger';
                                    ?>
                                    <span class="badge rounded-pill <?= $clr ?>"><?= $r['status_pesanan'] ?></span>
                                </td>
                                <td class="text-center">
                                    <form method="POST" class="d-flex gap-1 justify-content-center">
                                        <input type="hidden" name="idp" value="<?= $r['id_pesanan'] ?>">
                                        <select name="stts" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                                            <option value="">Ganti</option>
                                            <option value="Pending">Pending</option>
                                            <option value="Dikemas">Dikemas</option>
                                            <option value="Selesai">Selesai</option>
                                            <option value="Dibatalkan">Dibatalkan</option>
                                        </select>
                                        <input type="hidden" name="up_status" value="1">
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
        <?php elseif($page == 'pelanggan'): ?>
            <div class="card card-table">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold">Daftar Pelanggan</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Telepon</th>
                                <th>Alamat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $q_usr = mysqli_query($conn, "SELECT * FROM user WHERE role_user='pelanggan' ORDER BY id_user DESC");
                            while($r = mysqli_fetch_assoc($q_usr)):
                            ?>
                            <tr>
                                <td><div class="fw-bold"><?= $r['nama_user'] ?></div></td>
                                <td><span class="badge bg-light text-dark border fw-normal"><?= $r['username_user'] ?></span></td>
                                <td><?= $r['telp_user'] ?></td>
                                <td><small class="text-muted"><?= $r['alamat_user'] ?></small></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php endif; ?>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('show');
    }
</script>
</body>
</html>