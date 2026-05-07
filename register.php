<?php
require_once "config/koneksi.php";

if(isset($_POST['register'])){

    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $alamat   = mysqli_real_escape_string($conn, $_POST['alamat']);
    $telp     = mysqli_real_escape_string($conn, $_POST['telp']);

    $cek = mysqli_query($conn, "SELECT * FROM user WHERE username_user='$username'");

    if(mysqli_num_rows($cek) > 0){
        $error = "Username sudah digunakan!";
    } else {
        mysqli_query($conn, "INSERT INTO user 
            (nama_user, username_user, password, role_user, alamat_user, telp_user)
            VALUES 
            ('$nama','$username','$password','pelanggan','$alamat','$telp')");

        header("Location: login.php?register=success");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Toko Adat Bajawa</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
            font-family: 'Segoe UI', sans-serif;
        }
        .register-card {
            width: 100%;
            max-width: 450px;
            border: none;
            border-radius: 25px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            background: white;
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .register-header {
            padding: 35px 35px 15px;
            text-align: center;
        }
        .brand-icon {
            width: 60px;
            height: 60px;
            background: rgba(13, 110, 253, 0.1);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 24px;
            color: #0d6efd;
        }
        .register-body { padding: 10px 35px 35px; }
        .form-label { font-size: 13px; font-weight: 600; color: #6c757d; margin-bottom: 5px; }
        .form-control {
            border-radius: 12px;
            padding: 10px 15px;
            background-color: #f8f9fa;
            border: 1px solid #f1f1f1;
            font-size: 14px;
            transition: 0.3s;
        }
        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
            border-color: #0d6efd;
            background-color: white;
        }
        .btn-register {
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s;
            margin-top: 15px;
        }
        .btn-register:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3); }
        .link-text { font-size: 13px; text-decoration: none; }
    </style>
</head>
<body>

    <div class="register-card">
        <div class="register-header">
            <div class="brand-icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <h4 class="fw-bold mb-1">Daftar Akun</h4>
            <p class="text-muted small">Lengkapi data untuk mulai belanja</p>
        </div>
        
        <div class="register-body">
            <?php if(isset($error)) : ?>
                <div class="alert alert-danger border-0 small text-center mb-3">
                    <i class="fas fa-exclamation-circle me-1"></i> <?= $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" placeholder="John Doe" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" placeholder="john123" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">No. Telepon</label>
                    <input type="tel" name="telp" class="form-control" placeholder="08123456789" required>
                </div>

                <div class="mb-4">
                    <label class="form-label">Alamat Lengkap</label>
                    <textarea name="alamat" class="form-control" rows="2" placeholder="Jl. Raya Bajawa No. 1..." required></textarea>
                </div>

                <button type="submit" name="register" class="btn btn-primary w-100 btn-register shadow-sm">
                    Buat Akun Sekarang
                </button>

                <div class="text-center mt-4">
                    <span class="text-muted small">Sudah punya akun?</span>
                    <a href="login.php" class="link-text text-primary fw-bold ms-1">Login di sini</a>
                </div>
                <div class="text-center mt-2">
                    <a href="index.php" class="link-text text-muted small"><i class="fas fa-arrow-left me-1"></i> Kembali ke Toko</a>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>