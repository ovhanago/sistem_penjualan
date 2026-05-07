<?php
session_start();
require_once "config/koneksi.php";

// Jika sudah login
if(isset($_SESSION['id_user'])){

    if($_SESSION['role_user'] == 'admin'){
        header("Location: dashboard-admin.php");

    } elseif($_SESSION['role_user'] == 'pemilik'){
        header("Location: dashboard-pemilik.php");

    } elseif($_SESSION['role_user'] == 'pengirim'){
        header("Location: dashboard-pengirim.php");

    } else {
        header("Location: index.php");
    }

    exit;
}

// Proses Login
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

        // SESSION LOGIN
        $_SESSION['login']      = true;
        $_SESSION['id_user']    = $data['id_user'];
        $_SESSION['nama_user']  = $data['nama_user'];
        $_SESSION['role_user']  = $data['role_user'];

        // Redirect berdasarkan role
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

<title>Login</title>

<link href="assets/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#f5f5f5;
}

.login-box{
    max-width:400px;
    margin:auto;
    margin-top:80px;
    background:white;
    padding:30px;
    border-radius:10px;
    box-shadow:0 0 10px rgba(0,0,0,0.1);
}

</style>

</head>

<body>

<div class="container">

    <div class="login-box">

        <h3 class="mb-4 text-center">
            Login
        </h3>

        <?php if(isset($error)) : ?>

            <div class="alert alert-danger">
                <?= $error; ?>
            </div>

        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">

                <label>Username</label>

                <input 
                    type="text"
                    name="username"
                    class="form-control"
                    required
                >

            </div>

            <div class="mb-3">

                <label>Password</label>

                <input 
                    type="password"
                    name="password"
                    class="form-control"
                    required
                >

            </div>

            <div class="mb-3">

                <label>Role</label>

                <select name="role" class="form-control" required>

                    <option value="">-- Pilih Role --</option>

                    <option value="admin">Admin</option>
                    <option value="pemilik">Pemilik</option>
                    <option value="pengirim">Pengirim</option>
                    <option value="pelanggan">Pelanggan</option>

                </select>

            </div>

            <button 
                type="submit"
                name="login"
                class="btn btn-primary w-100"
            >
                Login
            </button>

        </form>

    </div>

</div>

</body>
</html>