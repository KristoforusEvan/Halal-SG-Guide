<?php
// masjid.php  -  CRUD 2: jadwal kunjungan masjid (milik tiap user)
//   GET                      -> daftar (Read)
//   POST aksi=simpan         -> tambah (Create) kalau id kosong, ubah (Update) kalau id ada
//   POST aksi=hapus          -> hapus (Delete)
// Semua query memakai prepared statement + cek user_id.
require 'fungsi.php';
require 'koneksi.php';

$user_id = wajibLogin();

// Pilihan yang valid untuk dropdown waktu sholat (dicek juga di server)
$daftarWaktu = array('Dzuhur & Ashar (Jamak)', 'Maghrib & Isya (Jamak)', 'Sholat Jumat', 'Subuh');

// ---------- READ ----------
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $q = mysqli_prepare($conn, 'SELECT id, nama_masjid, area_singapura, waktu_sholat, keterangan FROM jadwal_masjid WHERE user_id = ? ORDER BY id DESC');
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
    $stmt = mysqli_prepare($conn, 'DELETE FROM jadwal_masjid WHERE id = ? AND user_id = ?');
    mysqli_stmt_bind_param($stmt, 'ii', $id, $user_id);
    mysqli_stmt_execute($stmt);
    if (mysqli_stmt_affected_rows($stmt) !== 1) {
        gagal('Data tidak ditemukan.', 404);
    }
    berhasil('Jadwal kunjungan masjid berhasil dihapus.');
}

// ---------- CREATE / UPDATE ----------
if ($aksi === 'simpan') {
    $id             = ambilPost('id');
    $nama_masjid    = ambilPost('nama_masjid');
    $area_singapura = ambilPost('area_singapura');
    $waktu_sholat   = ambilPost('waktu_sholat');
    $keterangan     = ambilPost('keterangan');

    if ($nama_masjid === '' || $area_singapura === '' || $waktu_sholat === '' || $keterangan === '') {
        gagal('Harap lengkapi semua data jadwal masjid.');
    }
    if (strlen($nama_masjid) > 100 || strlen($area_singapura) > 100) {
        gagal('Nama masjid dan area maksimal 100 karakter.');
    }
    if (strlen($keterangan) > 255) {
        gagal('Keterangan maksimal 255 karakter.');
    }
    if (!in_array($waktu_sholat, $daftarWaktu, true)) {
        gagal('Waktu sholat tidak valid.');
    }

    if ($id !== '') {
        // UPDATE: pastikan data memang milik user ini
        if (!ctype_digit($id)) {
            gagal('ID tidak valid.');
        }
        $cek = mysqli_prepare($conn, 'SELECT id FROM jadwal_masjid WHERE id = ? AND user_id = ?');
        mysqli_stmt_bind_param($cek, 'ii', $id, $user_id);
        mysqli_stmt_execute($cek);
        if (mysqli_num_rows(mysqli_stmt_get_result($cek)) !== 1) {
            gagal('Data tidak ditemukan.', 404);
        }
        $upd = mysqli_prepare($conn, 'UPDATE jadwal_masjid SET nama_masjid = ?, area_singapura = ?, waktu_sholat = ?, keterangan = ? WHERE id = ? AND user_id = ?');
        mysqli_stmt_bind_param($upd, 'ssssii', $nama_masjid, $area_singapura, $waktu_sholat, $keterangan, $id, $user_id);
        mysqli_stmt_execute($upd);
        berhasil('Jadwal kunjungan masjid berhasil diperbarui.');
    }

    // CREATE
    $ins = mysqli_prepare($conn, 'INSERT INTO jadwal_masjid (user_id, nama_masjid, area_singapura, waktu_sholat, keterangan) VALUES (?, ?, ?, ?, ?)');
    mysqli_stmt_bind_param($ins, 'issss', $user_id, $nama_masjid, $area_singapura, $waktu_sholat, $keterangan);
    mysqli_stmt_execute($ins);
    berhasil('Jadwal kunjungan masjid berhasil ditambahkan.');
}

gagal('Aksi tidak dikenal.');
