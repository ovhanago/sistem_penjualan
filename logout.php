<?php
// Deteksi role dari session yang ada untuk menentukan nama session mana yang akan di-destroy
$roles = ['ADMIN', 'PELANGGAN', 'PEMILIK', 'PENGIRIM'];

foreach($roles as $role) {
    session_name("SESS_" . $role);
    session_start();

    // Jika di session ini ada data user, maka ini session yang ingin kita hapus
    if(isset($_SESSION['id_user'])) {
        session_destroy();
        // Hapus juga cookie-nya agar benar-benar bersih
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        break; // Hapus satu session saja per panggil logout
    }
    // Tutup session sementara sebelum cek session name berikutnya jika tidak ketemu
    session_write_close();
}

header("Location: login.php");
exit;