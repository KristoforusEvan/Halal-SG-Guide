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
        $stmtDel = mysqli_prepare($conn, 'DELETE FROM rencana_makanan WHERE id = ? AND user_id = ?');
        mysqli_stmt_bind_param($stmtDel, 'ii', $id_hapus, $user_id);
        mysqli_stmt_execute($stmtDel);
        if (mysqli_stmt_affected_rows($stmtDel) === 1) {
            $pesan = 'Data rencana kuliner berhasil dihapus.';
            $tipe = 'sukses';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_edit = $_POST['id_edit'] ?? '';
    $nama_tempat = trim($_POST['nama_tempat'] ?? '');
    $lokasi_mrt = trim($_POST['lokasi_mrt'] ?? '');
    $status_halal = trim($_POST['status_halal'] ?? '');
    $catatan = trim($_POST['catatan'] ?? '');

    if ($nama_tempat === '' || $lokasi_mrt === '' || $status_halal === '' || $catatan === '') {
        $pesan = '!! Harap lengkapi semua data kuliner halal. !!';
        $tipe = 'error';
    } else {
        if ($id_edit !== '' && ctype_digit($id_edit)) {
            $stmtUpd = mysqli_prepare($conn, 'UPDATE rencana_makanan SET nama_tempat = ?, lokasi_mrt = ?, status_halal = ?, catatan = ? WHERE id = ? AND user_id = ?');
            mysqli_stmt_bind_param($stmtUpd, 'ssssii', $nama_tempat, $lokasi_mrt, $status_halal, $catatan, $id_edit, $user_id);
            mysqli_stmt_execute($stmtUpd);
            $pesan = 'Rencana kuliner berhasil diperbarui.';
            $tipe = 'sukses';
        } else {
            $stmtIns = mysqli_prepare($conn, 'INSERT INTO rencana_makanan (user_id, nama_tempat, lokasi_mrt, status_halal, catatan) VALUES (?, ?, ?, ?, ?)');
            mysqli_stmt_bind_param($stmtIns, 'issss', $user_id, $nama_tempat, $lokasi_mrt, $status_halal, $catatan);
            mysqli_stmt_execute($stmtIns);
            $pesan = 'Rencana kuliner halal berhasil ditambahkan.';
            $tipe = 'sukses';
        }
    }
}

$editData = null;
if (isset($_GET['edit']) && ctype_digit($_GET['edit'])) {
    $id_e = $_GET['edit'];
    $qEdit = mysqli_prepare($conn, 'SELECT * FROM rencana_makanan WHERE id = ? AND user_id = ?');
    mysqli_stmt_bind_param($qEdit, 'ii', $id_e, $user_id);
    mysqli_stmt_execute($qEdit);
    $resEdit = mysqli_stmt_get_result($qEdit);
    $editData = mysqli_fetch_assoc($resEdit);
}

$qList = mysqli_prepare($conn, 'SELECT * FROM rencana_makanan WHERE user_id = ? ORDER BY id DESC');
mysqli_stmt_bind_param($qList, 'i', $user_id);
mysqli_stmt_execute($qList);
$resultList = mysqli_stmt_get_result($qList);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="id" lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Cari Makanan Halal - Halal SG Guide</title>
    <link rel="stylesheet" type="text/css" href="css/style.css" />
    <script type="text/javascript" src="js/validasi.js"></script>
</head>
<body>
    <div id="header">
        <h1>Halal SG Guide - Rencana Wisata Kuliner Halal</h1>
        <p>Catat dan verifikasi restoran halal selama perjalanan Anda di Singapura</p>
    </div>
    <div id="nav">
        <a href="index.php">Homepage</a>
        <a href="makanan.php">Cari Makanan Halal (CRUD 1)</a>
        <a href="masjid.php">Cari Masjid &amp; Jadwal Ibadah (CRUD 2)</a>
        <a href="profil.php">Profil Saya</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="container">
        <h2><?php echo ($editData !== null) ? 'Edit Rencana Kuliner Halal' : 'Tambah Rencana Kuliner Halal'; ?></h2>
        <?php
        if ($pesan !== '') {
            echo '<p class="' . $tipe . '">' . htmlspecialchars($pesan) . '</p>';
        }
        ?>
        <div class="card-form">
            <form action="makanan.php" method="post" onsubmit="return fn_ValMakanan();">
                <input type="hidden" name="id_edit" value="<?php echo ($editData !== null) ? $editData['id'] : ''; ?>" />
                <div class="form-group">
                    <label for="nama_tempat">Nama Restoran / Kedai</label>
                    <input type="text" id="nama_tempat" name="nama_tempat" value="<?php echo ($editData !== null) ? htmlspecialchars($editData['nama_tempat']) : ''; ?>" />
                </div>
                <div class="form-group">
                    <label for="lokasi_mrt">Lokasi / Stasiun MRT Terdekat</label>
                    <input type="text" id="lokasi_mrt" name="lokasi_mrt" value="<?php echo ($editData !== null) ? htmlspecialchars($editData['lokasi_mrt']) : ''; ?>" />
                </div>
                <div class="form-group">
                    <label for="status_halal">Kategori Verifikasi Halal</label>
                    <select id="status_halal" name="status_halal">
                        <option value="Sertifikat Halal MUIS">Sertifikat Halal MUIS</option>
                        <option value="Muslim-Owned (Pemilik Muslim)">Muslim-Owned (Pemilik Muslim)</option>
                        <option value="No Pork No Lard">No Pork No Lard</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="catatan">Catatan Menu &amp; Sumber Verifikasi</label>
                    <textarea id="catatan" name="catatan" rows="3" cols="30"><?php echo ($editData !== null) ? htmlspecialchars($editData['catatan']) : ''; ?></textarea>
                </div>
                <input type="submit" class="btn" value="Simpan Rencana Kuliner" />
            </form>
        </div>

        <h2>Daftar Rencana Wisata Kuliner Anda</h2>
        <table>
            <tr>
                <th>No</th>
                <th>Nama Tempat</th>
                <th>Lokasi / MRT</th>
                <th>Status Halal</th>
                <th>Catatan</th>
                <th>Aksi</th>
            </tr>
            <?php
            $no = 1;
            while ($row = mysqli_fetch_assoc($resultList)) {
                echo '<tr>';
                echo '<td>' . $no . '</td>';
                echo '<td><strong>' . htmlspecialchars($row['nama_tempat']) . '</strong></td>';
                echo '<td>' . htmlspecialchars($row['lokasi_mrt']) . '</td>';
                echo '<td><span class="badge-halal">' . htmlspecialchars($row['status_halal']) . '</span></td>';
                echo '<td>' . htmlspecialchars($row['catatan']) . '</td>';
                echo '<td><a class="aksi-link" href="makanan.php?edit=' . $row['id'] . '">Edit</a> | <a class="aksi-hapus" href="makanan.php?hapus=' . $row['id'] . '">Hapus</a></td>';
                echo '</tr>';
                $no++;
            }
            ?>
        </table>
        <p><strong>Jumlah rencana kuliner:</strong> <?php echo mysqli_num_rows($resultList); ?></p>
    </div>
</body>
</html>
