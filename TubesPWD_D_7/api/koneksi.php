<?php
// koneksi.php - koneksi ke database MySQL (XAMPP: user root, password kosong)
// Di-require SETELAH fungsi.php. Kalau gagal konek, mysqli melempar error
// yang ditangkap fungsi.php lalu dibalas sebagai JSON.
$conn = mysqli_connect('localhost', 'root', '', 'db_wisata_halal_sg');
mysqli_set_charset($conn, 'utf8mb4');
