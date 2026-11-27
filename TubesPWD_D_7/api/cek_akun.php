<?php
// cek_akun.php (GET) - mengecek ketersediaan username atau email secara real-time saat registrasi
require 'fungsi.php';
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    gagal('Metode permintaan tidak diizinkan.', 405);
}

if (isset($_GET['username']) && is_string($_GET['username'])) {
    $username = trim($_GET['username']);
    $err = validasiUsername($username);
    if ($err !== '') {
        berhasil('', array('tersedia' => false, 'pesan' => $err));
    }

    $stmt = mysqli_prepare($conn, 'SELECT id FROM users WHERE username = ?');
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $dipakai = mysqli_num_rows(mysqli_stmt_get_result($stmt)) > 0;

    berhasil('', array(
        'tersedia' => !$dipakai,
        'pesan'    => $dipakai ? 'Username sudah dipakai, silakan pilih yang lain.' : 'Username tersedia.'
    ));
}

if (isset($_GET['email']) && is_string($_GET['email'])) {
    $email = strtolower(trim($_GET['email']));
    $err = validasiEmail($email);
    if ($err !== '') {
        berhasil('', array('tersedia' => false, 'pesan' => $err));
    }

    $stmt = mysqli_prepare($conn, 'SELECT id FROM users WHERE email = ?');
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $dipakai = mysqli_num_rows(mysqli_stmt_get_result($stmt)) > 0;

    berhasil('', array(
        'tersedia' => !$dipakai,
        'pesan'    => $dipakai ? 'Email sudah terdaftar, silakan gunakan email lain.' : 'Email tersedia.'
    ));
}

gagal('Parameter pengecekan tidak lengkap.');