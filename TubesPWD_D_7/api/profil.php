<?php
// profil.php
//   GET  -> ambil data profil + nama file foto_profil
//   POST -> perbarui profil + unggah foto profil (multipart/form-data, Bonus 4)
require 'fungsi.php';
require 'koneksi.php';

$user_id = wajibLogin();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $q = mysqli_prepare($conn, 'SELECT username, email, nama, asal_kota, foto_profil FROM users WHERE id = ?');
    mysqli_stmt_bind_param($q, 'i', $user_id);
    mysqli_stmt_execute($q);
    $data = mysqli_fetch_assoc(mysqli_stmt_get_result($q));
    if (!$data) {
        gagal('Data pengguna tidak ditemukan.', 404);
    }
    if (!empty($data['foto_profil'])) {
        $data['foto_profil'] = basename($data['foto_profil']);
    }
    berhasil('', array('data' => $data));
}

wajibPost();

$username  = ambilPost('username');
$email     = strtolower(ambilPost('email'));
$nama      = ambilPost('nama');
$asal_kota = ambilPost('asal_kota');

$error = validasiUsername($username);
if ($error === '') { $error = validasiEmail($email); }
if ($error === '') { $error = validasiNama($nama); }
if ($error === '') { $error = validasiAsalKota($asal_kota); }
if ($error !== '') {
    gagal($error);
}

if ($nama === '')      { $nama = null; }
if ($asal_kota === '') { $asal_kota = null; }

// Cek keunikan username & email terhadap akun lain
$cekUser = mysqli_prepare($conn, 'SELECT id FROM users WHERE username = ? AND id <> ?');
mysqli_stmt_bind_param($cekUser, 'si', $username, $user_id);
mysqli_stmt_execute($cekUser);
if (mysqli_num_rows(mysqli_stmt_get_result($cekUser)) > 0) {
    gagal('Username sudah dipakai akun lain.');
}

$cekEmail = mysqli_prepare($conn, 'SELECT id FROM users WHERE email = ? AND id <> ?');
mysqli_stmt_bind_param($cekEmail, 'si', $email, $user_id);
mysqli_stmt_execute($cekEmail);
if (mysqli_num_rows(mysqli_stmt_get_result($cekEmail)) > 0) {
    gagal('Email sudah dipakai akun lain.');
}

// Ambil nama foto lama terlebih dahulu
$qLama = mysqli_prepare($conn, 'SELECT foto_profil FROM users WHERE id = ?');
mysqli_stmt_bind_param($qLama, 'i', $user_id);
mysqli_stmt_execute($qLama);
$rowLama = mysqli_fetch_assoc(mysqli_stmt_get_result($qLama));
$foto_profil = ($rowLama && !empty($rowLama['foto_profil'])) ? basename($rowLama['foto_profil']) : null;

// Proses unggah foto baru jika user memilih file di input foto_profil
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

    // Validasi tipe MIME asli file menggunakan finfo (bukan sekadar ekstensi)
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

    $folderTujuan = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'profil';
    if (!is_dir($folderTujuan)) {
        mkdir($folderTujuan, 0755, true);
    }

    $namaFileBaru = 'user_' . $user_id . '_' . time() . '.' . $ekstensiDiizinkan[$mime];
    $pathTujuan   = $folderTujuan . DIRECTORY_SEPARATOR . $namaFileBaru;

    if (!move_uploaded_file($file['tmp_name'], $pathTujuan)) {
        gagal('Gagal menyimpan file foto profil ke direktori server.', 500);
    }

    // Hapus file foto lama jika ada agar penyimpanan tetap bersih
    if ($foto_profil !== null) {
        $pathLama = $folderTujuan . DIRECTORY_SEPARATOR . $foto_profil;
        if (is_file($pathLama)) {
            @unlink($pathLama);
        }
    }

    $foto_profil = $namaFileBaru;
}

try {
    $upd = mysqli_prepare($conn, 'UPDATE users SET username = ?, email = ?, nama = ?, asal_kota = ?, foto_profil = ? WHERE id = ?');
    mysqli_stmt_bind_param($upd, 'sssssi', $username, $email, $nama, $asal_kota, $foto_profil, $user_id);
    mysqli_stmt_execute($upd);
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() === 1062) {
        gagal('Username atau email sudah dipakai akun lain.');
    }
    throw $e;
}

$_SESSION['username'] = $username;
$_SESSION['email']    = $email;
$_SESSION['nama']     = ($nama !== null) ? $nama : $username;

berhasil('Profil berhasil diperbarui!', array(
    'foto_profil' => $foto_profil
));