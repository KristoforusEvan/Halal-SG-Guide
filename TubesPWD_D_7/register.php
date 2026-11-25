<?php
session_start();
require 'koneksi.php';

$pesan = '';
$tipe = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $email === '' || $password === '') {
        $pesan = '!! Harap lengkapi username, email, dan password. !!';
        $tipe = 'error';
    } elseif (!preg_match("/^[a-zA-Z0-9_]+$/", $username)) {
        $pesan = '!! Username hanya boleh berisi huruf, angka, dan underscore (_). !!';
        $tipe = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $pesan = '!! Format email tidak valid. !!';
        $tipe = 'error';
    } elseif (strlen($password) < 8) {
        $pesan = '!! Password minimal 8 karakter. !!';
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
            $password_enkripsi = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, 'INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
            mysqli_stmt_bind_param($stmt, 'sss', $username, $email, $password_enkripsi);
            mysqli_stmt_execute($stmt);

            if (mysqli_stmt_affected_rows($stmt) === 1) {
                $pesan = 'Registrasi berhasil! Silakan login menggunakan email dan password Anda.';
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
    <link rel="stylesheet" type="text/css" href="css/style.css?v=5" />
    <script type="text/javascript" src="js/validasi.js?v=3"></script>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-left">
                <div class="brand-tag">HALAL SG GUIDE &bull; SINGAPORE</div>
                <div class="auth-center-text">
                    <p class="auth-arabic">Ahlan Wa Sahlan</p>
                    <h1 class="auth-title">JOIN WITH US</h1>
                    <div class="auth-divider"></div>
                    <p class="auth-desc">
                        Daftarkan akun wisatawan Anda untuk menyimpan daftar restoran halal favorit dan mengatur jadwal ibadah selama berwisata di Singapura.
                    </p>
                </div>
                <div class="auth-highlights">
                    <div class="highlight-item">
                        <strong>100% MUIS</strong>
                        Kuliner Terverifikasi
                    </div>
                    <div class="highlight-item">
                        <strong>70+ Masjid</strong>
                        Panduan Lokasi Sholat
                    </div>
                    <div class="highlight-item">
                        <strong>MRT Guide</strong>
                        Rute Ramah Muslim
                    </div>
                </div>
            </div>

            <div class="auth-right">
                <h2 class="form-heading">Create Account</h2>
                <p class="form-subtext">Buat akun baru cukup dengan username, email, dan password.</p>

                <div id="pesanError"></div>

                <?php
                if ($pesan !== '') {
                    echo '<p class="' . $tipe . '">' . htmlspecialchars($pesan) . '</p>';
                }
                ?>

                <form action="register.php" method="post" onsubmit="return fn_ValRegister();">
                    <div class="input-box">
                        <input type="text" id="username" name="username" placeholder="Username" />
                    </div>
                    <div class="input-box">
                        <input type="text" id="email" name="email" placeholder="Email (contoh: nama@gmail.com)" />
                    </div>
                    <div class="input-box">
                        <input type="password" id="password" name="password" placeholder="Password (minimal 8 karakter)" />
                    </div>
                    <div><input type="submit" class="btn-pill" value="Daftar Sekarang" /></div>
                </form>

                <div class="auth-footer">
                    Sudah punya akun? <a href="login.php">Login di sini</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>