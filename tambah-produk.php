<?php
require_once "config/koneksi.php";

if(isset($_POST['simpan'])){

    $nama   = mysqli_real_escape_string($conn, $_POST['nama']);
    $jumlah = mysqli_real_escape_string($conn, $_POST['jumlah']);
    $harga  = mysqli_real_escape_string($conn, $_POST['harga']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    // ================= UPLOAD GAMBAR =================
    $namaBaru = "";

    if(isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0){

        $namaFile = $_FILES['gambar']['name'];
        $tmpFile  = $_FILES['gambar']['tmp_name'];

        $extValid = ['jpg','jpeg','png'];
        $ext = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

        if(in_array($ext, $extValid)){

            // Buat nama unik
            $namaBaru = uniqid() . "." . $ext;

            // Pindahkan ke folder uploads
            move_uploaded_file($tmpFile, "uploads/" . $namaBaru);

        } else {
            echo "<script>alert('Format gambar harus jpg, jpeg, atau png');</script>";
            return;
        }
    }

    // ================= SIMPAN KE DATABASE =================
    mysqli_query($conn, "INSERT INTO produk 
        (nama_produk, jumlah_produk, harga_produk, status, gambar_produk) 
        VALUES ('$nama','$jumlah','$harga','$status','$namaBaru')");

    header("Location: dashboard-admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4 mb-5">

<h3>Tambah Produk</h3>

<form method="POST" enctype="multipart/form-data">

    <div class="mb-3">
        <label>Nama Produk</label>
        <input type="text" name="nama" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Jumlah</label>
        <input type="number" name="jumlah" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Harga</label>
        <input type="number" name="harga" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Status</label>
        <select name="status" class="form-control">
            <option value="Tersedia">Tersedia</option>
            <option value="Habis">Habis</option>
        </select>
    </div>

    <div class="mb-3">
        <label>Upload Gambar</label>
        <input type="file" name="gambar" class="form-control" accept="image/*" required>
    </div>

    <button type="submit" name="simpan" class="btn btn-primary" >
        Simpan
    </button>

    <button type="button" class="btn btn-danger" onclick="history.back()">
    Batal
</button>

</form>

</body>
</html>