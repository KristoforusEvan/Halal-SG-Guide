<?php
// register.php (POST) - daftar akun baru: username, email, password.
// (nama lengkap dan asal kota opsional, diisi nanti lewat halaman profil)
// Password dienkripsi dengan password_hash (bcrypt) sebelum disimpan.
require 'fungsi.php';
require 'koneksi.php';

wajibPost();

$username = ambilPost('username');
$email    = strtolower(ambilPost('email'));   // email selalu huruf kecil
$password = ambilPassword();

// Validasi di server (validasi JS bisa dilewati, PHP tidak)
$error = validasiUsername($username);
if ($error === '') { $error = validasiEmail($email); }
if ($error === '') { $error = validasiPassword($password); }
if ($error !== '') {
    gagal($error);
}

// Cek username atau email sudah dipakai atau belum
$cek = mysqli_prepare($conn, 'SELECT id FROM users WHERE username = ? OR email = ?');
mysqli_stmt_bind_param($cek, 'ss', $username, $email);
mysqli_stmt_execute($cek);
$resCek = mysqli_stmt_get_result($cek);
if (mysqli_num_rows($resCek) > 0) {
    gagal('Username atau email sudah terdaftar. Silakan gunakan yang lain.');
}

// Enkripsi password pakai bcrypt (otomatis ada salt), bukan md5
$password_enkripsi = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = mysqli_prepare($conn, 'INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
    mysqli_stmt_bind_param($stmt, 'sss', $username, $email, $password_enkripsi);
    mysqli_stmt_execute($stmt);
} catch (mysqli_sql_exception $e) {
    // Kode 1062 = duplikat (dua orang daftar di detik yang sama)
    if ($e->getCode() === 1062) {
        gagal('Username atau email sudah terdaftar. Silakan gunakan yang lain.');
    }
    throw $e;
}

berhasil('Registrasi berhasil! Silakan login menggunakan email dan password Anda.');
