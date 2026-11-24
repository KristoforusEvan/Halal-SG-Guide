<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$pesan = '';
$tipe = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $asal_kota = trim($_POST['asal_kota'] ?? '');

    if ($nama === '' || $email === '' || $asal_kota === '') {
        $pesan = '!! Semua kolom profil wajib diisi. !!';
        $tipe = 'error';
    } else {
        $stmt = mysqli_prepare($conn, 'UPDATE users SET nama = ?, email = ?, asal_kota = ? WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'sssi', $nama, $email, $asal_kota, $user_id);
        mysqli_stmt_execute($stmt);

        $_SESSION['nama'] = $nama;
        $pesan = 'Profil berhasil diperbarui!';
        $tipe = 'sukses';
    }
}

$qUser = mysqli_prepare($conn, 'SELECT nama, username, email, asal_kota FROM users WHERE id = ?');
mysqli_stmt_bind_param($qUser, 'i', $user_id);
mysqli_stmt_execute($qUser);
$resUser = mysqli_stmt_get_result($qUser);
$dataUser = mysqli_fetch_assoc($resUser);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="id" lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Profil Pengguna - Halal SG Guide</title>
    <link rel="stylesheet" type="text/css" href="css/style.css" />
</head>
<body>
    <div id="header">
        <h1>Halal SG Guide - Profil Wisatawan</h1>
        <p>Kelola informasi pribadi akun wisata halal Anda</p>
    </div>
    <div id="nav">
        <a href="index.php">Homepage</a>
        <a href="makanan.php">Cari Makanan Halal (CRUD 1)</a>
        <a href="masjid.php">Cari Masjid &amp; Jadwal Ibadah (CRUD 2)</a>
        <a href="profil.php">Profil Saya</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="container">
        <h2>Lihat &amp; Update Profil Pengguna</h2>
        <?php
        if ($pesan !== '') {
            echo '<p class="' . $tipe . '">' . htmlspecialchars($pesan) . '</p>';
        }
        ?>
        <div class="card-form">
            <form action="profil.php" method="post">
                <div class="form-group">
                    <label>Username (Tidak dapat diubah)</label>
                    <input type="text" value="<?php echo htmlspecialchars($dataUser['username']); ?>" readonly="readonly" />
                </div>
                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($dataUser['nama']); ?>" />
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($dataUser['email']); ?>" />
                </div>
                <div class="form-group">
                    <label for="asal_kota">Asal Kota</label>
                    <input type="text" id="asal_kota" name="asal_kota" value="<?php echo htmlspecialchars($dataUser['asal_kota']); ?>" />
                </div>
                <input type="submit" class="btn" value="Simpan Perubahan Profil" />
            </form>
        </div>
    </div>
</body>
</html>