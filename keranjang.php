<?php
session_start();
require_once "config/koneksi.php";

$message = "";
$messageType = "";

// Fungsi Hapus Produk dari Keranjang
if(isset($_GET['aksi']) && $_GET['aksi'] == 'hapus' && isset($_GET['id'])){
    $id_produk = $_GET['id'];
    unset($_SESSION['keranjang'][$id_produk]);
    $message = "Produk telah dihapus dari keranjang!";
    $messageType = "success";
}

// Fungsi Update Jumlah Produk di Keranjang
if(isset($_POST['update']) || isset($_POST['checkout'])){
    if(!empty($_POST['jumlah'])){
        foreach($_POST['jumlah'] as $id => $jumlah){
            $id_clean = mysqli_real_escape_string($conn, $id);
            $q_val = mysqli_query($conn, "SELECT jumlah_produk FROM produk WHERE id_produk = '$id_clean'");
            $d_val = mysqli_fetch_assoc($q_val);
            $stok_max = $d_val['jumlah_produk'];

            if($jumlah <= 0){
                unset($_SESSION['keranjang'][$id]);
            } else {
                $_SESSION['keranjang'][$id] = ($jumlah > $stok_max) ? $stok_max : $jumlah;
            }
        }
    }
    
    if(isset($_POST['checkout'])){
        header("Location: checkout.php");
        exit;
    }

    $message = "Keranjang telah diperbarui!";
    $messageType = "info";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - Toko Adat Bajawa</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #0d6efd;
            --soft-bg: #f0f2f5;
        }
        body { background-color: var(--soft-bg); font-family: 'Segoe UI', sans-serif; padding-bottom: 90px; }
        .card { border: none; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .product-img { border-radius: 15px; object-fit: cover; }
        .btn-checkout { border-radius: 15px; padding: 12px 30px; font-weight: 600; }
        
        /* Bottom Navigation for Mobile */
        .bottom-nav { position: fixed; bottom: 0; left: 0; right: 0; background: #fff; display: none; justify-content: space-around; padding: 12px 0; box-shadow: 0 -5px 15px rgba(0,0,0,0.05); z-index: 1030; border-top-left-radius: 25px; border-top-right-radius: 25px; }
        .bottom-nav-item { text-align: center; color: #6c757d; text-decoration: none; font-size: 11px; transition: 0.3s; }
        .bottom-nav-item i { font-size: 20px; display: block; margin-bottom: 4px; }
        .bottom-nav-item.active { color: var(--primary-color); font-weight: bold; }

        .cart-item-mobile { background: #fff; border-radius: 20px; padding: 15px; margin-bottom: 15px; position: relative; }
        .input-qty-mobile { max-width: 80px; border-radius: 50px; text-align: center; border: 1px solid #eee; background: #f8f9fa; }

        @media (max-width: 768px) {
            .bottom-nav { display: flex; }
            .navbar { display: none; }
            .desktop-cart { display: none; }
            .mobile-cart { display: block; }
        }
        @media (min-width: 769px) {
            .mobile-cart { display: none; }
        }
    </style>
</head>
<body>

<!-- Mobile Bottom Nav -->
<div class="bottom-nav">
    <a href="index.php" class="bottom-nav-item">
        <i class="fas fa-home"></i>
        <span>Beranda</span>
    </a>
    <a href="keranjang.php" class="bottom-nav-item active">
        <i class="fas fa-shopping-cart"></i>
        <span>Keranjang</span>
    </a>
    <a href="dashboard-pelanggan.php" class="bottom-nav-item">
        <i class="fas fa-list-alt"></i>
        <span>Pesanan</span>
    </a>
    <a href="<?= isset($_SESSION['id_user']) ? 'dashboard-pelanggan.php' : 'login.php' ?>" class="bottom-nav-item">
        <i class="fas fa-user"></i>
        <span>Akun</span>
    </a>
</div>

<!-- NAVBAR (Desktop) -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top d-none d-lg-block">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="fas fa-store-alt me-2"></i>Toko Adat Bajawa
        </a>
        <div class="ms-auto">
            <a href="index.php" class="btn btn-light btn-sm rounded-pill px-3 fw-bold text-primary shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali Belanja
            </a>
        </div>
    </div>
</nav>

<div class="container mt-lg-5 mt-3">
    <?php if($message != ""): ?>
        <div class="alert alert-<?= $messageType ?> alert-dismissible fade show rounded-4 shadow-sm border-0 mb-4 px-4" role="alert">
            <i class="fas fa-info-circle me-2"></i> <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-lg-none mb-3 d-flex justify-content-between align-items-center">
        <h4 class="fw-bold mb-0">Keranjang Saya</h4>
        <a href="index.php" class="text-primary text-decoration-none fw-bold small">Tambah Produk</a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4 overflow-hidden">
                <div class="card-body p-0">
                    <?php if(empty($_SESSION['keranjang'])): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-basket fa-4x text-light mb-3"></i>
                            <h5 class="text-muted fw-bold">Keranjang Kosong</h5>
                            <p class="text-muted small">Yuk, cari pakaian adat Bajawa impianmu sekarang!</p>
                            <a href="index.php" class="btn btn-primary mt-2 px-4 rounded-pill shadow-sm">Mulai Belanja</a>
                        </div>
                    <?php else: ?>
                        <form method="POST" id="form-keranjang">
                            <!-- Desktop Version -->
                            <div class="table-responsive desktop-cart">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light text-muted small text-uppercase">
                                        <tr>
                                            <th class="ps-4">Produk</th>
                                            <th>Harga</th>
                                            <th width="120">Jumlah</th>
                                            <th>Subtotal</th>
                                            <th class="text-center pe-4">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $total = 0;
                                        foreach($_SESSION['keranjang'] as $id_produk => $jumlah): 
                                            $query = mysqli_query($conn, "SELECT * FROM produk WHERE id_produk = '$id_produk'");
                                            $row = mysqli_fetch_assoc($query);
                                            $subtotal = $row['harga_produk'] * $jumlah;
                                            $total += $subtotal;
                                        ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center py-2">
                                                    <img src="uploads/<?= $row['gambar_produk'] ?>" width="60" height="60" class="product-img me-3 shadow-sm">
                                                    <div>
                                                        <h6 class="mb-0 fw-bold"><?= $row['nama_produk'] ?></h6>
                                                        <small class="text-muted x-small">Stok: <?= $row['jumlah_produk'] ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>Rp <?= number_format($row['harga_produk'], 0, ',', '.') ?></td>
                                            <td>
                                                <input type="number" name="jumlah[<?= $id_produk ?>]" value="<?= $jumlah ?>" 
                                                       class="form-control form-control-sm text-center border-0 bg-light rounded-pill input-jumlah" 
                                                       min="1" max="<?= $row['jumlah_produk'] ?>" data-id="<?= $id_produk ?>" data-harga="<?= $row['harga_produk'] ?>">
                                            </td>
                                            <td class="fw-bold text-primary">Rp <span class="subtotal-val" id="subtotal-<?= $id_produk ?>"><?= number_format($subtotal, 0, ',', '.') ?></span></td>
                                            <td class="text-center pe-4">
                                                <a href="keranjang.php?aksi=hapus&id=<?= $id_produk ?>" class="btn btn-light btn-sm rounded-circle text-danger shadow-sm border-0" onclick="return confirm('Hapus produk ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Mobile Version -->
                            <div class="mobile-cart p-3 bg-light">
                                <?php 
                                foreach($_SESSION['keranjang'] as $id_produk => $jumlah): 
                                    $query = mysqli_query($conn, "SELECT * FROM produk WHERE id_produk = '$id_produk'");
                                    $row = mysqli_fetch_assoc($query);
                                ?>
                                <div class="cart-item-mobile shadow-sm">
                                    <div class="d-flex">
                                        <img src="uploads/<?= $row['gambar_produk'] ?>" width="70" height="70" class="product-img me-3">
                                        <div class="flex-grow-1">
                                            <h6 class="fw-bold mb-1 small"><?= $row['nama_produk'] ?></h6>
                                            <div class="text-primary fw-bold mb-2 small">Rp <?= number_format($row['harga_produk'], 0, ',', '.') ?></div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <input type="number" name="jumlah[<?= $id_produk ?>]" value="<?= $jumlah ?>" 
                                                       class="form-control form-control-sm input-qty-mobile input-jumlah-mob" 
                                                       min="1" max="<?= $row['jumlah_produk'] ?>" data-id="<?= $id_produk ?>" data-harga="<?= $row['harga_produk'] ?>">
                                                <a href="keranjang.php?aksi=hapus&id=<?= $id_produk ?>" class="text-danger small text-decoration-none fw-bold">
                                                    <i class="fas fa-trash-alt me-1"></i> Hapus
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="p-3 bg-white d-flex justify-content-between border-top">
                                <button type="submit" name="update" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-bold">
                                    <i class="fas fa-sync-alt me-1"></i> Update
                                </button>
                                <div class="text-end">
                                    <small class="text-muted d-block x-small">Total</small>
                                    <h5 class="fw-bold text-primary mb-0">Rp <span id="total-keranjang"><?= number_format($total, 0, ',', '.') ?></span></h5>
                                </div>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 bg-white sticky-lg-top" style="top: 100px; border-radius: 25px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 text-dark">Ringkasan</h5>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted small">Total Pembayaran</span>
                        <span class="h5 fw-bold text-primary mb-0">Rp <span id="total-ringkasan"><?= isset($total) ? number_format($total, 0, ',', '.') : 0 ?></span></span>
                    </div>
                    <hr class="opacity-10">
                    
                    <?php if(!empty($_SESSION['keranjang'])): ?>
                        <?php if(isset($_SESSION['id_user'])): ?>
                            <button type="submit" name="checkout" form="form-keranjang" class="btn btn-primary w-100 btn-checkout mb-3 shadow-sm py-3">
                                Checkout <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-primary w-100 btn-checkout mb-3 shadow-sm py-3">
                                Login untuk Bayar <i class="fas fa-sign-in-alt ms-2"></i>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <div class="text-center opacity-50 mt-2">
                        <i class="fas fa-lock me-1"></i> <span style="font-size: 11px;">Keamanan Data Terjamin</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
<script>
    const updateTotals = () => {
        let total = 0;
        document.querySelectorAll('.input-jumlah, .input-jumlah-mob').forEach(inp => {
            const hrg = parseFloat(inp.getAttribute('data-harga'));
            const jml = parseInt(inp.value) || 0;
            total += hrg * jml;
            
            // Sync mobile and desktop inputs for same ID
            const id = inp.getAttribute('data-id');
            const otherInps = document.querySelectorAll(`input[data-id="${id}"]`);
            otherInps.forEach(other => { if(other !== inp) other.value = inp.value; });
            
            // Update subtotal display if exists
            const subElem = document.getElementById('subtotal-' + id);
            if(subElem) subElem.innerText = new Intl.NumberFormat('id-ID').format(hrg * jml);
        });
        const formatted = new Intl.NumberFormat('id-ID').format(total);
        document.getElementById('total-keranjang').innerText = formatted;
        document.getElementById('total-ringkasan').innerText = formatted;
    };

    document.querySelectorAll('.input-jumlah, .input-jumlah-mob').forEach(input => {
        input.addEventListener('input', updateTotals);
    });
</script>
</body>
</html>