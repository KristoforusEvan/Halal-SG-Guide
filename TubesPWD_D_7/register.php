<?php
session_start();
require_once 'koneksi.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$pesan = '';
$tipe  = '';
$nama  = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama'] ?? '');
    $email    = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if ($nama === '' || $email === '' || $password === '') {
        $pesan = 'Harap lengkapi semua data registrasi (Nama, Email, dan Password).';
        $tipe  = 'error';
    } elseif (!preg_match("/^[a-zA-Z\s'.]+$/", $nama)) {
        $pesan = 'Nama hanya boleh mengandung huruf, spasi, titik, dan petik tunggal.';
        $tipe  = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $pesan = 'Format alamat email tidak valid.';
        $tipe  = 'error';
    } elseif (strlen($password) < 6) {
        $pesan = 'Password minimal harus 6 karakter.';
        $tipe  = 'error';
    } else {
        // Mengecek apakah email sudah terdaftar di database
        $cek = mysqli_prepare($conn, 'SELECT id FROM users WHERE email = ? LIMIT 1');
        mysqli_stmt_bind_param($cek, 's', $email);
        mysqli_stmt_execute($cek);
        $resCek = mysqli_stmt_get_result($cek);

        if (mysqli_num_rows($resCek) > 0) {
            $pesan = 'Email sudah terdaftar. Silakan gunakan email lain atau langsung login.';
            $tipe  = 'error';
            mysqli_stmt_close($cek);
        } else {
            mysqli_stmt_close($cek);

            // Enkripsi password dengan password_hash() agar cocok dengan password_verify() di index.php
            $password_enkripsi = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, 'INSERT INTO users (nama, email, password) VALUES (?, ?, ?)');
            mysqli_stmt_bind_param($stmt, 'sss', $nama, $email, $password_enkripsi);
            mysqli_stmt_execute($stmt);

            if (mysqli_stmt_affected_rows($stmt) === 1) {
                $pesan = 'Registrasi berhasil! Silakan login menggunakan akun Anda.';
                $tipe  = 'sukses';
                // Kosongkan form setelah berhasil mendaftar
                $nama  = '';
                $email = '';
            } else {
                $pesan = 'Gagal menyimpan data registrasi. Silakan coba lagi.';
                $tipe  = 'error';
            }
            mysqli_stmt_close($stmt);
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

                <div id="pesanError">
                    <?php
                    if ($pesan !== '') {
                        echo '<p class="' . $tipe . '">' . htmlspecialchars($pesan) . '</p>';
                    }
                    ?>
                </div>

                <form action="register.php" method="post" onsubmit="return fn_ValRegister();">
                    <div class="input-box">
                        <input type="text" id="nama" name="nama" placeholder="Nama Lengkap (Huruf &amp; Spasi)"
                               value="<?php echo htmlspecialchars($nama); ?>" />
                    </div>
                    <div class="input-box">
                        <input type="text" id="email" name="email" placeholder="Email (contoh: nama@gmail.com)"
                               value="<?php echo htmlspecialchars($email); ?>" />
                    </div>
                    <div class="input-box">
                        <input type="password" id="password" name="password" placeholder="Password" />
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