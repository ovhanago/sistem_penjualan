<?php
session_start();
require_once "config/koneksi.php";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Pakaian Adat Bajawa - Eksklusif & Berkualitas</title>
    
    <!-- Google Fonts: Inter & Playfair Display untuk kesan mewah -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root {
            --primary-dark: #1a1a1a;
            --accent-color: #0d6efd;
            --text-muted: #6c757d;
            --card-shadow: 0 10px 30px rgba(0,0,0,0.05);
            --transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        body { 
            background-color: #fcfcfc; 
            font-family: 'Inter', sans-serif; 
            color: var(--primary-dark);
            overflow-x: hidden;
        }

        /* Modern Navbar */
        .navbar { 
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 15px 0;
            transition: var(--transition);
        }
        .navbar-brand { 
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            letter-spacing: -0.5px;
            color: var(--primary-dark) !important;
        }
        .nav-link { 
            font-weight: 500; 
            color: var(--primary-dark) !important;
            opacity: 0.8;
            transition: var(--transition);
        }
        .nav-link:hover { opacity: 1; color: var(--accent-color) !important; }

        /* Premium Hero Section */
        .hero-section { 
            background: linear-gradient(135deg, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.3) 100%), 
                        url('https://sultansinindonesieblog.wordpress.com/wp-content/uploads/2021/12/1-ng.jpg');
            background-size: cover;
            background-position: center;
            height: 80vh;
            min-height: 500px;
            display: flex;
            align-items: center;
            color: white;
            border-radius: 0 0 40px 40px;
            margin-bottom: 60px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: 4rem;
            line-height: 1.1;
            margin-bottom: 20px;
        }

        /* Enhanced Search Box */
        .search-container {
            max-width: 600px;
            margin: 0 auto;
            position: relative;
        }
        .search-input {
            border-radius: 100px;
            padding: 15px 30px;
            border: 1px solid rgba(0,0,0,0.1);
            box-shadow: 0 5px 15px rgba(0,0,0,0.02);
            font-size: 0.95rem;
            transition: var(--transition);
        }
        .search-input:focus {
            box-shadow: 0 10px 25px rgba(13, 110, 253, 0.1);
            border-color: var(--accent-color);
        }

        /* Elegant Product Card */
        .product-card { 
            background: #fff;
            border: none;
            border-radius: 24px;
            overflow: hidden;
            transition: var(--transition);
            height: 100%;
            display: flex;
            flex-direction: column;
            border: 1px solid rgba(0,0,0,0.03);
        }
        .product-card:hover { 
            transform: translateY(-12px);
            box-shadow: 0 30px 60px rgba(0,0,0,0.1);
        }
        .image-wrapper {
            position: relative;
            padding-top: 125%; /* 4:5 Aspect Ratio */
            overflow: hidden;
        }
        .product-img { 
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
        }
        .product-card:hover .product-img { transform: scale(1.1); }
        
        .card-body { padding: 25px; }
        .product-title { 
            font-weight: 600; 
            font-size: 1.1rem; 
            margin-bottom: 8px;
            color: var(--primary-dark);
            height: 2.6em;
            overflow: hidden;
        }
        .product-price { 
            font-weight: 700; 
            color: var(--accent-color);
            font-size: 1.25rem;
            margin-bottom: 15px;
        }

        .btn-add-cart {
            border-radius: 100px;
            padding: 10px 20px;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: var(--transition);
        }

        /* Responsive Fixes */
        @media (max-width: 991px) {
            .hero-title { font-size: 2.8rem; }
            .search-container { width: 90%; }
        }
        @media (max-width: 768px) {
            .hero-section { height: 60vh; border-radius: 0 0 30px 30px; }
            .hero-title { font-size: 2rem; }
            .navbar-brand { font-size: 1.25rem; }
            .product-card { border-radius: 20px; }
            .card-body { padding: 15px; }
            .product-title { font-size: 0.95rem; }
            .product-price { font-size: 1.1rem; }
        }
    </style>
</head>
<body>

