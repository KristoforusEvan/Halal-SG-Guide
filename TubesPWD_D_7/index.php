<?php
session_start();
require 'koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = '!! Harap isi username dan password. !!';
    } else {
        $password_enkripsi = md5($password);
        $stmt = mysqli_prepare($conn, 'SELECT id, nama, username FROM users WHERE username = ? AND password = ?');
        mysqli_stmt_bind_param($stmt, 'ss', $username, $password_enkripsi);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['nama'] = $row['nama'];
            $_SESSION['username'] = $row['username'];
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
    <title>Halal SG Guide - Panduan Wisata Halal Singapura</title>
    <link rel="stylesheet" type="text/css" href="css/style.css" />
    <script type="text/javascript" src="js/validasi.js"></script>
</head>
<body>
    <div id="header">
        <h1>Halal SG Guide - Panduan Wisata Muslim Singapura</h1>
        <p>Informasi Kuliner Halal Terverifikasi MUIS &amp; Lokasi Masjid Terdekat</p>
    </div>

    <?php if (isset($_SESSION['user_id'])) { ?>
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
                    <td>Zam Zam Restaurant</td>
                    <td>Bugis MRT (North Bridge Rd)</td>
                    <td>Sertifikat Halal MUIS</td>
                    <td>Murtabak Rusa &amp; Nasi Briyani</td>
                </tr>
                <tr>
                    <td>Hajah Maimunah</td>
                    <td>Bugis / Jalan Pisang</td>
                    <td>Sertifikat Halal MUIS</td>
                    <td>Nasi Padang &amp; Tahu Telur</td>
                </tr>
                <tr>
                    <td>Encik Tan</td>
                    <td>Orchard / Bugis Junction</td>
                    <td>Sertifikat Halal MUIS</td>
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
                    <td>Masjid Sultan</td>
                    <td>Kampong Glam / Bugis</td>
                    <td>Bugis MRT (EW12 / DT14)</td>
                    <td>Ruang Sholat Luas, Tempat Wudhu Nyaman</td>
                </tr>
                <tr>
                    <td>Masjid Al-Falah</td>
                    <td>Orchard Road</td>
                    <td>Somerset MRT (NS23)</td>
                    <td>Akses Strategis di Pusat Belanja Orchard</td>
                </tr>
                <tr>
                    <td>Masjid Jamae (Chulia)</td>
                    <td>Chinatown</td>
                    <td>Chinatown MRT (NE4 / DT19)</td>
                    <td>Masjid Bersejarah di Tengah Chinatown</td>
                </tr>
            </table>
        </div>
    <?php } else { ?>
        <div class="container">
            <h2>Login Wisatawan</h2>
            <?php
            if ($error !== '') {
                echo '<p class="error">' . htmlspecialchars($error) . '</p>';
            }
            ?>
            <form action="index.php" method="post" onsubmit="return fn_ValLogin();">
                <table>
                    <tr>
                        <td style="width:30%;"><label for="username"><strong>Username</strong></label></td>
                        <td><input type="text" id="username" name="username" /></td>
                    </tr>
                    <tr>
                        <td><label for="password"><strong>Password</strong></label></td>
                        <td><input type="password" id="password" name="password" /></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align:center;">
                            <input type="submit" class="btn" value="Masuk" />
                        </td>
                    </tr>
                </table>
            </form>
            <p>Belum memiliki akun? <a href="register.php">Daftar pengguna baru di sini</a></p>
        </div>
    <?php } ?>
</body>
</html>