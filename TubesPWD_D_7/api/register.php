<?php
require 'fungsi.php';
require 'koneksi.php';

wajibPost();

$username = ambilPost('username');
$email    = strtolower(ambilPost('email'));   
$password = ambilPassword();

$error = validasiUsername($username);
if ($error === '') { $error = validasiEmail($email); }
if ($error === '') { $error = validasiPassword($password); }
if ($error !== '') {
    gagal($error);
}

$cek = mysqli_prepare($conn, 'SELECT id FROM users WHERE username = ? OR email = ?');
mysqli_stmt_bind_param($cek, 'ss', $username, $email);
mysqli_stmt_execute($cek);
$resCek = mysqli_stmt_get_result($cek);
if (mysqli_num_rows($resCek) > 0) {
    gagal('Username atau email sudah terdaftar. Silakan gunakan yang lain.');
}

$password_enkripsi = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = mysqli_prepare($conn, 'INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
    mysqli_stmt_bind_param($stmt, 'sss', $username, $email, $password_enkripsi);
    mysqli_stmt_execute($stmt);
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() === 1062) {
        gagal('Username atau email sudah terdaftar. Silakan gunakan yang lain.');
    }
    throw $e;
}

berhasil('Registrasi berhasil! Silakan login menggunakan email dan password Anda.');
