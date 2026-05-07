<?php
require_once "config/koneksi.php";

// Cek session admin jika diperlukan (opsional, tergantung integrasi)
// session_start();
// if(!isset($_SESSION['id_user']) || $_SESSION['role_user'] != 'admin') { header("Location: login.php"); exit; }

$data = mysqli_query($conn, "SELECT * FROM produk ORDER BY id_produk DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Produk - Admin</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; padding: 20px; }
        .card-produk { border: none; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .table thead { background-color: #f8f9fa; }
        .table th { border: none; color: #6c757d; font-size: 13px; text-uppercase; letter-spacing: 1px; padding: 15px; }
        .table td { padding: 15px; vertical-align: middle; border-bottom: 1px solid #f1f1f1; }
        .img-produk { width: 50px; height: 50px; object-fit: cover; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .btn-action { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.2s; }
        .btn-action:hover { transform: scale(1.1); }
    </style>
</head>
<body>

<div class="container">
    <div class="card card-produk">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-box me-2"></i>Manajemen Produk</h5>
            <a href="tambah-produk.php" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm">
                <i class="fas fa-plus me-1"></i> Tambah Produk
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">No</th>
                            <th>Info Produk</th>
                            <th>Stok</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th class="text-center pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        if(mysqli_num_rows($data) > 0) :
                            while($row = mysqli_fetch_assoc($data)) : 
                                $path = "uploads/".$row['gambar_produk'];
                        ?>
                        <tr>
                            <td class="ps-4 text-muted"><?= $no++ ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if(file_exists($path) && $row['gambar_produk'] != ""): ?>
                                        <img src="<?= $path ?>" class="img-produk me-3">
                                    <?php else: ?>
                                        <div class="img-produk me-3 bg-light d-flex align-items-center justify-content-center text-muted">
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
                            <td>
                                <span class="badge rounded-pill <?= $row['status'] == 'Tersedia' ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $row['status'] ?>
                                </span>
                            </td>
                            <td class="text-center pe-4">
                                <a href="edit-produk.php?id=<?= $row['id_produk'] ?>" class="btn-action bg-warning text-white me-1 text-decoration-none shadow-sm">
                                    <i class="fas fa-edit small"></i>
                                </a>
                                <a href="hapus-produk.php?id=<?= $row['id_produk'] ?>" class="btn-action bg-danger text-white text-decoration-none shadow-sm" onclick="return confirm('Hapus produk ini?')">
                                    <i class="fas fa-trash small"></i>
                                </a>
                            </td>
                        </tr>
                        <?php 
                            endwhile; 
                        else:
                        ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">Belum ada data produk tersedia.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>