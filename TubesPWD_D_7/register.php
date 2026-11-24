<?php
session_start();
require 'koneksi.php';

$pesan = '';
$tipe = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $asal_kota = trim($_POST['asal_kota'] ?? '');

    if ($nama === '' || $username === '' || $email === '' || $password === '' || $asal_kota === '') {
        $pesan = '!! Harap lengkapi semua data registrasi. !!';
        $tipe = 'error';
    } elseif (!preg_match("/^[a-zA-Z ]*$/", $nama)) {
        $pesan = '!! Nama hanya boleh mengandung huruf dan spasi. !!';
        $tipe = 'error';
    } else {
        $cek = mysqli_prepare($conn, 'SELECT id FROM users WHERE username = ? OR email = ?');
        mysqli_stmt_bind_param($cek, 'ss', $username, $email);
        mysqli_stmt_execute($cek);
        $resCek = mysqli_stmt_get_result($cek);

        if (mysqli_num_rows($resCek) > 0) {
            $pesan = '!! Username atau Email sudah terdaftar. Silakan gunakan yang lain. !!';
            $tipe = 'error';
        } else {
            $password_enkripsi = md5($password);
            $stmt = mysqli_prepare($conn, 'INSERT INTO users (nama, username, email, password, asal_kota) VALUES (?, ?, ?, ?, ?)');
            mysqli_stmt_bind_param($stmt, 'sssss', $nama, $username, $email, $password_enkripsi, $asal_kota);
            mysqli_stmt_execute($stmt);

            if (mysqli_stmt_affected_rows($stmt) === 1) {
                $pesan = 'Registrasi berhasil! Silakan login menggunakan akun Anda.';
                $tipe = 'sukses';
            } else {
                $pesan = '!! Gagal menyimpan data registrasi. !!';
                $tipe = 'error';
            }
        }
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="id" lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Registrasi - Panduan Wisata Halal Singapura</title>
    <link rel="stylesheet" type="text/css" href="css/style.css" />
    <script type="text/javascript" src="js/validasi.js"></script>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-left">
                <div class="brand-tag">HALAL SG GUIDE</div>
                <p class="auth-sub">Start your halal journey</p>
                <h1 class="auth-title">JOIN WITH US</h1>
                <div class="auth-divider"></div>
                <p class="auth-desc">
                    Buat akun baru untuk mencatat rencana destinasi kuliner halal dan jadwal sholat selama berada di Singapura.
                </p>
            </div>

            <div class="auth-right">
                <h2 class="form-heading">Create Account</h2>
                <p class="form-subtext">Lengkapi data di bawah ini untuk mendaftar sebagai wisatawan baru.</p>

                <?php
                if ($pesan !== '') {
                    echo '<p class="' . $tipe . '">' . htmlspecialchars($pesan) . '</p>';
                }
                ?>

                <form action="register.php" method="post" onsubmit="return fn_ValRegister();">
                    <div class="input-box">
                        <input type="text" id="nama" name="nama" placeholder="Nama Lengkap (Huruf &amp; Spasi)" />
                    </div>
                    <div class="input-box">
                        <input type="text" id="username" name="username" placeholder="Username" />
                    </div>
                    <div class="input-box">
                        <input type="text" id="email" name="email" placeholder="Email (contoh: nama@gmail.com)" />
                    </div>
                    <div class="input-box">
                        <input type="password" id="password" name="password" placeholder="Password" />
                    </div>
                    <div class="input-box">
                        <input type="text" id="asal_kota" name="asal_kota" placeholder="Asal Kota (Indonesia)" />
                    </div>
                    <input type="submit" class="btn-pill" value="Daftar Sekarang" />
                </form>

                <div class="auth-footer">
                    Sudah punya akun? <a href="index.php">Login di sini</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>