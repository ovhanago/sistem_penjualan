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
    <title>Keranjang Belanja - Toko Adat</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .product-img { border-radius: 10px; object-fit: cover; }
        .navbar { box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<!-- NAVBAR (Sama dengan PC) -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="fas fa-store me-2"></i>Toko Pakaian Adat
        </a>
        
        <button class="navbar-toggler shadow-none border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a href="index.php" class="nav-link text-white me-lg-3"><i class="fas fa-arrow-left me-1"></i> Kembali Belanja</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5 mb-5">
    <?php if($message != ""): ?>
        <div class="alert alert-<?= $messageType ?> alert-dismissible fade show rounded-pill px-4 shadow-sm border-0 mb-4" role="alert">
            <i class="fas fa-info-circle me-2"></i> <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0 fw-bold text-primary">Isi Keranjang Belanja</h5>
                </div>
                <div class="card-body p-0">
                    <?php if(empty($_SESSION['keranjang'])): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-4x text-light mb-3"></i>
                            <h5 class="text-muted">Keranjang Anda Kosong</h5>
                            <a href="index.php" class="btn btn-primary mt-3 rounded-pill px-4">Belanja Sekarang</a>
                        </div>
                    <?php else: ?>
                        <form method="POST" id="form-keranjang">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light text-muted small text-uppercase">
                                        <tr>
                                            <th class="ps-4">Produk</th>
                                            <th>Harga</th>
                                            <th width="100">Jumlah</th>
                                            <th>Subtotal</th>
                                            <th class="text-center pe-4">Hapus</th>
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
                                                        <h6 class="mb-0 fw-bold small"><?= $row['nama_produk'] ?></h6>
                                                        <small class="text-muted" style="font-size: 10px;">Stok: <?= $row['jumlah_produk'] ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="small">Rp <?= number_format($row['harga_produk'], 0, ',', '.') ?></td>
                                            <td>
                                                <input type="number" name="jumlah[<?= $id_produk ?>]" value="<?= $jumlah ?>" 
                                                       class="form-control form-control-sm text-center border-0 bg-light rounded-pill input-jumlah" 
                                                       min="1" max="<?= $row['jumlah_produk'] ?>" data-id="<?= $id_produk ?>" data-harga="<?= $row['harga_produk'] ?>">
                                            </td>
                                            <td class="fw-bold text-primary small">Rp <span class="subtotal-item" id="subtotal-<?= $id_produk ?>"><?= number_format($subtotal, 0, ',', '.') ?></span></td>
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
                            <div class="p-3 bg-light d-flex justify-content-between">
                                <button type="submit" name="update" class="btn btn-outline-primary btn-sm rounded-pill px-4">
                                    <i class="fas fa-sync-alt me-1"></i> Update
                                </button>
                                <div class="text-end">
                                    <small class="text-muted d-block" style="font-size: 11px;">Subtotal Sementara</small>
                                    <h5 class="fw-bold text-primary mb-0">Rp <span id="total-keranjang"><?= number_format($total, 0, ',', '.') ?></span></h5>
                                </div>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Ringkasan Belanja</h5>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="h5 fw-bold mb-0">Total Tagihan</span>
                        <span class="h5 fw-bold text-primary mb-0">Rp <span id="total-ringkasan"><?= isset($total) ? number_format($total, 0, ',', '.') : 0 ?></span></span>
                    </div>
                    
                    <?php if(!empty($_SESSION['keranjang'])): ?>
                        <?php if(isset($_SESSION['id_user'])): ?>
                            <button type="submit" name="checkout" form="form-keranjang" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm">
                                Lanjut Pembayaran <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm">
                                Login untuk Bayar <i class="fas fa-sign-in-alt ms-2"></i>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('.input-jumlah').forEach(input => {
        input.addEventListener('input', function() {
            const id = this.getAttribute('data-id');
            const harga = parseFloat(this.getAttribute('data-harga'));
            const max = parseInt(this.getAttribute('max'));
            let jumlah = parseInt(this.value);
            
            if (isNaN(jumlah) || jumlah < 1) return;
            if (jumlah > max) {
                alert('Maksimal stok: ' + max);
                this.value = max;
                jumlah = max;
            }

            const subtotal = harga * jumlah;
            document.getElementById('subtotal-' + id).innerText = new Intl.NumberFormat('id-ID').format(subtotal);

            let total = 0;
            document.querySelectorAll('.input-jumlah').forEach(inp => {
                total += parseFloat(inp.getAttribute('data-harga')) * (parseInt(inp.value) || 0);
            });

            const formatted = new Intl.NumberFormat('id-ID').format(total);
            document.getElementById('total-keranjang').innerText = formatted;
            document.getElementById('total-ringkasan').innerText = formatted;
        });
    });
</script>
</body>
</html>