<!-- NAVIGATION -->
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <span class="fs-3">Bajawa</span><span class="text-primary ms-1">Adat</span>
        </a>

        <div class="d-flex align-items-center order-lg-3">
            <a href="keranjang.php" class="nav-link position-relative me-3">
                <i class="fas fa-shopping-bag fs-4"></i>
                <?php if(isset($_SESSION['keranjang']) && count($_SESSION['keranjang']) > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary" style="font-size: 10px;">
                        <?= count($_SESSION['keranjang']) ?>
                    </span>
                <?php endif; ?>
            </a>
            
            <button class="navbar-toggler border-0 shadow-none p-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fas fa-bars fs-4 text-dark"></i>
            </button>
        </div>

        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link px-lg-3" href="index.php">Beranda</a></li>
                <li class="nav-item"><a class="nav-link px-lg-3" href="#produk">Koleksi</a></li>
                <?php if(isset($_SESSION['id_user'])): ?>
                    <li class="nav-item dropdown d-lg-none mt-3">
                        <a class="nav-link fw-bold text-primary" href="dashboard-pelanggan.php">Dashboard (<?= $_SESSION['nama_user'] ?>)</a>
                        <a class="nav-link text-danger" href="logout.php">Keluar</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item d-lg-none mt-3">
                        <a class="btn btn-dark rounded-pill w-100" href="login.php">Masuk Akun</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="d-none d-lg-flex order-lg-4 ms-3">
            <?php if(isset($_SESSION['id_user'])): ?>
                <div class="dropdown">
                    <button class="btn btn-outline-dark rounded-pill px-4 dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="far fa-user me-2"></i> <?= explode(' ', $_SESSION['nama_user'])[0] ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-3 py-3 rounded-4">
                        <li><a class="dropdown-item py-2 px-4" href="dashboard-pelanggan.php"><i class="fas fa-user-circle me-2 text-primary"></i>Profil Saya</a></li>
                        <li><a class="dropdown-item py-2 px-4" href="pesanan.php"><i class="fas fa-receipt me-2 text-primary"></i>Pesanan</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item py-2 px-4 text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Keluar</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <a href="login.php" class="btn btn-dark rounded-pill px-4 fw-bold">Masuk</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="hero-section text-center">
    <div class="container px-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <span class="text-uppercase tracking-widest fw-bold small opacity-75 mb-3 d-block">The Heritage of NTT</span>
                <h1 class="hero-title">Koleksi Pakaian Adat Bajawa Pilihan</h1>
                <p class="lead mb-5 mx-auto opacity-90" style="max-width: 600px;">Rasakan kemewahan tenun asli dan detail ornamen tradisional yang dikerjakan sepenuh hati oleh pengrajin lokal.</p>
                <div class="search-container">
                    <form class="d-flex shadow-lg rounded-pill bg-white p-1">
                        <input class="form-control border-0 bg-transparent ps-4 search-input" type="search" placeholder="Cari busana impian Anda...">
                        <button class="btn btn-primary rounded-pill px-4 py-2" type="submit">
                            <i class="fas fa-search me-2"></i> <span class="d-none d-sm-inline">Cari</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- PRODUK -->
<div class="container mb-5" id="produk">
    <div class="text-center mb-5">
        <h2 class="display-6 fw-bold mb-3" style="font-family: 'Playfair Display', serif;">Produk Terbaru</h2>
        <div style="width: 60px; height: 3px; background: var(--accent-color); margin: 0 auto;"></div>
    </div>

    <div class="row g-4 g-md-5">
        <?php
        $produk = mysqli_query($conn, "SELECT * FROM produk");
        while($row = mysqli_fetch_assoc($produk)){
            $path = "uploads/".$row['gambar_produk'];
        ?>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="product-card">
                <div class="image-wrapper">
                    <?php if(file_exists($path) && $row['gambar_produk'] != ""): ?>
                        <img src="<?= $path ?>" class="product-img" alt="<?= $row['nama_produk'] ?>">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/400x500?text=Premium+Collection" class="product-img" alt="No Image">
                    <?php endif; ?>
                </div>

                <div class="card-body">
                    <h6 class="product-title"><?= $row['nama_produk']; ?></h6>
                    <div class="product-price">Rp <?= number_format($row['harga_produk'],0,',','.'); ?></div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="badge bg-light text-dark rounded-pill px-3 py-2 fw-normal" style="font-size: 0.75rem;">
                            <i class="fas fa-cube me-1 text-primary"></i> <?= $row['jumlah_produk']; ?> Tersedia
                        </span>
                        <div class="small text-warning"><i class="fas fa-star me-1"></i>4.9</div>
                    </div>

                    <?php if($row['jumlah_produk'] > 0){ ?>
                        <a href="keranjang-tambah.php?id=<?= $row['id_produk']; ?>" class="btn btn-outline-dark btn-add-cart w-100">
                            Tambah Ke Tas
                        </a>
                    <?php } else { ?>
                        <button class="btn btn-secondary btn-add-cart w-100" disabled>Habis Terjual</button>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

<footer class="bg-white py-5 mt-5 border-top">
    <div class="container text-center">
        <div class="navbar-brand fs-2 mb-4 d-block">Bajawa<span class="text-primary">Adat</span></div>
        <p class="text-muted mb-4 mx-auto" style="max-width: 500px;">Menjaga tradisi melalui kualitas busana terbaik. Setiap benang menceritakan kisah budaya yang tak lekang oleh waktu.</p>
        <div class="d-flex justify-content-center gap-4 mb-5">
            <a href="#" class="text-dark opacity-50"><i class="fab fa-instagram fa-lg"></i></a>
            <a href="#" class="text-dark opacity-50"><i class="fab fa-facebook fa-lg"></i></a>
            <a href="#" class="text-dark opacity-50"><i class="fab fa-whatsapp fa-lg"></i></a>
        </div>
        <p class="text-muted small">&copy; <?= date("Y") ?> Bajawa Adat Premium Store. Dibuat dengan bangga untuk Indonesia.</p>
    </div>
</footer>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>