<?php
session_start();
require_once "config/koneksi.php";

$message = "";
$messageType = "";

if(isset($_GET['aksi']) && $_GET['aksi'] == 'hapus' && isset($_GET['id'])){
    $id_produk = $_GET['id'];
    unset($_SESSION['keranjang'][$id_produk]);
    $message = "Produk dihapus!";
    $messageType = "success";
}

if(isset($_POST['update']) || isset($_POST['checkout'])){
    if(!empty($_POST['jumlah'])){
        foreach($_POST['jumlah'] as $id => $jumlah){
            $id_clean = mysqli_real_escape_string($conn, $id);
            $q_val = mysqli_query($conn, "SELECT jumlah_produk FROM produk WHERE id_produk = '$id_clean'");
            $d_val = mysqli_fetch_assoc($q_val);
            $stk = $d_val['jumlah_produk'];
            $_SESSION['keranjang'][$id] = ($jumlah > $stk) ? $stk : (($jumlah < 1) ? 1 : $jumlah);
        }
    }
    if(isset($_POST['checkout'])){
        header("Location: checkout.php");
        exit;
    }
    $message = "Keranjang diperbarui!";
    $messageType = "info";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang - Toko Adat</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
        .navbar { background: #fff !important; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .card-custom { border: none; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); background: #fff; overflow: hidden; }
        .product-img { border-radius: 12px; object-fit: cover; }
        .btn-checkout { background: linear-gradient(135deg, #0d6efd, #0099ff); border: none; color: #fff; border-radius: 12px; padding: 12px; font-weight: 600; }
        .btn-checkout:hover { opacity: 0.9; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(13, 110, 253, 0.2); }
        .table thead th { background: #f8f9fa; border: none; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; padding: 15px; color: #6c757d; }
        .table td { padding: 15px; vertical-align: middle; border-bottom: 1px solid #f8f9fa; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light sticky-top py-3">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="index.php"><i class="fas fa-store me-2"></i>TOKO ADAT</a>
        <a href="index.php" class="btn btn-light btn-sm rounded-pill px-4"><i class="fas fa-arrow-left me-1"></i> Belanja</a>
    </div>
</nav>

<div class="container py-4 py-lg-5">
    <?php if($message != ""): ?>
        <div class="alert alert-<?= $messageType ?> alert-dismissible fade show rounded-pill px-4 border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-info-circle me-2"></i> <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0 fw-bold">Keranjang Belanja</h5>
                </div>
                <div class="table-responsive">
                    <?php if(empty($_SESSION['keranjang'])): ?>
                        <div class="text-center py-5">
                            <img src="https://illustrations.popsy.co/blue/shopping-cart.svg" width="150" class="mb-4 opacity-50">
                            <h5 class="text-muted">Keranjang masih kosong nih.</h5>
                            <a href="index.php" class="btn btn-primary rounded-pill px-4 mt-2">Mulai Belanja</a>
                        </div>
                    <?php else: ?>
                        <form method="POST" id="form-cart">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Produk</th>
                                        <th>Harga</th>
                                        <th width="100">Jumlah</th>
                                        <th>Subtotal</th>
                                        <th class="pe-4 text-center">Hapus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $total = 0;
                                    foreach($_SESSION['keranjang'] as $id => $qty): 
                                        $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM produk WHERE id_produk = '$id'"));
                                        $sub = $row['harga_produk'] * $qty;
                                        $total += $sub;
                                    ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center py-1">
                                                <img src="uploads/<?= $row['gambar_produk'] ?>" width="60" height="60" class="product-img me-3 shadow-sm">
                                                <div>
                                                    <div class="fw-bold text-dark text-truncate" style="max-width: 150px;"><?= $row['nama_produk'] ?></div>
                                                    <small class="text-muted">ID: #<?= $id ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="small">Rp<?= number_format($row['harga_produk'], 0, ',', '.') ?></td>
                                        <td>
                                            <input type="number" name="jumlah[<?= $id ?>]" value="<?= $qty ?>" class="form-control form-control-sm text-center rounded-pill border-0 bg-light" min="1" max="<?= $row['jumlah_produk'] ?>">
                                        </td>
                                        <td class="fw-bold text-primary">Rp<?= number_format($sub, 0, ',', '.') ?></td>
                                        <td class="pe-4 text-center">
                                            <a href="keranjang.php?aksi=hapus&id=<?= $id ?>" class="text-danger opacity-50"><i class="fas fa-trash-alt"></i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <div class="p-3 border-top bg-light d-flex justify-content-between">
                                <button type="submit" name="update" class="btn btn-white btn-sm rounded-pill border px-4 shadow-sm">Update</button>
                                <div class="text-end">
                                    <small class="text-muted d-block">Subtotal</small>
                                    <h5 class="fw-bold text-primary mb-0">Rp<?= number_format($total, 0, ',', '.') ?></h5>
                                </div>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card-custom p-4">
                <h6 class="fw-bold mb-4 text-uppercase letter-spacing-1">Ringkasan Pesanan</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Item</span>
                    <span class="fw-bold"><?= !empty($_SESSION['keranjang']) ? count($_SESSION['keranjang']) : 0 ?></span>
                </div>
                <hr class="opacity-10">
                <div class="d-flex justify-content-between mb-4">
                    <span class="h5 fw-bold">Total</span>
                    <span class="h5 fw-bold text-primary">Rp<?= isset($total) ? number_format($total, 0, ',', '.') : 0 ?></span>
                </div>
                
                <?php if(!empty($_SESSION['keranjang'])): ?>
                    <button type="submit" name="checkout" form="form-cart" class="btn btn-checkout w-100 shadow-sm mb-3">
                        Lanjut Checkout <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                <?php else: ?>
                    <button class="btn btn-secondary w-100 rounded-pill py-2" disabled>Keranjang Kosong</button>
                <?php endif; ?>
                <div class="text-center">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/Logo_Midtrans.png" width="80" class="opacity-50">
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>