<?php
session_start();
require_once "config/koneksi.php";

if(!isset($_SESSION['id_user']) || $_SESSION['role_user'] != 'admin'){
    header("Location: login.php");
    exit;
}

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Toko Adat</title>

    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root{
            --primary-color:#0d6efd;
            --sidebar-bg:#ffffff;
            --main-bg:#f8f9fa;
        }

        body{
            background-color:var(--main-bg);
            font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;
            overflow-x:hidden;
        }

        /* SIDEBAR */
        .sidebar{
            width:260px;
            height:100vh;
            background:var(--sidebar-bg);
            position:fixed;
            left:0;
            top:0;
            box-shadow:4px 0 10px rgba(0,0,0,0.03);
            z-index:1000;
            transition:all 0.3s;
        }

        .sidebar-header{
            padding:30px 25px;
            border-bottom:1px solid #f1f1f1;
        }

        .nav-list{
            padding:20px 15px;
        }

        .nav-item{
            display:flex;
            align-items:center;
            padding:12px 15px;
            color:#6c757d;
            text-decoration:none;
            border-radius:12px;
            margin-bottom:5px;
            transition:all 0.2s;
        }

        .nav-item i{
            width:25px;
            font-size:18px;
            margin-right:12px;
        }

        .nav-item:hover,
        .nav-item.active{
            background-color:rgba(13,110,253,0.1);
            color:var(--primary-color);
            font-weight:600;
        }

        /* MAIN CONTENT */
        .main-content{
            margin-left:260px;
            padding:30px;
            min-height:100vh;
        }

        .top-navbar{
            background:#fff;
            padding:15px 30px;
            border-radius:15px;
            box-shadow:0 2px 10px rgba(0,0,0,0.03);
            margin-bottom:30px;
            display:flex;
            justify-content:space-between;
            align-items:center;
        }

        /* CARD */
        .stat-card{
            border:none;
            border-radius:20px;
            padding:20px;
            background:#fff;
            box-shadow:0 4px 15px rgba(0,0,0,0.03);
            transition:transform 0.3s;
        }

        .stat-card:hover{
            transform:translateY(-5px);
        }

        .icon-box{
            width:50px;
            height:50px;
            border-radius:12px;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:24px;
        }

        /* TABLE */
        .card-table{
            border:none;
            border-radius:20px;
            box-shadow:0 4px 15px rgba(0,0,0,0.03);
            overflow:hidden;
        }

        .table thead{
            background-color:#f8f9fa;
        }

        .table th{
            border:none;
            padding:15px;
            font-weight:600;
            font-size:14px;
            color:#6c757d;
        }

        .table td{
            padding:15px;
            border-bottom:1px solid #f8f9fa;
            vertical-align:middle;
        }

        /* MOBILE */
        @media(max-width:768px){

            .sidebar{
                width:100%;
                height:auto;
                position:relative;
            }

            .main-content{
                margin-left:0;
                padding:15px;
            }

            .top-navbar{
                flex-direction:column;
                align-items:flex-start;
                gap:10px;
            }
        }
    </style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">

    <div class="sidebar-header">
        <h4 class="fw-bold text-primary mb-0">
            <i class="fas fa-store me-2"></i>
            Admin Panel
        </h4>
    </div>

    <div class="nav-list">

        <a href="dashboard-admin.php?page=dashboard"
           class="nav-item <?= $page == 'dashboard' ? 'active' : '' ?>">
            <i class="fas fa-home"></i>
            Dashboard
        </a>

        <a href="dashboard-admin.php?page=produk"
           class="nav-item <?= $page == 'produk' ? 'active' : '' ?>">
            <i class="fas fa-box"></i>
            Data Produk
        </a>

        <a href="dashboard-admin.php?page=pesanan"
           class="nav-item <?= $page == 'pesanan' ? 'active' : '' ?>">
            <i class="fas fa-shopping-bag"></i>
            Pesanan
        </a>

        <a href="dashboard-admin.php?page=pelanggan"
           class="nav-item <?= $page == 'pelanggan' ? 'active' : '' ?>">
            <i class="fas fa-users"></i>
            Pelanggan
        </a>

        <div class="mt-4 pt-4 border-top">

            <a href="logout.php" class="nav-item text-danger">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>

        </div>

    </div>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">

    <!-- TOP NAVBAR -->
    <div class="top-navbar">

        <h5 class="fw-bold mb-0">
            <?= ucfirst($page) ?> Overview
        </h5>

        <div class="d-flex align-items-center">

            <div class="text-end me-3">
                <small class="text-muted d-block">
                    Selamat datang,
                </small>

                <span class="fw-bold">
                    <?= $_SESSION['nama_user'] ?>
                </span>
            </div>

            <img
                src="https://ui-avatars.com/api/?name=<?= $_SESSION['nama_user'] ?>&background=0D6EFD&color=fff"
                class="rounded-circle"
                width="40"
            >

        </div>

    </div>

    <!-- DASHBOARD -->
    <?php if($page == 'dashboard'): ?>

        <div class="row g-4">

            <!-- TOTAL PRODUK -->
            <div class="col-md-4">

                <div class="stat-card">

                    <div class="d-flex justify-content-between">

                        <div>

                            <p class="text-muted mb-1 small">
                                Total Produk
                            </p>

                            <h3 class="fw-bold mb-0">

                                <?php
                                $q_total_produk = mysqli_query($conn, "SELECT id_produk FROM produk");
                                echo mysqli_num_rows($q_total_produk);
                                ?>

                            </h3>

                        </div>

                        <div class="icon-box bg-primary text-white">
                            <i class="fas fa-box"></i>
                        </div>

                    </div>

                </div>

            </div>

            <!-- TOTAL PESANAN -->
            <div class="col-md-4">

                <div class="stat-card">

                    <div class="d-flex justify-content-between">

                        <div>

                            <p class="text-muted mb-1 small">
                                Total Pesanan
                            </p>

                            <h3 class="fw-bold mb-0">

                                <?php
                                $q_total_pesanan = mysqli_query($conn, "SELECT id_pesanan FROM pesanan");
                                echo mysqli_num_rows($q_total_pesanan);
                                ?>

                            </h3>

                        </div>

                        <div class="icon-box bg-success text-white">
                            <i class="fas fa-shopping-cart"></i>
                        </div>

                    </div>

                </div>

            </div>

            <!-- TOTAL PELANGGAN -->
            <div class="col-md-4">

                <div class="stat-card">

                    <div class="d-flex justify-content-between">

                        <div>

                            <p class="text-muted mb-1 small">
                                Total Pelanggan
                            </p>

                            <h3 class="fw-bold mb-0">

                                <?php
                                $q_total_user = mysqli_query($conn, "SELECT id_user FROM user WHERE role_user='pelanggan'");
                                echo mysqli_num_rows($q_total_user);
                                ?>

                            </h3>

                        </div>

                        <div class="icon-box bg-info text-white">
                            <i class="fas fa-users"></i>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    <?php endif; ?>

</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>

</body>
</html>