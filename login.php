<?php 
session_start(); 
require_once "config/koneksi.php"; 

if(isset($_SESSION['id_user'])){
    if($_SESSION['role_user'] == 'admin') header("Location: dashboard-admin.php");
    elseif($_SESSION['role_user'] == 'pelanggan') header("Location: dashboard-pelanggan.php");
    else header("Location: index.php");
    exit;
}

if(isset($_POST['login'])){
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role     = mysqli_real_escape_string($conn, $_POST['role']);

    $query = mysqli_query($conn, 
        "SELECT * FROM user 
         WHERE username_user='$username' 
         AND password='$password' 
         AND role_user='$role'"
    );

    if(mysqli_num_rows($query) > 0){
        $data = mysqli_fetch_assoc($query);

        $_SESSION['id_user']   = $data['id_user'];
        $_SESSION['nama_user'] = $data['nama_user'];
        $_SESSION['role_user'] = $data['role_user'];

        if($role == "admin"){
            header("Location: dashboard-admin.php");
        } elseif($role == "pemilik"){
            header("Location: dashboard-pemilik.php");
        } elseif($role == "pengirim"){
            header("Location: dashboard-pengirim.php");
        } else {
            header("Location: dashboard-pelanggan.php");
        }
        exit;

    } else {
        $error = "Username, Password, atau Role salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Toko Adat</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .login-card {
            border: none;
            border-radius: 25px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
            margin: auto;
        }
        .login-header {
            background: #0d6efd;
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .login-body {
            background: white;
            padding: 40px 35px;
        }
        .form-control {
            border-radius: 12px;
            padding: 12px 20px;
            background-color: #f8f9fa;
            border: 1px solid #eee;
        }
        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
            background-color: #fff;
        }
        .btn-login {
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
        }
        .role-selector {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
        }
        .role-option {
            flex: 1;
            text-align: center;
        }
        .role-option input { display: none; }
        .role-option label {
            display: block;
            padding: 10px;
            background: #f8f9fa;
            border: 1px solid #eee;
            border-radius: 10px;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.2s;
        }
        .role-option input:checked + label {
            background: #0d6efd;
            color: white;
            border-color: #0d6efd;
        }
    </style>
</head>
<body>

<div class="container p-3">
    <div class="login-card">
        <div class="login-header">
            <h3 class="fw-bold mb-1">Selamat Datang</h3>
            <p class="mb-0 opacity-75">Silakan masuk ke akun Anda</p>
        </div>
        <div class="login-body">
            <?php if(isset($error)): ?>
                <div class="alert alert-danger border-0 small py-2 mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-user text-muted"></i></span>
                        <input type="text" name="username" class="form-control border-start-0 ps-0" placeholder="Masukkan username" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-lock text-muted"></i></span>
                        <input type="password" name="password" class="form-control border-start-0 ps-0" placeholder="Masukkan password" required>
                    </div>
                </div>

                <label class="form-label small fw-bold text-muted mb-2">Masuk Sebagai</label>
                <div class="role-selector">
                    <div class="role-option">
                        <input type="radio" name="role" value="pelanggan" id="role_pel" checked>
                        <label for="role_pel">Pelanggan</label>
                    </div>
                    <div class="role-option">
                        <input type="radio" name="role" value="admin" id="role_adm">
                        <label for="role_adm">Admin</label>
                    </div>
                    <div class="role-option">
                        <input type="radio" name="role" value="pengirim" id="role_peng">
                        <label for="role_peng">Kurir</label>
                    </div>
                </div>

                <button type="submit" name="login" class="btn btn-primary w-100 btn-login mb-3">
                    MASUK SEKARANG
                </button>

                <div class="text-center">
                    <small class="text-muted">Belum punya akun? <a href="register.php" class="text-decoration-none fw-bold">Daftar</a></small>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>