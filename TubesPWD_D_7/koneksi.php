<?php
$conn = mysqli_connect('localhost', 'root', '', 'db_wisata_halal_sg');

if (!$conn) {
    die('Koneksi gagal: ' . mysqli_connect_error());
}
?>