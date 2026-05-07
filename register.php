<?php
require_once "config/koneksi.php";

if(isset($_POST['register'])){

    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $alamat   = mysqli_real_escape_string($conn, $_POST['alamat']);
    $telp     = mysqli_real_escape_string($conn, $_POST['telp']);

    // Cek username sudah ada atau belum
    $cek = mysqli_query($conn, 
        "SELECT * FROM user WHERE username_user='$username'"
    );

    if(mysqli_num_rows($cek) > 0){

        $error = "Username sudah digunakan!";

    } else {

        mysqli_query($conn, "INSERT INTO user 
            (nama_user, username_user, password, role_user, alamat_user, telp_user)
            VALUES 
            ('$nama','$username','$password','pelanggan','$alamat','$telp')");

        echo "<script>
                alert('Registrasi berhasil! Silakan login.');
                window.location='login.php';
              </script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Pelanggan</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">

            <div class="card shadow">
                <div class="card-header text-center">
                    <h4>Registrasi Pelanggan</h4>
                </div>

                <div class="card-body">

                    <?php if(isset($error)) : ?>
                        <div class="alert alert-danger">
                            <?= $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">

                        <div class="mb-3">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Alamat</label>
                            <textarea name="alamat" class="form-control" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label>No. Telepon</label>
                            <input type="text" name="telp" class="form-control" required>
                        </div>

                        <button type="submit" name="register" class="btn btn-success w-100">
                            Daftar
                        </button>

                    </form>

                    <div class="text-center mt-3">
                        Sudah punya akun? 
                        <a href="login.php">Login</a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>