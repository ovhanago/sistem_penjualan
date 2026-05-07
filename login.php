<?php 
// 1. Tentukan nama session berdasarkan role yang sedang diakses atau di-input
if(isset($_POST['role'])){
    session_name("SESS_" . strtoupper($_POST['role']));
} elseif(isset($_GET['role'])){
    session_name("SESS_" . strtoupper($_GET['role']));
}

session_start(); 
require_once "config/koneksi.php"; 

// 2. Jika sudah login dengan role tertentu, arahkan ke dashboard yang sesuai
if(isset($_SESSION['id_user']) && isset($_SESSION['role_user'])){
    $role = $_SESSION['role_user'];
    if($role == "admin") header("Location: dashboard-admin.php");
    elseif($role == "pemilik") header("Location: dashboard-pemilik.php");
    elseif($role == "pengirim") header("Location: dashboard-pengirim.php");
    else header("Location: dashboard-pelanggan.php");
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
            header("Location: index.php");
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
    <title>Login - Toko Pakaian Adat</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
        }
        
        /* Container utama untuk centering */
        .wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            width: 100%;
            max-width: 350px;
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            background: white;
            overflow: hidden;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-header {
            padding: 30px 30px 10px;
            text-align: center;
        }

        .brand-icon {
            width: 65px;
            height: 65px;
            background: rgba(13, 110, 253, 0.1);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 26px;
            color: #0d6efd;
        }

        .login-body {
            padding: 10px 30px 30px;
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 5px;
        }

        .input-group-text {
            background-color: #f8f9fa;
            border: 1px solid #f1f1f1;
            color: #adb5bd;
            border-radius: 12px 0 0 12px;
        }

        .form-control, .form-select {
            border-radius: 12px;
            padding: 10px 15px;
            background-color: #f8f9fa;
            border: 1px solid #f1f1f1;
            font-size: 14px;
            transition: all 0.3s;
        }

        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
            border-color: #0d6efd;
            background-color: white;
        }

        /* Khusus input-group focus effect */
        .input-group:focus-within .input-group-text {
            border-color: #0d6efd;
            color: #0d6efd;
        }

        .btn-login {
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            font-size: 15px;
            letter-spacing: 0.5px;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
        }

        .footer {
            padding: 20px;
            text-align: center;
            color: white;
            font-size: 13px;
            opacity: 0.8;
        }

        .link-text {
            font-size: 13px;
            text-decoration: none;
            transition: color 0.2s;
        }
        
        /* Mobile adjustment */
        @media (max-height: 600px) {
            .wrapper {
                align-items: flex-start;
                padding-top: 40px;
            }
        }
    </style>
</head>
<body>

    <div class="wrapper">
        <div class="card login-card">
            <div class="login-header">
                <div class="brand-icon">
                    <i class="fas fa-store"></i>
                </div>
                <h4 class="fw-bold mb-1">Login</h4>
                <p class="text-muted small">Akses akun Toko Adat Anda</p>
            </div>
            
            <div class="login-body">
                <?php if(isset($error)) : ?>
                    <div class="alert alert-danger border-0 small py-2 mb-3 text-center">
                        <i class="fas fa-exclamation-circle me-1"></i> <?= $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" name="username" class="form-control" placeholder="Username" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Role</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                            <select name="role" class="form-select" required>
                                <option value="pelanggan">Pelanggan</option>
                                <option value="admin">Admin</option>
                                <option value="pemilik">Pemilik</option>
                                <option value="pengirim">Pengirim</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" name="login" class="btn btn-primary w-100 btn-login shadow-sm">
                        Login
                    </button>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="lupa-password.php" class="link-text text-muted">Lupa password?</a>
                        <a href="register.php" class="link-text text-primary fw-bold">Daftar Akun</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>