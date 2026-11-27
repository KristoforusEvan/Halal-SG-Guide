<?php
// login.php (POST) - autentikasi email + password, menyimpan role ke $_SESSION
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

// Ambil kolom role agar sistem bisa membedakan redirect admin vs user biasa
$stmt = mysqli_prepare($conn, 'SELECT id, username, email, nama, password, role FROM users WHERE email = ?');
mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

if ($row && password_verify($password, $row['password'])) {
    session_regenerate_id(true);
    $_SESSION['user_id']  = (int) $row['id'];
    $_SESSION['username'] = $row['username'];
    $_SESSION['email']    = $row['email'];
    $_SESSION['nama']     = !empty($row['nama']) ? $row['nama'] : $row['username'];
    $_SESSION['role']     = $row['role'];

    berhasil('Login berhasil.', array(
        'nama' => $_SESSION['nama'],
        'role' => $_SESSION['role']
    ));
}

gagal('Email atau password salah.', 401);