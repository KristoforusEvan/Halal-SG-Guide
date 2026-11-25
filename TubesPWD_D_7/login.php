<?php
session_start();
require 'koneksi.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = '!! Harap isi email dan password. !!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = '!! Format email tidak valid. !!';
    } else {
        $stmt = mysqli_prepare($conn, 'SELECT id, username, email, nama, password FROM users WHERE email = ?');
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        if ($row && password_verify($password, $row['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']  = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['email']    = $row['email'];
            $_SESSION['nama']     = !empty($row['nama']) ? $row['nama'] : $row['username'];
            header('Location: index.php');
            exit;
        } else {
            $error = '!! Email atau password salah. !!';
        }
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="id" lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Login - Halal SG Guide</title>
    <link rel="stylesheet" type="text/css" href="css/style.css?v=5" />
    <script type="text/javascript" src="js/validasi.js?v=2"></script>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-left">
                <div class="brand-tag">HALAL SG GUIDE &bull; SINGAPORE</div>
                <div class="auth-center-text">
                    <p class="auth-arabic">Assalamu'alaikum</p>
                    <h1 class="auth-title">WELCOME BACK</h1>
                    <div class="auth-divider"></div>
                    <p class="auth-desc">
                        Jelajahi keindahan Singapura dengan tenang. Temukan kuliner bersertifikat halal MUIS dan lokasi masjid terdekat dalam satu panduan terpercaya.
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
                <h2 class="form-heading">Login Account</h2>
                <p class="form-subtext">Masuk menggunakan email dan password Anda untuk mengelola rencana kuliner dan jadwal ibadah.</p>

                <div id="pesanError"></div>

                <?php
                if ($error !== '') {
                    echo '<p class="error">' . htmlspecialchars($error) . '</p>';
                }
                ?>

                <form action="login.php" method="post" onsubmit="return fn_ValLogin();">
                    <div class="input-box">
                        <input type="text" id="email" name="email" placeholder="Email (contoh: nama@gmail.com)" />
                    </div>
                    <div class="input-box">
                        <input type="password" id="password" name="password" placeholder="Password" />
                    </div>
                    <div><input type="submit" class="btn-pill" value="Login Sekarang" /></div>
                </form>

                <div class="auth-footer">
                    Belum memiliki akun? <a href="register.php">Daftar di sini</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>