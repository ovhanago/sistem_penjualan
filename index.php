<?php
session_start();
require_once "config/koneksi.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Toko Pakaian Adat Bajawa - Koleksi Nusantara</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .navbar { box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .product-card { border: none; border-radius: 15px; transition: transform 0.3s; overflow: hidden; height: 100%; }
        .product-card:hover { transform: translateY(-10px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
        .product-img { height: 250px; object-fit: cover; }
        .badge-category { position: absolute; top: 15px; left: 15px; background: rgba(13, 110, 253, 0.9); color: white; padding: 5px 15px; border-radius: 20px; font-size: 12px; }
        .hero-section { background: linear-gradient(rgba(13, 110, 253, 0.5), rgba(13, 110, 253, 0.5)), url('https://sultansinindonesieblog.wordpress.com/wp-content/uploads/2021/12/1-ng.jpg'); background-size: cover; background-position: center; color: white; padding: 100px 0; margin-bottom: 50px; border-radius: 0 0 50px 50px; }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="index.php">
            <i class="fas fa-store me-2"></i>Toko Adat
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <form class="d-flex mx-auto w-50">
                <div class="input-group">
                    <input class="form-control border-0 rounded-start-pill ps-4" type="search" placeholder="Cari pakaian adat...">
                    <button class="btn btn-light rounded-end-pill px-4" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            <ul class="navbar-nav align-items-center">
                <li class="nav-item me-2">
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
                    <li class="nav-item dropdown">
                        <a class="btn btn-light btn-sm rounded-pill px-3 dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i> <?= $_SESSION['nama_user'] ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow mt-2">
                            <li><a class="dropdown-item" href="<?= $_SESSION['role_user'] == 'admin' ? 'dashboard-admin.php' : 'dashboard-pelanggan.php' ?>"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a href="login.php" class="btn btn-light btn-sm rounded-pill px-4 fw-bold">Login</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- HERO -->
<div class="hero-section text-center">
    <div class="container">
        <p class="lead mb-4">Temukan keindahan budaya Indonesia melalui berbagai pilihan pakaian adat terbaik.</p>
        <a href="#produk" class="btn btn-light btn-lg rounded-pill px-5 shadow text-primary fw-bold">Jelajahi Sekarang</a>
    </div>
</div>

<div class="container mb-5" id="produk">
    <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-pill shadow-sm border-0 px-4 mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> Produk berhasil ditambahkan ke keranjang!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <?php
        $produk = mysqli_query($conn, "SELECT * FROM produk");
        while($row = mysqli_fetch_assoc($produk)){
            $path = "uploads/".$row['gambar_produk'];
        ?>
        <div class="col-md-3">
            <div class="card product-card shadow-sm position-relative">
                
                <?php if(file_exists($path) && $row['gambar_produk'] != ""): ?>
                    <img src="<?= $path ?>" class="card-img-top product-img">
                <?php else: ?>
                    <img src="https://via.placeholder.com/300x400?text=No+Image" class="card-img-top product-img">
                <?php endif; ?>

                <div class="card-body text-center p-4">
                    <h6 class="fw-bold mb-2"><?= $row['nama_produk']; ?></h6>
                    <h5 class="text-primary fw-bold mb-3">Rp <?= number_format($row['harga_produk'],0,',','.'); ?></h5>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <small class="text-muted"><i class="fas fa-box me-1"></i> Stok: <?= $row['jumlah_produk']; ?></small>
                        <small class="text-warning"><i class="fas fa-star me-1"></i> 4.8</small>
                    </div>

                    <?php if($row['jumlah_produk'] > 0){ ?>
                        <?php if(isset($_SESSION['id_user'])): ?>
                            <a href="keranjang-tambah.php?id=<?= $row['id_produk']; ?>" class="btn btn-primary w-100 rounded-pill py-2 shadow-sm">
                                <i class="fas fa-cart-plus me-2"></i> Tambah
                            </a>
                        <?php else: ?>
                            <button type="button" class="btn btn-outline-primary w-100 rounded-pill py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#loginRequiredModal">
                                <i class="fas fa-cart-plus me-2"></i> Tambah
                            </button>
                        <?php endif; ?>
                    <?php } else { ?>
                        <button class="btn btn-secondary w-100 rounded-pill py-2" disabled>Stok Habis</button>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

<!-- Modal Login Required -->
<div class="modal fade" id="loginRequiredModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-body text-center p-5">
                <div class="mb-4">
                    <i class="fas fa-user-lock fa-4x text-primary opacity-25"></i>
                </div>
                <h4 class="fw-bold mb-3">Mau Belanja?</h4>
                <p class="text-muted mb-4">Silakan login terlebih dahulu untuk menambahkan produk favorit Anda ke keranjang belanja.</p>
                <div class="d-grid gap-2">
                    <a href="login.php" class="btn btn-primary rounded-pill py-2 fw-bold">Login Sekarang</a>
                    <button type="button" class="btn btn-light rounded-pill py-2" data-bs-dismiss="modal">Nanti Saja</button>
                </div>
                <div class="mt-3 small">
                    Belum punya akun? <a href="register.php" class="text-primary text-decoration-none fw-bold">Daftar di sini</a>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="bg-white py-5 mt-5 shadow-sm">
    <div class="container text-center">
        <h5 class="fw-bold mb-3 text-primary">Toko Pakaian Adat Bajawa</h5>
        <p class="text-muted mb-4 small">Lestarikan budaya bangsa dengan memakai produk lokal berkualitas.</p>
        <div class="mb-4">
            <a href="#" class="btn btn-light btn-sm rounded-circle me-2"><i class="fab fa-instagram"></i></a>
            <a href="#" class="btn btn-light btn-sm rounded-circle me-2"><i class="fab fa-facebook"></i></a>
            <a href="#" class="btn btn-light btn-sm rounded-circle"><i class="fab fa-whatsapp"></i></a>
        </div>
        <p class="text-muted mb-0 small">&copy; <?= date("Y") ?> Sistem Penjualan. All Rights Reserved.</p>
    </div>
</footer>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>