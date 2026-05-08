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
    foreach($_POST['jumlah'] as $id => $jumlah){
        $id_clean = mysqli_real_escape_string($conn, $id);
        $q_val = mysqli_query($conn, "SELECT jumlah_produk FROM produk WHERE id_produk = '$id_clean'");
        $d_val = mysqli_fetch_assoc($q_val);
        $stok_max = $d_val['jumlah_produk'];

        if($jumlah <= 0){
            unset($_SESSION['keranjang'][$id]);
        } else {
            // Jika input melebihi stok, paksa ke stok maksimal
            $_SESSION['keranjang'][$id] = ($jumlah > $stok_max) ? $stok_max : $jumlah;
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
<html>
<head>
    <title>Keranjang Belanja</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card { border: none; border-radius: 15px; }
        .table thead { background-color: #f1f3f5; }
        .product-img { border-radius: 10px; object-fit: cover; }
        .btn-checkout { border-radius: 10px; padding: 12px 30px; font-weight: 600; border: none; }
        .navbar { box-shadow: 0 2px 4px rgba(0,0,0,0.08); }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="fas fa-store me-2"></i>Toko Pakaian Adat
        </a>
        <div class="ms-auto">
            <a href="index.php" class="btn btn-light btn-sm rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Kembali Belanja
            </a>
        </div>
    </div>
</nav>

<div class="container mt-5 mb-5">
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-pill px-4 shadow-sm mb-4 border-0" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?= $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-pill px-4 shadow-sm mb-4 border-0" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> <?= $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 border-0">
                    <h4 class="mb-0 fw-bold text-primary">Keranjang Belanja</h4>
                </div>
                <div class="card-body p-0">
                    
                    <?php if($message != ""): ?>
                    <div class="p-3">
                        <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
                            <i class="fas fa-info-circle me-2"></i> <?= $message ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if(empty($_SESSION['keranjang'])): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-4x text-light mb-3"></i>
                            <h5 class="text-muted">Keranjang Anda masih kosong</h5>
                            <a href="index.php" class="btn btn-primary mt-3 px-4 rounded-pill">Belanja Sekarang</a>
                        </div>
                    <?php else: ?>
                        <form method="POST" id="form-keranjang">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="text-muted small text-uppercase">
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
                                                    <img src="uploads/<?= $row['gambar_produk'] ?>" width="70" height="70" class="product-img me-3 shadow-sm">
                                                    <div>
                                                        <h6 class="mb-0 fw-bold"><?= $row['nama_produk'] ?></h6>
                                                        <small class="text-muted">ID: #<?= $row['id_produk'] ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>Rp <?= number_format($row['harga_produk'], 0, ',', '.') ?></td>
                                            <td>
                                                <input type="number" name="jumlah[<?= $id_produk ?>]" value="<?= $jumlah ?>" 
                                                       class="form-control form-control-sm text-center border-0 bg-light rounded-pill input-jumlah" 
                                                       min="1" max="<?= $row['jumlah_produk'] ?>" data-id="<?= $id_produk ?>" data-harga="<?= $row['harga_produk'] ?>">
                                            </td>
                                            <td class="fw-bold text-primary">
                                                Rp <span class="subtotal-item" id="subtotal-<?= $id_produk ?>"><?= number_format($subtotal, 0, ',', '.') ?></span>
                                            </td>
                                            <td class="text-center pe-4">
                                                <button type="button" class="btn btn-outline-danger btn-sm border-0" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $id_produk ?>">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Modal Konfirmasi Hapus -->
                                        <div class="modal fade" id="deleteModal<?= $id_produk ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow">
                                                    <div class="modal-body text-center p-4">
                                                        <i class="fas fa-exclamation-circle fa-3x text-warning mb-3"></i>
                                                        <h5 class="fw-bold">Hapus Produk?</h5>
                                                        <p class="text-muted">Apakah Anda yakin ingin menghapus <b><?= $row['nama_produk'] ?></b> dari keranjang?</p>
                                                        <div class="d-flex justify-content-center gap-2 mt-4">
                                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                                            <a href="keranjang.php?aksi=hapus&id=<?= $id_produk ?>" class="btn btn-danger rounded-pill px-4">Ya, Hapus</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-4 bg-light d-flex justify-content-between">
                                <button type="submit" name="update" class="btn btn-outline-primary rounded-pill px-4">
                                    <i class="fas fa-sync-alt me-2"></i> Update Keranjang
                                </button>
                                <div class="text-end">
                                    <small class="text-muted d-block">Subtotal</small>
                                    <h4 class="fw-bold text-primary mb-0">Rp <span id="total-keranjang"><?= number_format($total, 0, ',', '.') ?></span></h4>
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
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Produk</span>
                        <span class="fw-bold"><span id="total-qty"><?= !empty($_SESSION['keranjang']) ? count($_SESSION['keranjang']) : 0 ?></span></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="h5 fw-bold">Total</span>
                        <span class="h5 fw-bold text-primary">Rp <span id="total-ringkasan"><?= isset($total) ? number_format($total, 0, ',', '.') : 0 ?></span></span>
                    </div>
                    
                    <?php if(!empty($_SESSION['keranjang'])): ?>
                        <?php if(isset($_SESSION['id_user'])): ?>
                            <button type="submit" name="checkout" form="form-keranjang" class="btn btn-primary w-100 btn-checkout mb-3 shadow-sm">
                                Lanjut ke Pembayaran <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        <?php else: ?>
                            <div class="alert alert-info border-0 shadow-sm small mb-3">
                                <i class="fas fa-info-circle me-2"></i> Silakan login untuk melakukan pembayaran.
                            </div>
                            <a href="login.php" class="btn btn-primary w-100 btn-checkout mb-3">
                                Login Sekarang <i class="fas fa-sign-in-alt ms-2"></i>
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <button class="btn btn-secondary w-100 btn-checkout" disabled>
                            Checkout Kosong
                        </button>
                    <?php endif; ?>
                    
                    <div class="text-center">
                        <small class="text-muted">Pembayaran Aman & Terpercaya</small>
                        <div class="mt-2 text-primary opacity-50">
                            <i class="fab fa-cc-visa mx-1 fa-lg"></i>
                            <i class="fab fa-cc-mastercard mx-1 fa-lg"></i>
                            <i class="fas fa-university mx-1 fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PESANAN SAYA SECTION (Ditambahkan di bawah keranjang) -->
    <?php if(isset($_SESSION['id_user'])): ?>
    <div class="row mt-5">
        <div class="col-12">
            <div class="card shadow-sm overflow-hidden border-0">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-receipt me-2"></i>Pesanan Saya</h5>
                    <a href="dashboard-pelanggan.php" class="btn btn-outline-primary btn-sm rounded-pill px-3">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-4">No. Pesanan</th>
                                    <th>Tanggal</th>
                                    <th>Total Tagihan</th>
                                    <th>Status Pesanan</th>
                                    <th class="pe-4 text-center">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $id_user = $_SESSION['id_user'];
                                $q_pesanan = mysqli_query($conn, "SELECT * FROM pesanan WHERE id_user = '$id_user' ORDER BY id_pesanan DESC LIMIT 5");
                                if(mysqli_num_rows($q_pesanan) > 0):
                                    while($r = mysqli_fetch_assoc($q_pesanan)):
                                        $badge = 'bg-warning text-dark';
                                        $status = $r['status_pesanan'];
                                        if($status == 'Dikirim') $badge = 'bg-info text-white';
                                        if($status == 'Selesai') $badge = 'bg-success text-white';
                                        if($status == 'Dibatalkan' || $status == 'Batal') $badge = 'bg-danger text-white';
                                ?>
                                <tr>
                                    <td class="ps-4 fw-bold">#ORD-<?= $r['id_pesanan'] ?></td>
                                    <td><?= date('d M Y', strtotime($r['tanggal_pesanan'])) ?></td>
                                    <td class="fw-bold text-primary">Rp <?= number_format($r['total_harga'], 0, ',', '.') ?></td>
                                    <td><span class="badge rounded-pill <?= $badge ?> px-3 py-2" style="font-size: 11px;"><?= $r['status_pesanan'] ?></span></td>
                                    <td class="pe-4 text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="pesanan-detail.php?id=<?= $r['id_pesanan'] ?>" class="btn btn-light btn-sm rounded-circle text-primary shadow-sm" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if($status == 'Pending' || $status == 'Menunggu Pembayaran' || $status == 'Dikemas'): ?>
                                            <a href="batal-pesanan.php?id=<?= $r['id_pesanan'] ?>" class="btn btn-light btn-sm rounded-circle text-danger shadow-sm" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" title="Batalkan Pesanan" onclick="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                                                <i class="fas fa-times"></i>
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php 
                                    endwhile; 
                                else: 
                                ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fas fa-history fa-2x mb-2 opacity-25"></i><br>
                                        Belum ada riwayat pesanan.
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
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

            // Jika input melebihi stok, paksa ke nilai maksimal
            if (jumlah > max) {
                alert('Jumlah melebihi stok yang tersedia (Maks: ' + max + ')');
                this.value = max;
                jumlah = max;
            }

            // Hitung Subtotal Item
            const subtotal = harga * jumlah;
            document.getElementById('subtotal-' + id).innerText = new Intl.NumberFormat('id-ID').format(subtotal);

            // Hitung Total Keseluruhan & Total Qty
            let total = 0;
            let totalQty = 0;
            document.querySelectorAll('.input-jumlah').forEach(inp => {
                const hrg = parseFloat(inp.getAttribute('data-harga'));
                const jml = parseInt(inp.value) || 0;
                total += hrg * jml;
                totalQty += jml;
            });

            const formattedTotal = new Intl.NumberFormat('id-ID').format(total);
            document.getElementById('total-keranjang').innerText = formattedTotal;
            document.getElementById('total-ringkasan').innerText = formattedTotal;
            if(document.getElementById('total-qty')) {
                document.getElementById('total-qty').innerText = totalQty;
            }
        });
    });
</script>
</body>
</html>