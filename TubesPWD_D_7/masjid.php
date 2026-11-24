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

if (isset($_GET['hapus'])) {
    $id_hapus = $_GET['hapus'];
    if (ctype_digit($id_hapus)) {
        $stmtDel = mysqli_prepare($conn, 'DELETE FROM jadwal_masjid WHERE id = ? AND user_id = ?');
        mysqli_stmt_bind_param($stmtDel, 'ii', $id_hapus, $user_id);
        mysqli_stmt_execute($stmtDel);
        if (mysqli_stmt_affected_rows($stmtDel) === 1) {
            $pesan = 'Jadwal kunjungan masjid berhasil dihapus.';
            $tipe = 'sukses';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_edit = $_POST['id_edit'] ?? '';
    $nama_masjid = trim($_POST['nama_masjid'] ?? '');
    $area_singapura = trim($_POST['area_singapura'] ?? '');
    $waktu_sholat = trim($_POST['waktu_sholat'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');

    if ($nama_masjid === '' || $area_singapura === '' || $waktu_sholat === '' || $keterangan === '') {
        $pesan = '!! Harap lengkapi semua data jadwal masjid. !!';
        $tipe = 'error';
    } else {
        if ($id_edit !== '' && ctype_digit($id_edit)) {
            $stmtUpd = mysqli_prepare($conn, 'UPDATE jadwal_masjid SET nama_masjid = ?, area_singapura = ?, waktu_sholat = ?, keterangan = ? WHERE id = ? AND user_id = ?');
            mysqli_stmt_bind_param($stmtUpd, 'ssssii', $nama_masjid, $area_singapura, $waktu_sholat, $keterangan, $id_edit, $user_id);
            mysqli_stmt_execute($stmtUpd);
            $pesan = 'Jadwal kunjungan masjid berhasil diperbarui.';
            $tipe = 'sukses';
        } else {
            $stmtIns = mysqli_prepare($conn, 'INSERT INTO jadwal_masjid (user_id, nama_masjid, area_singapura, waktu_sholat, keterangan) VALUES (?, ?, ?, ?, ?)');
            mysqli_stmt_bind_param($stmtIns, 'issss', $user_id, $nama_masjid, $area_singapura, $waktu_sholat, $keterangan);
            mysqli_stmt_execute($stmtIns);
            $pesan = 'Jadwal kunjungan masjid berhasil ditambahkan.';
            $tipe = 'sukses';
        }
    }
}

$editData = null;
if (isset($_GET['edit']) && ctype_digit($_GET['edit'])) {
    $id_e = $_GET['edit'];
    $qEdit = mysqli_prepare($conn, 'SELECT * FROM jadwal_masjid WHERE id = ? AND user_id = ?');
    mysqli_stmt_bind_param($qEdit, 'ii', $id_e, $user_id);
    mysqli_stmt_execute($qEdit);
    $resEdit = mysqli_stmt_get_result($qEdit);
    $editData = mysqli_fetch_assoc($resEdit);
}

$qList = mysqli_prepare($conn, 'SELECT * FROM jadwal_masjid WHERE user_id = ? ORDER BY id DESC');
mysqli_stmt_bind_param($qList, 'i', $user_id);
mysqli_stmt_execute($qList);
$resultList = mysqli_stmt_get_result($qList);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="id" lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Cari Masjid &amp; Jadwal Ibadah - Halal SG Guide</title>
    <link rel="stylesheet" type="text/css" href="css/style.css" />
    <script type="text/javascript" src="js/validasi.js"></script>
</head>
<body>
    <div id="header">
        <h1>Halal SG Guide - Jadwal Kunjungan Masjid &amp; Ibadah</h1>
        <p>Atur jadwal sholat dan rute masjid terdekat di kawasan wisata Singapura</p>
    </div>
    <div id="nav">
        <a href="index.php">Homepage</a>
        <a href="makanan.php">Cari Makanan Halal (CRUD 1)</a>
        <a href="masjid.php">Cari Masjid &amp; Jadwal Ibadah (CRUD 2)</a>
        <a href="profil.php">Profil Saya</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="container">
        <h2><?php echo ($editData !== null) ? 'Edit Jadwal Kunjungan Masjid' : 'Tambah Jadwal Kunjungan Masjid'; ?></h2>
        <?php
        if ($pesan !== '') {
            echo '<p class="' . $tipe . '">' . htmlspecialchars($pesan) . '</p>';
        }
        ?>
        <div class="card-form">
            <form action="masjid.php" method="post" onsubmit="return fn_ValMasjid();">
                <input type="hidden" name="id_edit" value="<?php echo ($editData !== null) ? $editData['id'] : ''; ?>" />
                <div class="form-group">
                    <label for="nama_masjid">Nama Masjid / Musholla</label>
                    <input type="text" id="nama_masjid" name="nama_masjid" value="<?php echo ($editData !== null) ? htmlspecialchars($editData['nama_masjid']) : ''; ?>" />
                </div>
                <div class="form-group">
                    <label for="area_singapura">Area / Stasiun MRT</label>
                    <input type="text" id="area_singapura" name="area_singapura" value="<?php echo ($editData !== null) ? htmlspecialchars($editData['area_singapura']) : ''; ?>" />
                </div>
                <div class="form-group">
                    <label for="waktu_sholat">Rencana Waktu Sholat</label>
                    <select id="waktu_sholat" name="waktu_sholat">
                        <option value="Dzuhur &amp; Ashar (Jamak)">Dzuhur &amp; Ashar (Jamak)</option>
                        <option value="Maghrib &amp; Isya (Jamak)">Maghrib &amp; Isya (Jamak)</option>
                        <option value="Sholat Jumat">Sholat Jumat</option>
                        <option value="Subuh">Subuh</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="keterangan">Keterangan Rute / Akses</label>
                    <textarea id="keterangan" name="keterangan" rows="3" cols="30"><?php echo ($editData !== null) ? htmlspecialchars($editData['keterangan']) : ''; ?></textarea>
                </div>
                <input type="submit" class="btn" value="Simpan Jadwal Ibadah" />
            </form>
        </div>

        <h2>Daftar Rencana Kunjungan Masjid Anda</h2>
        <table>
            <tr>
                <th>No</th>
                <th>Nama Masjid</th>
                <th>Area / MRT</th>
                <th>Waktu Sholat</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
            <?php
            $no = 1;
            while ($row = mysqli_fetch_assoc($resultList)) {
                echo '<tr>';
                echo '<td>' . $no . '</td>';
                echo '<td><strong>' . htmlspecialchars($row['nama_masjid']) . '</strong></td>';
                echo '<td>' . htmlspecialchars($row['area_singapura']) . '</td>';
                echo '<td><span class="badge-halal">' . htmlspecialchars($row['waktu_sholat']) . '</span></td>';
                echo '<td>' . htmlspecialchars($row['keterangan']) . '</td>';
                echo '<td><a class="aksi-link" href="masjid.php?edit=' . $row['id'] . '">Edit</a> | <a class="aksi-hapus" href="masjid.php?hapus=' . $row['id'] . '">Hapus</a></td>';
                echo '</tr>';
                $no++;
            }
            ?>
        </table>
        <p><strong>Jumlah jadwal masjid:</strong> <?php echo mysqli_num_rows($resultList); ?></p>
    </div>
</body>
</html>