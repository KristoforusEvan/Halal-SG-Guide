<?php
session_start();
require 'koneksi.php';

// Ambil input pencarian dari URL (metode GET)
$keyword  = trim($_GET['q'] ?? '');
$kategori = $_GET['kategori'] ?? '';

// Whitelist kategori: hanya nilai ini yang boleh dipakai di query
$kategoriValid = array('kuliner', 'masjid', 'wisata');

// Bangun query bertahap sesuai filter yang diisi
$sql    = 'SELECT nama, kategori, area_mrt, keterangan, status FROM tempat WHERE 1=1';
$tipe   = '';
$params = array();

if ($keyword !== '') {
    $sql     .= ' AND (nama LIKE ? OR area_mrt LIKE ?)';
    $like     = '%' . $keyword . '%';
    $tipe    .= 'ss';
    $params[] = $like;
    $params[] = $like;
}
if (in_array($kategori, $kategoriValid, true)) {
    $sql     .= ' AND kategori = ?';
    $tipe    .= 's';
    $params[] = $kategori;
}
$sql .= ' ORDER BY kategori, nama LIMIT 30';

$stmt = mysqli_prepare($conn, $sql);
if ($tipe !== '') {
    // Tanda ... memecah array $params jadi argumen terpisah
    mysqli_stmt_bind_param($stmt, $tipe, ...$params);
}
mysqli_stmt_execute($stmt);
$hasil  = mysqli_stmt_get_result($stmt);
$jumlah = mysqli_num_rows($hasil);

$sudahLogin = isset($_SESSION['user_id']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="id" lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Halal SG Guide - Panduan Wisata Halal Singapura</title>
    <link rel="stylesheet" type="text/css" href="css/style.css?v=4" />
</head>
<body>
    <div id="header">
        <h1>Halal SG Guide</h1>
        <p>Cari kuliner halal dan masjid terdekat selama liburan di Singapura</p>
    </div>

    <!-- Navbar berubah sesuai status login -->
    <div id="nav">
        <a href="index.php">Homepage</a>
        <?php if ($sudahLogin) { ?>
            <a href="makanan.php">Rencana Kuliner</a>
            <a href="masjid.php">Jadwal Masjid</a>
            <a href="profil.php">Profil Saya</a>
            <a href="logout.php">Logout</a>
        <?php } else { ?>
            <a href="login.php">Login</a>
            <a href="register.php">Daftar</a>
        <?php } ?>
    </div>

    <div class="container">
        <?php if ($sudahLogin) { ?>
            <p>Halo, <strong><?php echo htmlspecialchars($_SESSION['nama']); ?></strong>!</p>
        <?php } ?>

        <!-- Form pencarian -->
        <div class="card-form">
            <form action="index.php" method="get">
                <div class="form-group">
                    <label for="q">Cari nama tempat / stasiun MRT</label>
                    <input type="text" id="q" name="q" value="<?php echo htmlspecialchars($keyword); ?>" />
                </div>
                <div class="form-group">
                    <label for="kategori">Kategori</label>
                    <select id="kategori" name="kategori">
                        <option value="">Semua</option>
                        <option value="kuliner"<?php if ($kategori === 'kuliner') echo ' selected="selected"'; ?>>Kuliner Halal</option>
                        <option value="masjid"<?php if ($kategori === 'masjid') echo ' selected="selected"'; ?>>Masjid</option>
                        <option value="wisata"<?php if ($kategori === 'wisata') echo ' selected="selected"'; ?>>Wisata</option>
                    </select>
                </div>
                <div><input type="submit" class="btn" value="Cari" /></div>
            </form>
        </div>

        <h2>Hasil (<?php echo $jumlah; ?> tempat)</h2>

        <!-- Daftar tempat dalam bentuk kartu -->
        <div class="grid-tempat">
            <?php while ($row = mysqli_fetch_assoc($hasil)) { ?>
            <div class="kartu-tempat">
                <div class="kartu-gambar kat-<?php echo htmlspecialchars($row['kategori']); ?>"></div>
                <div class="kartu-isi">
                    <span class="badge-halal"><?php echo htmlspecialchars($row['status']); ?></span>
                    <h3><?php echo htmlspecialchars($row['nama']); ?></h3>
                    <p class="kartu-mrt"><?php echo htmlspecialchars($row['area_mrt']); ?></p>
                    <p><?php echo htmlspecialchars($row['keterangan']); ?></p>
                </div>
            </div>
            <?php } ?>
        </div>

        <?php if ($jumlah === 0) { ?>
            <p class="error">Tidak ada tempat yang cocok. Coba kata kunci lain.</p>
        <?php } ?>
    </div>
</body>
</html>