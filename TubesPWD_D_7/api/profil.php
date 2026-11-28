<?php
// profil.php
//   GET  -> Ambil data profil user yang sedang login
//   POST -> Perbarui username, email, nama, asal_kota, dan upload foto_profil
require 'fungsi.php';
require 'koneksi.php';

wajibLogin();
$userId = (int) $_SESSION['user_id'];

// ---------- GET: AMBIL DATA PROFIL ----------
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = mysqli_prepare($conn, 'SELECT id, username, email, nama, asal_kota, role, foto_profil AS foto FROM users WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$row) {
        gagal('Data pengguna tidak ditemukan.', 404);
    }

    $row['foto'] = ($row['foto'] !== null && trim($row['foto']) !== '') ? basename($row['foto']) : 'bg-singapore.jpg';
    berhasil('', array('data' => $row));
}

// ---------- POST: UPDATE PROFIL & UPLOAD FOTO ----------
wajibPost();

$username = ambilPost('username');
$email    = ambilPost('email');
$nama     = ambilPost('nama');
$asalKota = ambilPost('asal_kota');

if (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
    gagal('Username harus 3-30 karakter (huruf, angka, atau underscore).');
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
    gagal('Format email tidak valid.');
}
if (strlen($nama) > 100 || strlen($asalKota) > 100) {
    gagal('Nama lengkap dan asal kota maksimal 100 karakter.');
}

// Ambil foto lama dari database
$qLama = mysqli_prepare($conn, 'SELECT foto_profil FROM users WHERE id = ?');
mysqli_stmt_bind_param($qLama, 'i', $userId);
mysqli_stmt_execute($qLama);
$dataLama = mysqli_fetch_assoc(mysqli_stmt_get_result($qLama));
$fotoAktif = ($dataLama && !empty($dataLama['foto_profil'])) ? basename($dataLama['foto_profil']) : 'bg-singapore.jpg';

// Cek duplikasi username / email milik user lain
$cek = mysqli_prepare($conn, 'SELECT id FROM users WHERE (username = ? OR email = ?) AND id <> ?');
mysqli_stmt_bind_param($cek, 'ssi', $username, $email, $userId);
mysqli_stmt_execute($cek);
if (mysqli_fetch_assoc(mysqli_stmt_get_result($cek))) {
    gagal('Username atau email sudah digunakan akun lain.');
}

// Proses unggah foto profil baru jika dipilih
if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] !== UPLOAD_ERR_NO_FILE) {
    $file = $_FILES['foto_profil'];

    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
        gagal('Ukuran foto profil maksimal 2 MB.');
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        gagal('Gagal mengunggah foto profil. Kode error: ' . $file['error']);
    }
    if ($file['size'] > 2 * 1024 * 1024) {
        gagal('Ukuran foto profil maksimal 2 MB.');
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $ekstensiDiizinkan = array(
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp'
    );

    if (!isset($ekstensiDiizinkan[$mime])) {
        gagal('Format foto profil harus JPG, PNG, atau WEBP.');
    }

    $folderTujuan = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'img';
    $namaFileBaru = 'profil_' . $userId . '_' . time() . '_' . mt_rand(100, 999) . '.' . $ekstensiDiizinkan[$mime];
    $pathTujuan   = $folderTujuan . DIRECTORY_SEPARATOR . $namaFileBaru;

    if (!move_uploaded_file($file['tmp_name'], $pathTujuan)) {
        gagal('Gagal menyimpan file foto profil ke folder img/.', 500);
    }

    // Hapus file foto profil lama jika bukan gambar bawaan
    if ($fotoAktif !== 'bg-singapore.jpg' && strpos($fotoAktif, 'profil_') === 0) {
        $pathLama = $folderTujuan . DIRECTORY_SEPARATOR . $fotoAktif;
        if (is_file($pathLama)) {
            @unlink($pathLama);
        }
    }

    $fotoAktif = $namaFileBaru;
}

$upd = mysqli_prepare($conn, 'UPDATE users SET username = ?, email = ?, nama = ?, asal_kota = ?, foto_profil = ? WHERE id = ?');
mysqli_stmt_bind_param($upd, 'sssssi', $username, $email, $nama, $asalKota, $fotoAktif, $userId);
mysqli_stmt_execute($upd);

$_SESSION['username'] = $username;
berhasil('Profil berhasil diperbarui!', array('foto' => $fotoAktif));