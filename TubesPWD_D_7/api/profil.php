<?php
// profil.php
//   GET  -> ambil data profil pengguna yang sedang login
//   POST -> perbarui profil
//           (username dan email wajib; nama lengkap dan asal kota opsional)
require 'fungsi.php';
require 'koneksi.php';

$user_id = wajibLogin();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $q = mysqli_prepare($conn, 'SELECT username, email, nama, asal_kota FROM users WHERE id = ?');
    mysqli_stmt_bind_param($q, 'i', $user_id);
    mysqli_stmt_execute($q);
    $data = mysqli_fetch_assoc(mysqli_stmt_get_result($q));
    if (!$data) {
        gagal('Data pengguna tidak ditemukan.', 404);
    }
    berhasil('', array('data' => $data));
}

wajibPost();

$username  = ambilPost('username');
$email     = strtolower(ambilPost('email'));
$nama      = ambilPost('nama');
$asal_kota = ambilPost('asal_kota');

$error = validasiUsername($username);
if ($error === '') { $error = validasiEmail($email); }
if ($error === '') { $error = validasiNama($nama); }
if ($error === '') { $error = validasiAsalKota($asal_kota); }
if ($error !== '') {
    gagal($error);
}

// Kolom opsional yang dikosongkan disimpan sebagai NULL
if ($nama === '')      { $nama = null; }
if ($asal_kota === '') { $asal_kota = null; }

// Username tidak boleh sama dengan akun LAIN
$cekUser = mysqli_prepare($conn, 'SELECT id FROM users WHERE username = ? AND id <> ?');
mysqli_stmt_bind_param($cekUser, 'si', $username, $user_id);
mysqli_stmt_execute($cekUser);
if (mysqli_num_rows(mysqli_stmt_get_result($cekUser)) > 0) {
    gagal('Username sudah dipakai akun lain.');
}

// Email tidak boleh sama dengan akun LAIN
$cekEmail = mysqli_prepare($conn, 'SELECT id FROM users WHERE email = ? AND id <> ?');
mysqli_stmt_bind_param($cekEmail, 'si', $email, $user_id);
mysqli_stmt_execute($cekEmail);
if (mysqli_num_rows(mysqli_stmt_get_result($cekEmail)) > 0) {
    gagal('Email sudah dipakai akun lain.');
}

try {
    $upd = mysqli_prepare($conn, 'UPDATE users SET username = ?, email = ?, nama = ?, asal_kota = ? WHERE id = ?');
    mysqli_stmt_bind_param($upd, 'ssssi', $username, $email, $nama, $asal_kota, $user_id);
    mysqli_stmt_execute($upd);
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() === 1062) {
        gagal('Username atau email sudah dipakai akun lain.');
    }
    throw $e;
}

// Perbarui session supaya sapaan di homepage ikut berubah
$_SESSION['username'] = $username;
$_SESSION['email']    = $email;
$_SESSION['nama']     = ($nama !== null) ? $nama : $username;
berhasil('Profil berhasil diperbarui!');
