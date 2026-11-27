<?php
// login.php (POST) - login pakai email + password.
require 'fungsi.php';
require 'koneksi.php';

wajibPost();

$email    = strtolower(ambilPost('email'));
$password = ambilPassword();

if ($email === '' || $password === '') {
    gagal('Email dan password wajib diisi.');
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    gagal('Format email tidak valid.');
}

// Ambil user berdasarkan email SAJA, lalu cek password dengan password_verify
$stmt = mysqli_prepare($conn, 'SELECT id, username, email, nama, password FROM users WHERE email = ?');
mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

if ($row && password_verify($password, $row['password'])) {
    session_regenerate_id(true); // cegah session fixation
    $_SESSION['user_id']  = (int) $row['id'];
    $_SESSION['username'] = $row['username'];
    $_SESSION['email']    = $row['email'];
    // Nama yang ditampilkan: nama lengkap kalau sudah diisi, kalau belum pakai username
    $_SESSION['nama']     = !empty($row['nama']) ? $row['nama'] : $row['username'];
    berhasil('Login berhasil.', array('nama' => $_SESSION['nama']));
}

// Pesan sengaja disamakan agar orang luar tidak bisa menebak email mana yang terdaftar
gagal('Email atau password salah.', 401);
