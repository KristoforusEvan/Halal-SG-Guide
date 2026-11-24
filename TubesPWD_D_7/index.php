<?php
session_start();
require_once 'koneksi.php';

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Harap isi email dan password terlebih dahulu.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format alamat email tidak valid.';
    } else {
        $stmt = mysqli_prepare($conn, 'SELECT id, nama, email, password FROM users WHERE email = ? LIMIT 1');
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $row['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['nama']    = $row['nama'];
                $_SESSION['email']   = $row['email'];

                mysqli_stmt_close($stmt);
                mysqli_close($conn);

                header('Location: index.php');
                exit();
            } else {
                $error = 'Email atau password yang Anda masukkan salah.';
            }
        } else {
            $error = 'Email atau password yang Anda masukkan salah.';
        }

        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="id" lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Halal SG Guide - Panduan Wisata Halal Singapura</title>
    <link rel="stylesheet" type="text/css" href="css/style.css" />
    <script type="text/javascript" src="js/validasi.js"></script>
</head>
<body>
    <?php if (isset($_SESSION['user_id'])) { ?>
        <div id="header">
            <h1>Halal SG Guide - Panduan Wisata Muslim Singapura</h1>
            <p>Informasi Kuliner Halal Terverifikasi MUIS &amp; Lokasi Masjid Terdekat</p>
        </div>

        <div id="nav">
            <a href="index.php">Homepage</a>
            <a href="makanan.php">Cari Makanan Halal (CRUD 1)</a>
            <a href="masjid.php">Cari Masjid &amp; Jadwal Ibadah (CRUD 2)</a>
            <a href="profil.php">Profil Saya</a>
            <a href="logout.php">Logout</a>
        </div>

        <div class="container">
            <h2>Selamat Datang, <?php echo htmlspecialchars($_SESSION['nama']); ?>!</h2>
            <p>Rencanakan perjalanan nyaman Anda di Singapura dengan panduan sumber terverifikasi resmi.</p>

            <h3>Rekomendasi Kuliner Bersertifikat Halal (Sumber: MUIS Singapore)</h3>
            <table>
                <tr>
                    <th>Nama Restoran</th>
                    <th>Area / MRT Terdekat</th>
                    <th>Status Verifikasi</th>
                    <th>Menu Andalan</th>
                </tr>
                <tr>
                    <td><strong>Zam Zam Restaurant</strong></td>
                    <td>Bugis MRT (North Bridge Rd)</td>
                    <td><span class="badge-halal">Sertifikat Halal MUIS</span></td>
                    <td>Murtabak Rusa &amp; Nasi Briyani</td>
                </tr>
                <tr>
                    <td><strong>Hajah Maimunah</strong></td>
                    <td>Bugis / Jalan Pisang</td>
                    <td><span class="badge-halal">Sertifikat Halal MUIS</span></td>
                    <td>Nasi Padang &amp; Tahu Telur</td>
                </tr>
                <tr>
                    <td><strong>Encik Tan</strong></td>
                    <td>Orchard / Bugis Junction</td>
                    <td><span class="badge-halal">Sertifikat Halal MUIS</span></td>
                    <td>Laksa &amp; Hainanese Chicken Rice</td>
                </tr>
            </table>

            <h3>Rekomendasi Masjid Utama di Kawasan Wisata</h3>
            <table>
                <tr>
                    <th>Nama Masjid</th>
                    <th>Kawasan Wisata</th>
                    <th>MRT Terdekat</th>
                    <th>Fasilitas</th>
                </tr>
                <tr>
                    <td><strong>Masjid Sultan</strong></td>
                    <td>Kampong Glam / Bugis</td>
                    <td>Bugis MRT (EW12 / DT14)</td>
                    <td>Ruang Sholat Luas, Tempat Wudhu Nyaman</td>
                </tr>
                <tr>
                    <td><strong>Masjid Al-Falah</strong></td>
                    <td>Orchard Road</td>
                    <td>Somerset MRT (NS23)</td>
                    <td>Akses Strategis di Pusat Belanja Orchard</td>
                </tr>
                <tr>
                    <td><strong>Masjid Jamae (Chulia)</strong></td>
                    <td>Chinatown</td>
                    <td>Chinatown MRT (NE4 / DT19)</td>
                    <td>Masjid Bersejarah di Tengah Chinatown</td>
                </tr>
            </table>
        </div>
    <?php } else { ?>
        <div class="auth-wrapper">
            <div class="auth-card">
                <div class="auth-left">
                    <div class="brand-tag">HALAL SG GUIDE</div>
                    <p class="auth-sub">Nice to see you again</p>
                    <h1 class="auth-title">WELCOME BACK</h1>
                    <div class="auth-divider"></div>
                    <p class="auth-desc">
                        Temukan informasi kuliner bersertifikat halal MUIS dan lokasi masjid terdekat di Singapura dalam satu platform terverifikasi.
                    </p>
                </div>

                <div class="auth-right">
                    <h2 class="form-heading">Login Account</h2>
                    <p class="form-subtext">Masuk menggunakan akun wisatawan Anda untuk mengelola rencana kuliner dan jadwal ibadah.</p>

                    <div id="pesanError">
                        <?php
                        if ($error !== '') {
                            echo '<p class="error">' . htmlspecialchars($error) . '</p>';
                        }
                        ?>
                    </div>

                    <form action="index.php" method="post" onsubmit="return fn_ValLogin();">
                        <div class="input-box">
                            <input type="text" id="email" name="email" placeholder="Email"
                                   value="<?php echo htmlspecialchars($email); ?>" />
                        </div>
                        <div class="input-box">
                            <input type="password" id="password" name="password" placeholder="Password" />
                        </div>
                        <input type="submit" class="btn-pill" value="Login Sekarang" />
                    </form>

                    <div class="auth-footer">
                        Belum memiliki akun? <a href="register.php">Daftar di sini</a>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</body>
</html>