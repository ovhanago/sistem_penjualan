<?php
require_once "config/koneksi.php";

$id = $_GET['id'];

$data = mysqli_query($conn, "SELECT * FROM produk WHERE id_produk='$id'");
$row = mysqli_fetch_assoc($data);

if(isset($_POST['update'])){

    $nama   = mysqli_real_escape_string($conn, $_POST['nama']);
    $jumlah = mysqli_real_escape_string($conn, $_POST['jumlah']);
    $harga  = mysqli_real_escape_string($conn, $_POST['harga']);

    $gambarLama = $row['gambar_produk'];
    $gambarBaru = $gambarLama;

    // ================= CEK UPLOAD GAMBAR BARU =================
    if(isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0){

        $namaFile = $_FILES['gambar']['name'];
        $tmpFile  = $_FILES['gambar']['tmp_name'];

        $extValid = ['jpg', 'jpeg', 'png'];
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
        gambar_produk='$gambarBaru'
        WHERE id_produk='$id'
    ");

    header("Location: dashboard-admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Produk</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4">

<h3>Edit Produk</h3>

<form method="POST" enctype="multipart/form-data">

    <div class="mb-2">
        <label>Nama Produk</label>
        <input 
            type="text" 
            name="nama" 
            value="<?= $row['nama_produk']; ?>" 
            class="form-control" 
            required
        >
    </div>

    <div class="mb-2">
        <label>Jumlah</label>
        <input 
            type="number" 
            name="jumlah" 
            value="<?= $row['jumlah_produk']; ?>" 
            class="form-control" 
            required
        >
    </div>

    <div class="mb-2">
        <label>Harga</label>
        <input 
            type="number" 
            name="harga" 
            value="<?= $row['harga_produk']; ?>" 
            class="form-control" 
            required
        >
    </div>

    <div class="mb-2">
        <label>Gambar Saat Ini</label><br>

        <?php if(!empty($row['gambar_produk'])) : ?>
            <img 
                src="uploads/<?= $row['gambar_produk']; ?>" 
                width="100" 
                class="img-thumbnail mb-2"
            >
        <?php else : ?>
            <span class="text-muted">Tidak ada gambar</span>
        <?php endif; ?>
    </div>

    <div class="mb-3">
        <label>Ganti Gambar (Opsional)</label>
        <input 
            type="file" 
            name="gambar" 
            class="form-control" 
            accept="image/*"
        >
    </div>

    <button type="submit" name="update" class="btn btn-success">
        Update
    </button>

    <button type="button" class="btn btn-danger" onclick="history.back()">
        Batal
    </button>

</form>

</body>
</html>