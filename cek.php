<?php
require 'config/koneksi.php';

if ($conn) {
    echo "Koneksi berhasil";
} else {
    echo "Koneksi gagal";
}