<?php
session_start();
require_once "config/koneksi.php";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Pakaian Adat Bajawa - Koleksi Nusantara</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; overflow-x: hidden; }
        .navbar { box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        
        /* Product Cards */
        .product-card { border: none; border-radius: 15px; transition: transform 0.3s; overflow: hidden; height: 100%; background: #fff; }
        .product-card:hover { transform: translateY(-8px); box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important; }
        .product-img-wrapper { position: relative; width: 100%; padding-top: 125%; /* 4:5 Aspect Ratio */ overflow: hidden; }
        .product-img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; }
        
        /* Hero Section */
        .hero-section { 
            background: linear-gradient(rgba(13, 110, 253, 0.6), rgba(13, 110, 253, 0.6)), url('https://sultansinindonesieblog.wordpress.com/wp-content/uploads/2021/12/1-ng.jpg'); 
            background-size: cover; 
            background-position: center; 
            color: white; 
            padding: 80px 0; 
            margin-bottom: 40px; 
            border-radius: 0 0 30px 30px; 
        }

        @media (max-width: 768px) {
            .hero-section { padding: 50px 0; border-radius: 0 0 20px 20px; }
            .hero-section h1 { font-size: 1.8rem; }
            .product-card .card-body { padding: 15px !important; }
            .product-card h6 { font-size: 0.9rem; }
            .product-card h5 { font-size: 1rem; }
            .btn-cart-sm { padding: 6px; font-size: 0.8rem; }
        }

        .search-bar { max-width: 500px; width: 100%; }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="index.php">
            <i class="fas fa-store me-2"></i>Toko Adat
        </a>
        
        <div class="d-flex align-items-center d-lg-none gap-2">
            <a href="keranjang.php" class="nav-link position-relative me-2">
                <i class="fas fa-shopping-cart fs-5 text-white"></i>
                <?php if(isset($_SESSION['keranjang']) && count($_SESSION['keranjang']) > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 9px;">
                    <?= count($_SESSION['keranjang']) ?>
                </span>
                <?php endif; ?>
            </a>
            <button class="navbar-toggler border-0 p-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>

        <div class="collapse navbar-collapse" id="navbarNav">
            <form class="d-flex mx-auto search-bar my-3 my-lg-0">
                <div class="input-group">
                    <input class="form-control border-0 rounded-start-pill ps-4" type="search" placeholder="Cari pakaian adat...">
                    <button class="btn btn-light rounded-end-pill px-4" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            <ul class="navbar-nav align-items-center">
                <li class="nav-item d-none d-lg-block me-3">
                    <a href="keranjang.php" class="nav-link position-relative">
                        <i class="fas fa-shopping-cart fs-5 text-white"></i>
                        <?php if(isset($_SESSION['keranjang']) && count($_SESSION['keranjang']) > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 10px;">
                            <?= count($_SESSION['keranjang']) ?>
                        </span>
                        <?php endif; ?>
                    </a>
                </li>
                
                <?php if(isset($_SESSION['id_user'])): ?>
                    <li class="nav-item dropdown w-100 w-lg-auto mt-2 mt-lg-0">
                        <a class="btn btn-light btn-sm rounded-pill px-4 w-100 dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i> <?= $_SESSION['nama_user'] ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow mt-2">
                            <li><a class="dropdown-item" href="<?= $_SESSION['role_user'] == 'admin' ? 'dashboard-admin.php' : 'dashboard-pelanggan.php' ?>"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item w-100 w-lg-auto mt-2 mt-lg-0">
                        <a href="login.php" class="btn btn-light btn-sm rounded-pill px-4 fw-bold w-100">Login</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- HERO -->
<div class="hero-section text-center">
    <div class="container px-4">
        <h1 class="fw-bold mb-3">Pakaian Adat Bajawa NTT</h1>
        <p class="lead mb-4 opacity-90">Koleksi pakaian tradisional terbaik langsung dari pengrajin lokal.</p>
        <a href="#produk" class="btn btn-light btn-lg rounded-pill px-5 shadow text-primary fw-bold">Jelajahi Sekarang</a>
    </div>
</div>

<div class="container mb-5 px-3 px-lg-0" id="produk">
    <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-pill shadow-sm border-0 px-4 mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> Berhasil masuk keranjang!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-3 g-lg-4">
        <?php
        $produk = mysqli_query($conn, "SELECT * FROM produk");
        while($row = mysqli_fetch_assoc($produk)){
            $path = "uploads/".$row['gambar_produk'];
        ?>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card product-card shadow-sm">
                <div class="product-img-wrapper">
                    <?php if(file_exists($path) && $row['gambar_produk'] != ""): ?>
                        <img src="<?= $path ?>" class="product-img">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/300x400?text=No+Image" class="product-img">
                    <?php endif; ?>
                </div>

                <div class="card-body text-center p-3 p-lg-4">
                    <h6 class="fw-bold mb-1 text-truncate"><?= $row['nama_produk']; ?></h6>
                    <h5 class="text-primary fw-bold mb-2">Rp <?= number_format($row['harga_produk'],0,',','.'); ?></h5>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3 d-none d-md-flex">
                        <small class="text-muted"><i class="fas fa-box me-1"></i> <?= $row['jumlah_produk']; ?> pcs</small>
                        <small class="text-warning"><i class="fas fa-star me-1"></i> 4.8</small>
                    </div>

                    <?php if($row['jumlah_produk'] > 0){ ?>
                        <?php if(isset($_SESSION['id_user'])): ?>
                            <a href="keranjang-tambah.php?id=<?= $row['id_produk']; ?>" class="btn btn-primary w-100 rounded-pill py-2 btn-cart-sm shadow-sm">
                                <i class="fas fa-cart-plus me-1 me-md-2"></i> Tambah
                            </a>
                        <?php else: ?>
                            <button type="button" class="btn btn-outline-primary w-100 rounded-pill py-2 btn-cart-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#loginRequiredModal">
                                <i class="fas fa-cart-plus me-1 me-md-2"></i> Tambah
                            </button>
                        <?php endif; ?>
                    <?php } else { ?>
                        <button class="btn btn-secondary w-100 rounded-pill py-2 btn-cart-sm" disabled>Habis</button>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

<!-- Modal Login Required -->
<div class="modal fade" id="loginRequiredModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mx-3 mx-sm-auto">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-body text-center p-4 p-lg-5">
                <div class="mb-4">
                    <i class="fas fa-user-lock fa-3x text-primary opacity-25"></i>
                </div>
                <h4 class="fw-bold mb-3">Mau Belanja?</h4>
                <p class="text-muted mb-4 small">Silakan login terlebih dahulu untuk menambahkan produk ke keranjang Anda.</p>
                <div class="d-grid gap-2">
                    <a href="login.php" class="btn btn-primary rounded-pill py-2 fw-bold">Login Sekarang</a>
                    <button type="button" class="btn btn-light rounded-pill py-2" data-bs-dismiss="modal">Nanti Saja</button>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="bg-white py-5 mt-5 border-top">
    <div class="container text-center px-4">
        <h5 class="fw-bold mb-2 text-primary">Toko Adat Bajawa</h5>
        <p class="text-muted mb-4 small">Lestarikan budaya bangsa dengan produk berkualitas.</p>
        <p class="text-muted mb-0 small">&copy; <?= date("Y") ?> Sistem Penjualan.</p>
    </div>
</footer>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>