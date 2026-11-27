<?php
// makanan.php  -  CRUD 1: rencana kuliner halal (milik tiap user)
//   GET                      -> daftar (Read)
//   POST aksi=simpan         -> tambah (Create) kalau id kosong, ubah (Update) kalau id ada
//   POST aksi=hapus          -> hapus (Delete)
// Semua query memakai prepared statement + cek user_id supaya user tidak bisa
// mengubah/menghapus data milik orang lain.
require 'fungsi.php';
require 'koneksi.php';

$user_id = wajibLogin();

// Pilihan yang valid untuk dropdown (dicek juga di server)
$daftarStatus = array('Sertifikat Halal MUIS', 'Muslim-Owned (Pemilik Muslim)', 'No Pork No Lard');

// ---------- READ ----------
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $q = mysqli_prepare($conn, 'SELECT id, nama_tempat, lokasi_mrt, status_halal, catatan FROM rencana_makanan WHERE user_id = ? ORDER BY id DESC');
    mysqli_stmt_bind_param($q, 'i', $user_id);
    mysqli_stmt_execute($q);
    $res = mysqli_stmt_get_result($q);
    $data = array();
    while ($row = mysqli_fetch_assoc($res)) {
        $data[] = $row;
    }
    berhasil('', array('data' => $data));
}

wajibPost();
$aksi = ambilPost('aksi');

// ---------- DELETE ----------
if ($aksi === 'hapus') {
    $id = ambilPost('id');
    if (!ctype_digit($id)) {
        gagal('ID tidak valid.');
    }
    $stmt = mysqli_prepare($conn, 'DELETE FROM rencana_makanan WHERE id = ? AND user_id = ?');
    mysqli_stmt_bind_param($stmt, 'ii', $id, $user_id);
    mysqli_stmt_execute($stmt);
    if (mysqli_stmt_affected_rows($stmt) !== 1) {
        gagal('Data tidak ditemukan.', 404);
    }
    berhasil('Data rencana kuliner berhasil dihapus.');
}

// ---------- CREATE / UPDATE ----------
if ($aksi === 'simpan') {
    $id           = ambilPost('id');
    $nama_tempat  = ambilPost('nama_tempat');
    $lokasi_mrt   = ambilPost('lokasi_mrt');
    $status_halal = ambilPost('status_halal');
    $catatan      = ambilPost('catatan');

    if ($nama_tempat === '' || $lokasi_mrt === '' || $status_halal === '' || $catatan === '') {
        gagal('Harap lengkapi semua data kuliner halal.');
    }
    if (strlen($nama_tempat) > 100 || strlen($lokasi_mrt) > 100) {
        gagal('Nama tempat dan lokasi maksimal 100 karakter.');
    }
    if (strlen($catatan) > 255) {
        gagal('Catatan maksimal 255 karakter.');
    }
    if (!in_array($status_halal, $daftarStatus, true)) {
        gagal('Kategori verifikasi halal tidak valid.');
    }

    if ($id !== '') {
        // UPDATE: pastikan data memang milik user ini
        if (!ctype_digit($id)) {
            gagal('ID tidak valid.');
        }
        $cek = mysqli_prepare($conn, 'SELECT id FROM rencana_makanan WHERE id = ? AND user_id = ?');
        mysqli_stmt_bind_param($cek, 'ii', $id, $user_id);
        mysqli_stmt_execute($cek);
        if (mysqli_num_rows(mysqli_stmt_get_result($cek)) !== 1) {
            gagal('Data tidak ditemukan.', 404);
        }
        $upd = mysqli_prepare($conn, 'UPDATE rencana_makanan SET nama_tempat = ?, lokasi_mrt = ?, status_halal = ?, catatan = ? WHERE id = ? AND user_id = ?');
        mysqli_stmt_bind_param($upd, 'ssssii', $nama_tempat, $lokasi_mrt, $status_halal, $catatan, $id, $user_id);
        mysqli_stmt_execute($upd);
        berhasil('Rencana kuliner berhasil diperbarui.');
    }

    // CREATE
    $ins = mysqli_prepare($conn, 'INSERT INTO rencana_makanan (user_id, nama_tempat, lokasi_mrt, status_halal, catatan) VALUES (?, ?, ?, ?, ?)');
    mysqli_stmt_bind_param($ins, 'issss', $user_id, $nama_tempat, $lokasi_mrt, $status_halal, $catatan);
    mysqli_stmt_execute($ins);
    berhasil('Rencana kuliner halal berhasil ditambahkan.');
}

gagal('Aksi tidak dikenal.');
