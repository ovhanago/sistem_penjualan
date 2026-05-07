<?php
session_start();
require_once "config/koneksi.php";

$step = 1; // 1: Input identitas, 2: Reset password
$error = "";
$success = "";

if(isset($_POST['cek_akun'])){
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $telp     = mysqli_real_escape_string($conn, $_POST['telp']);

    $query = mysqli_query($conn, "SELECT * FROM user WHERE username_user='$username' AND telp_user='$telp'");
    
    if(mysqli_num_rows($query) > 0){
        $data = mysqli_fetch_assoc($query);
        $_SESSION['reset_id'] = $data['id_user'];
        $step = 2;
    } else {
        $error = "Data tidak ditemukan! Pastikan username dan no. telp benar.";
    }
}

if(isset($_POST['reset_password'])){
    $id_user = $_SESSION['reset_id'];
    $new_pass = mysqli_real_escape_string($conn, $_POST['new_pass']);
    $confirm_pass = mysqli_real_escape_string($conn, $_POST['confirm_pass']);

    if($new_pass === $confirm_pass){
        mysqli_query($conn, "UPDATE user SET password='$new_pass' WHERE id_user='$id_user'");
        unset($_SESSION['reset_id']);
        $success = "Password berhasil diperbarui! Silakan login.";
        header("refresh:2;url=login.php");
    } else {
        $error = "Konfirmasi password tidak cocok!";
        $step = 2;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Toko Adat</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #6c757d 0%, #343a40 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .reset-card {
            border: none;
            border-radius: 25px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            background: white;
            padding: 40px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            
            <div class="card reset-card">
                <div class="text-center mb-4">
                    <i class="fas fa-key fa-3x text-secondary mb-3"></i>
                    <h4 class="fw-bold">Lupa Password</h4>
                    <p class="text-muted small">Pulihkan akses ke akun Anda</p>
                </div>

                <?php if($error != ""): ?>
                    <div class="alert alert-danger border-0 small py-2"><?= $error ?></div>
                <?php endif; ?>

                <?php if($success != ""): ?>
                    <div class="alert alert-success border-0 small py-2"><?= $success ?></div>
                <?php endif; ?>

                <?php if($step == 1): ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="small fw-bold text-muted mb-1">Username</label>
                            <input type="text" name="username" class="form-control bg-light border-0" style="border-radius:12px; padding:12px;" required>
                        </div>
                        <div class="mb-4">
                            <label class="small fw-bold text-muted mb-1">Nomor Telepon</label>
                            <input type="text" name="telp" class="form-control bg-light border-0" style="border-radius:12px; padding:12px;" placeholder="Contoh: 08123456789" required>
                        </div>
                        <button type="submit" name="cek_akun" class="btn btn-dark w-100 py-3 fw-bold" style="border-radius:12px;">Verifikasi Akun</button>
                    </form>
                <?php else: ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="small fw-bold text-muted mb-1">Password Baru</label>
                            <input type="password" name="new_pass" class="form-control bg-light border-0" style="border-radius:12px; padding:12px;" required>
                        </div>
                        <div class="mb-4">
                            <label class="small fw-bold text-muted mb-1">Konfirmasi Password</label>
                            <input type="password" name="confirm_pass" class="form-control bg-light border-0" style="border-radius:12px; padding:12px;" required>
                        </div>
                        <button type="submit" name="reset_password" class="btn btn-primary w-100 py-3 fw-bold" style="border-radius:12px;">Perbarui Password</button>
                    </form>
                <?php endif; ?>

                <div class="text-center mt-4">
                    <a href="login.php" class="text-decoration-none text-muted small"><i class="fas fa-arrow-left me-1"></i> Kembali ke Login</a>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>