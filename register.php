<?php 
session_start(); 
require_once "config/koneksi.php"; 

if(isset($_POST['register'])){
    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $telp     = mysqli_real_escape_string($conn, $_POST['telp']);
    $alamat   = mysqli_real_escape_string($conn, $_POST['alamat']);

    // Cek username
    $cek = mysqli_query($conn, "SELECT id_user FROM user WHERE username_user='$username'");
    if(mysqli_num_rows($cek) > 0){
        $error = "Username sudah digunakan!";
    } else {
        $insert = mysqli_query($conn, "INSERT INTO user (nama_user, username_user, password, telp_user, alamat_user, role_user) VALUES ('$nama', '$username', '$password', '$telp', '$alamat', 'pelanggan')");
        if($insert){
            header("Location: login.php?status=registered");
            exit;
        } else {
            $error = "Gagal mendaftar. Silakan coba lagi.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Toko Adat</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
            padding: 40px 0;
        }
        .reg-card {
            border: none;
            border-radius: 25px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 100%;
            max-width: 550px;
            margin: auto;
        }
        .reg-header {
            background: #0d6efd;
            color: white;
            padding: 35px;
            text-align: center;
        }
        .reg-body {
            background: white;
            padding: 40px;
        }
        .form-control {
            border-radius: 12px;
            padding: 10px 18px;
            background-color: #f8f9fa;
            border: 1px solid #eee;
            font-size: 14px;
        }
        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
            background-color: #fff;
        }
        .btn-reg {
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-reg:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
        }
    </style>
</head>
<body>

<div class="container px-3">
    <div class="reg-card">
        <div class="reg-header">
            <h3 class="fw-bold mb-1">Daftar Akun</h3>
            <p class="mb-0 opacity-75">Bergabunglah untuk mulai belanja</p>
        </div>
        <div class="reg-body">
            <?php if(isset($error)): ?>
                <div class="alert alert-danger border-0 small py-2 mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small fw-bold text-muted mb-1">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" placeholder="Nama sesuai KTP" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted mb-1">Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Username unik" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted mb-1">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Min. 6 karakter" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold text-muted mb-1">No. WhatsApp</label>
                        <input type="tel" name="telp" class="form-control" placeholder="08xxxx" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold text-muted mb-1">Alamat Pengiriman</label>
                        <textarea name="alamat" class="form-control" rows="3" placeholder="Alamat lengkap..." required></textarea>
                    </div>
                </div>

                <button type="submit" name="register" class="btn btn-primary w-100 btn-reg mt-4 mb-3">
                    DAFTAR SEKARANG
                </button>

                <div class="text-center">
                    <small class="text-muted">Sudah punya akun? <a href="login.php" class="text-decoration-none fw-bold">Masuk</a></small>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>