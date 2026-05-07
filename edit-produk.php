<?php
session_name("SESS_ADMIN");
session_start();
require_once "config/koneksi.php";

// Proteksi: Hanya admin yang boleh akses
if(!isset($_SESSION['id_user']) || $_SESSION['role_user'] != 'admin'){
    header("Location: login.php");
    exit;
}

// Amankan ID
if(!isset($_GET['id'])) {
    header("Location: dashboard-admin.php?page=produk");
    exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$data = mysqli_query($conn, "SELECT * FROM produk WHERE id_produk='$id'");
$row = mysqli_fetch_assoc($data);

if(!$row) {
    header("Location: dashboard-admin.php?page=produk");
    exit;
}

if(isset($_POST['update'])){

    $nama   = mysqli_real_escape_string($conn, $_POST['nama']);
    $jumlah = mysqli_real_escape_string($conn, $_POST['jumlah']);
    $harga  = mysqli_real_escape_string($conn, $_POST['harga']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $gambarLama = $row['gambar_produk'];
    $gambarBaru = $gambarLama;

    // ================= CEK UPLOAD GAMBAR BARU =================
    if(isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0){

        $namaFile = $_FILES['gambar']['name'];
        $tmpFile  = $_FILES['gambar']['tmp_name'];

        $extValid = ['jpg','jpeg','png'];
        $ext = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

        if(in_array($ext, $extValid)){

            $gambarBaru = uniqid() . "." . $ext;

            move_uploaded_file($tmpFile, "uploads/" . $gambarBaru);

            // Hapus gambar lama kalau ada
            if(!empty($gambarLama) && file_exists("uploads/" . $gambarLama)){
                unlink("uploads/" . $gambarLama);
            }

        } else {
            echo "<script>alert('Format gambar harus jpg, jpeg, atau png');</script>";
            exit;
        }
    }

    // ================= UPDATE DATABASE =================
    mysqli_query($conn, "UPDATE produk SET
        nama_produk='$nama',
        jumlah_produk='$jumlah',
        harga_produk='$harga',
        status='$status',
        gambar_produk='$gambarBaru'
        WHERE id_produk='$id'");

    header("Location: dashboard-admin.php?page=produk");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - Admin Panel</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .form-label { font-weight: 600; color: #495057; }
        .img-preview { border-radius: 15px; object-fit: cover; border: 3px solid #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card p-4 p-md-5">
                <div class="d-flex align-items-center mb-4">
                    <button type="button" class="btn btn-light rounded-circle me-3" onclick="location.href='dashboard-admin.php?page=produk'">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <h3 class="fw-bold mb-0">Edit Produk</h3>
                </div>

                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Nama Produk</label>
                        <input type="text" name="nama" value="<?= htmlspecialchars($row['nama_produk']); ?>" class="form-control rounded-pill px-4" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stok</label>
                            <input type="number" name="jumlah" value="<?= $row['jumlah_produk']; ?>" class="form-control rounded-pill px-4" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga (Rp)</label>
                            <input type="number" name="harga" value="<?= $row['harga_produk']; ?>" class="form-control rounded-pill px-4" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status Produk</label>
                        <select name="status" class="form-select rounded-pill px-4" required>
                            <option value="Tersedia" <?= ($row['status'] == 'Tersedia') ? 'selected' : ''; ?>>Tersedia</option>
                            <option value="Habis" <?= ($row['status'] == 'Habis') ? 'selected' : ''; ?>>Habis</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label d-block">Gambar Saat Ini</label>
                        <?php if(!empty($row['gambar_produk']) && file_exists("uploads/".$row['gambar_produk'])) : ?>
                            <img src="uploads/<?= $row['gambar_produk']; ?>" width="120" height="120" class="img-preview mb-3">
                        <?php else : ?>
                            <div class="p-4 bg-light text-center rounded-4 mb-3 small text-muted">Tidak ada gambar</div>
                        <?php endif; ?>
                        <input type="file" name="gambar" class="form-control" accept="image/*">
                        <small class="text-muted mt-1 d-block">Biarkan kosong jika tidak ingin mengganti gambar.</small>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" name="update" class="btn btn-primary btn-lg rounded-pill fw-bold shadow-sm">
                            <i class="fas fa-save me-2"></i> Simpan Perubahan
                        </button>
                        <a href="dashboard-admin.php?page=produk" class="btn btn-light btn-lg rounded-pill fw-bold text-muted">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>