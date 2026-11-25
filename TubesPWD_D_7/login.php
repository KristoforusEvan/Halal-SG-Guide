<?php
session_start();
require 'koneksi.php';

// Kalau sudah login, tidak perlu lihat halaman login lagi
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = '!! Harap isi username dan password. !!';
    } else {
        // Ambil user berdasarkan username SAJA, lalu cek password dengan password_verify
        $stmt = mysqli_prepare($conn, 'SELECT id, nama, username, password FROM users WHERE username = ?');
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        if ($row && password_verify($password, $row['password'])) {
            session_regenerate_id(true); // cegah session fixation
            $_SESSION['user_id']  = $row['id'];
            $_SESSION['nama']     = $row['nama'];
            $_SESSION['username'] = $row['username'];
            header('Location: index.php');
            exit;
        } else {
            $error = '!! Username atau password salah. !!';
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
    <link rel="stylesheet" type="text/css" href="css/style.css?v=4" />
    <script type="text/javascript" src="js/validasi.js"></script>
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
                <p class="form-subtext">Masuk menggunakan akun wisatawan Anda untuk mengelola rencana kuliner dan jadwal ibadah.</p>

                <!-- Wadah pesan error dari JavaScript (validasi.js) -->
                <div id="pesanError"></div>

                <?php
                // Pesan error dari PHP (misal password salah)
                if ($error !== '') {
                    echo '<p class="error">' . htmlspecialchars($error) . '</p>';
                }
                ?>

                <form action="login.php" method="post" onsubmit="return fn_ValLogin();">
                    <div class="input-box">
                        <input type="text" id="username" name="username" placeholder="Username" />
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