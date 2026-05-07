<?php
session_start();
require_once "config/koneksi.php";
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Pakaian Adat Bajawa - Koleksi Nusantara</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #0d6efd;
            --soft-bg: #f0f2f5;
            --card-shadow: 0 4px 15px rgba(0,0,0,0.05);
            --hover-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        body { background-color: var(--soft-bg); font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; padding-bottom: 70px; }
        .navbar { box-shadow: 0 2px 15px rgba(0,0,0,0.05); border-bottom: 1px solid rgba(0,0,0,0.05); }
        .product-card { border: none; border-radius: 20px; transition: all 0.3s ease; overflow: hidden; height: 100%; background: #fff; box-shadow: var(--card-shadow); }
        .product-card:hover { transform: translateY(-5px); box-shadow: var(--hover-shadow); }
        .product-img { height: 200px; object-fit: cover; transition: transform 0.5s ease; }
        .product-card:hover .product-img { transform: scale(1.05); }
        .hero-section { background: linear-gradient(135deg, rgba(13, 110, 253, 0.8), rgba(13, 110, 253, 0.6)), url('https://sultansinindonesieblog.wordpress.com/wp-content/uploads/2021/12/1-ng.jpg'); background-size: cover; background-position: center; color: white; padding: 120px 0; margin-bottom: 40px; border-radius: 0 0 40px 40px; }
        
        /* Bottom Navigation for Mobile */
        .bottom-nav { position: fixed; bottom: 0; left: 0; right: 0; background: #fff; display: none; justify-content: space-around; padding: 10px 0; box-shadow: 0 -2px 10px rgba(0,0,0,0.1); z-index: 1030; border-top-left-radius: 20px; border-top-right-radius: 20px; }
        .bottom-nav-item { text-align: center; color: #6c757d; text-decoration: none; font-size: 12px; transition: 0.3s; }
        .bottom-nav-item i { font-size: 20px; display: block; margin-bottom: 2px; }
        .bottom-nav-item.active { color: var(--primary-color); font-weight: bold; }
        
        /* Search Box */
        .search-box .form-control { border-radius: 50px; padding-left: 20px; border: 1px solid #eee; }
        .search-box .btn { border-radius: 50px; }

        @media (max-width: 768px) {
            body { padding-bottom: 80px; }
            .hero-section { padding: 60px 20px; border-radius: 0 0 30px 30px; margin-bottom: 30px; }
            .hero-section h1 { font-size: 1.75rem; line-height: 1.3; }
            .hero-section p { font-size: 0.9rem; }
            .bottom-nav { display: flex; }
            .navbar-search-desktop { display: none !important; }
            .product-img { height: 160px; }
            .card-body { padding: 12px !important; }
            .product-title { font-size: 0.9rem !important; height: 2.4em; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
            .product-price { font-size: 1.1rem !important; }
            .btn-cart-text { display: none; }
            .navbar-brand { font-size: 1.25rem !important; }
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top py-2">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="fas fa-store-alt me-2"></i>Toko Adat Bajawa
        </a>
        
        <div class="d-flex align-items-center d-lg-none">
            <a href="keranjang.php" class="text-white position-relative me-3">
                <i class="fas fa-shopping-cart fs-5"></i>
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
            <form class="d-flex mx-auto w-50 navbar-search-desktop my-2 my-lg-0 search-box">
                <div class="input-group">
                    <input class="form-control border-0 shadow-sm" type="search" placeholder="Cari pakaian adat bajawa...">
                    <button class="btn btn-light shadow-sm text-primary px-3" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            <ul class="navbar-nav align-items-center ms-auto">
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
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle bg-white bg-opacity-10 rounded-pill px-3 py-1 text-white border border-white border-opacity-25" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i> <?= $_SESSION['nama_user'] ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow mt-3 animated fadeIn">
                            <li><a class="dropdown-item py-2" href="<?= $_SESSION['role_user'] == 'admin' ? 'dashboard-admin.php' : 'dashboard-pelanggan.php' ?>"><i class="fas fa-tachometer-alt me-2 text-primary"></i>Dashboard</a></li>
                            <li><a class="dropdown-item py-2" href="pesanan.php"><i class="fas fa-shopping-bag me-2 text-primary"></i>Pesanan Saya</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item py-2 text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item mt-2 mt-lg-0">
                        <a href="login.php" class="btn btn-light btn-sm rounded-pill px-4 fw-bold text-primary shadow-sm">Login</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Mobile Bottom Nav -->
<div class="bottom-nav">
    <a href="index.php" class="bottom-nav-item active">
        <i class="fas fa-home"></i>
        <span>Beranda</span>
    </a>
    <a href="keranjang.php" class="bottom-nav-item">
        <i class="fas fa-shopping-cart"></i>
        <span>Keranjang</span>
    </a>
    <a href="pesanan.php" class="bottom-nav-item">
        <i class="fas fa-list-alt"></i>
        <span>Pesanan</span>
    </a>
    <a href="<?= isset($_SESSION['id_user']) ? ($_SESSION['role_user'] == 'admin' ? 'dashboard-admin.php' : 'dashboard-pelanggan.php') : 'login.php' ?>" class="bottom-nav-item">
        <i class="fas fa-user"></i>
        <span>Akun</span>
    </a>
</div>

<!-- HERO -->
<div class="hero-section text-center">
    <div class="container px-4">
        <h1 class="display-5 fw-bold mb-3">Pakaian Adat Bajawa NTT</h1>
        <p class="lead mb-4 opacity-90">Koleksi busana tradisional eksklusif langsung dari tangan pengrajin Bajawa.</p>
        <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
            <a href="#produk" class="btn btn-light btn-lg rounded-pill px-5 shadow-sm text-primary fw-bold">Belanja Sekarang</a>
            <a href="register.php" class="btn btn-outline-light btn-lg rounded-pill px-5 border-2 fw-bold">Daftar Akun</a>
        </div>
    </div>
</div>

<div class="container mb-5" id="produk">
    <div class="d-flex justify-content-between align-items-end mb-4 px-1">
        <div>
            <h4 class="fw-bold mb-0">Produk Unggulan</h4>
            <div style="width: 50px; height: 3px; background: var(--primary-color); border-radius: 5px; margin-top: 8px;"></div>
        </div>
        <a href="#" class="text-decoration-none small fw-bold">Lihat Semua <i class="fas fa-chevron-right ms-1"></i></a>
    </div>

    <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm border-0 px-4 mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> Produk berhasil ditambahkan!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-3 g-md-4">
        <?php
        $produk = mysqli_query($conn, "SELECT * FROM produk");
        while($row = mysqli_fetch_assoc($produk)){
            $path = "uploads/".$row['gambar_produk'];
        ?>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card product-card">
                <div class="position-relative overflow-hidden">
                    <?php if(file_exists($path) && $row['gambar_produk'] != ""): ?>
                        <img src="<?= $path ?>" class="card-img-top product-img" alt="<?= $row['nama_produk']; ?>">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/300x400?text=No+Image" class="card-img-top product-img" alt="No Image">
                    <?php endif; ?>
                    
                    <?php if($row['jumlah_produk'] <= 5 && $row['jumlah_produk'] > 0): ?>
                        <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-3 rounded-pill shadow-sm" style="font-size: 10px;">Stok Terbatas</span>
                    <?php endif; ?>
                </div>

                <div class="card-body d-flex flex-column p-3">
                    <h6 class="fw-bold mb-1 product-title text-dark"><?= $row['nama_produk']; ?></h6>
                    <h5 class="text-primary fw-bold mb-2 product-price">Rp <?= number_format($row['harga_produk'],0,',','.'); ?></h5>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3 mt-auto">
                        <small class="text-muted"><i class="fas fa-box-open me-1"></i> <?= $row['jumlah_produk']; ?> unit</small>
                        <div class="small text-warning"><i class="fas fa-star me-1"></i>4.9</div>
                    </div>

                    <?php if($row['jumlah_produk'] > 0){ ?>
                        <?php if(isset($_SESSION['id_user'])): ?>
                            <a href="keranjang-tambah.php?id=<?= $row['id_produk']; ?>" class="btn btn-primary w-100 rounded-pill py-2 shadow-sm border-0">
                                <i class="fas fa-plus-circle me-1"></i> <span class="btn-cart-text">Tambah</span>
                            </a>
                        <?php else: ?>
                            <button type="button" class="btn btn-primary w-100 rounded-pill py-2 shadow-sm border-0" data-bs-toggle="modal" data-bs-target="#loginRequiredModal">
                                <i class="fas fa-plus-circle me-1"></i> <span class="btn-cart-text">Tambah</span>
                            </button>
                        <?php endif; ?>
                    <?php } else { ?>
                        <button class="btn btn-secondary w-100 rounded-pill py-2 border-0" disabled>Habis</button>
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