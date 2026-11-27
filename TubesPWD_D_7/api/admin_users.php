<?php
// admin_users.php (Khusus Admin)
//   GET              -> Daftar seluruh akun pengguna terdaftar
//   POST aksi=hapus  -> Hapus akun pengguna berdasarkan ID (kecuali akun admin yang sedang login)
require 'fungsi.php';
require 'koneksi.php';

$admin_id = wajibAdmin();

// ---------- READ: DAFTAR AKUN ----------
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $res = mysqli_query($conn, 'SELECT id, username, email, nama, asal_kota, role FROM users ORDER BY role ASC, id DESC');
    $data = array();
    while ($row = mysqli_fetch_assoc($res)) {
        $data[] = $row;
    }
    berhasil('', array('data' => $data));
}

// ---------- DELETE: HAPUS AKUN PENGGUNA ----------
wajibPost();
$aksi = ambilPost('aksi');

if ($aksi === 'hapus') {
    $id = ambilPost('id');
    if (!ctype_digit($id)) {
        gagal('ID akun tidak valid.');
    }

    $target_id = (int) $id;
    if ($target_id === $admin_id) {
        gagal('Anda tidak dapat menghapus akun administrator yang sedang aktif digunakan.');
    }

    $stmt = mysqli_prepare($conn, 'DELETE FROM users WHERE id = ? AND role = ?');
    $roleUser = 'user';
    mysqli_stmt_bind_param($stmt, 'is', $target_id, $roleUser);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) !== 1) {
        gagal('Akun pengguna tidak ditemukan atau berstatus administrator.', 404);
    }
    berhasil('Akun pengguna berhasil dihapus.');
}

gagal('Aksi tidak dikenal.');