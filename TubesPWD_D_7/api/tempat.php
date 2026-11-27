<?php
// tempat.php
//   GET tanpa ?id             -> Daftar & filter tempat untuk Homepage (PUBLIK, tanpa login)
//   GET dengan ?id=...        -> Detail 1 tempat (PRIVAT, wajib login)
//   POST aksi=simpan          -> Tambah / Edit konten tempat (Khusus Admin)
//   POST aksi=hapus           -> Hapus konten tempat (Khusus Admin)
require 'fungsi.php';
require 'koneksi.php';

$daftarKategori = array('wisata', 'kuliner', 'masjid');

// ---------- GET: READ DETAIL ATAU DAFTAR TEMPAT ----------
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    // Mode Detail 1 Tempat (WAJIB LOGIN)
    if (isset($_GET['id'])) {
        wajibLogin();

        $id = is_string($_GET['id']) ? trim($_GET['id']) : '';
        if (!ctype_digit($id)) {
            gagal('ID tempat tidak valid.');
        }

        $stmt = mysqli_prepare($conn, 'SELECT id, nama, kategori, area, harga_estimasi, keterangan, deskripsi, alamat, jam_buka, status, foto FROM tempat WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if (!$row) {
            gagal('Tempat tidak ditemukan.', 404);
        }
        $row['foto'] = basename($row['foto']);
        berhasil('', array('data' => $row));
    }

    // Mode Daftar & Filter Homepage (PUBLIK)
    $keyword  = (isset($_GET['q']) && is_string($_GET['q'])) ? trim($_GET['q']) : '';
    $kategori = (isset($_GET['kategori']) && is_string($_GET['kategori'])) ? trim($_GET['kategori']) : '';
    $harga    = (isset($_GET['harga']) && is_string($_GET['harga'])) ? trim($_GET['harga']) : '';

    $sql    = 'SELECT id, nama, kategori, area, harga_estimasi, keterangan, deskripsi, alamat, jam_buka, status, foto FROM tempat WHERE 1=1';
    $types  = '';
    $params = array();

    if ($kategori !== '' && in_array($kategori, $daftarKategori, true)) {
        $sql .= ' AND kategori = ?';
        $types .= 's';
        $params[] = $kategori;
    }

    if ($keyword !== '') {
        $sql .= ' AND (nama LIKE ? OR area LIKE ? OR alamat LIKE ?)';
        $like = '%' . $keyword . '%';
        $types .= 'sss';
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
    }

    if ($harga === 'gratis') {
        $sql .= ' AND harga_estimasi = ?';
        $types .= 's';
        $params[] = 'Gratis';
    } elseif ($harga === 'berbayar') {
        $sql .= ' AND harga_estimasi <> ?';
        $types .= 's';
        $params[] = 'Gratis';
    }

    $sql .= ' ORDER BY id DESC';

    $stmt = mysqli_prepare($conn, $sql);
    if ($types !== '') {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    $hasil = array();
    while ($row = mysqli_fetch_assoc($res)) {
        $row['foto'] = basename($row['foto']);
        $hasil[] = $row;
    }

    berhasil('', array('jumlah' => count($hasil), 'data' => $hasil));
}

// ---------- POST: KHUSUS ADMIN (CREATE / UPDATE / DELETE KONTEN) ----------
wajibPost();
wajibAdmin();

$aksi = ambilPost('aksi');

// DELETE KONTEN
if ($aksi === 'hapus') {
    $id = ambilPost('id');
    if (!ctype_digit($id)) {
        gagal('ID tempat tidak valid.');
    }

    $stmt = mysqli_prepare($conn, 'DELETE FROM tempat WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) !== 1) {
        gagal('Konten tempat tidak ditemukan.', 404);
    }
    berhasil('Konten tempat berhasil dihapus.');
}

// CREATE / UPDATE KONTEN
if ($aksi === 'simpan') {
    $id             = ambilPost('id');
    $nama           = ambilPost('nama');
    $kategori       = ambilPost('kategori');
    $area           = ambilPost('area');
    $harga_estimasi = ambilPost('harga_estimasi');
    $keterangan     = ambilPost('keterangan');
    $deskripsi      = ambilPost('deskripsi');
    $alamat         = ambilPost('alamat');
    $jam_buka       = ambilPost('jam_buka');
    $status         = ambilPost('status');
    $foto           = basename(ambilPost('foto'));

    if ($foto === '') {
        $foto = 'bg-singapore.jpg';
    }
    if ($harga_estimasi === '') {
        $harga_estimasi = 'Gratis';
    }

    if ($nama === '' || $kategori === '' || $area === '' || $keterangan === '' || $deskripsi === '' || $alamat === '' || $jam_buka === '' || $status === '') {
        gagal('Harap lengkapi semua kolom konten tempat.');
    }
    if (!in_array($kategori, $daftarKategori, true)) {
        gagal('Kategori tempat tidak valid.');
    }
    if (strlen($nama) > 100 || strlen($area) > 100 || strlen($jam_buka) > 100) {
        gagal('Nama, area, dan jam buka maksimal 100 karakter.');
    }
    if (strlen($keterangan) > 150) {
        gagal('Ringkasan keterangan maksimal 150 karakter.');
    }
    if (strlen($alamat) > 200) {
        gagal('Alamat lengkap maksimal 200 karakter.');
    }

    if ($id !== '') {
        if (!ctype_digit($id)) {
            gagal('ID tempat tidak valid.');
        }
        $upd = mysqli_prepare($conn, 'UPDATE tempat SET nama = ?, kategori = ?, area = ?, harga_estimasi = ?, keterangan = ?, deskripsi = ?, alamat = ?, jam_buka = ?, status = ?, foto = ? WHERE id = ?');
        mysqli_stmt_bind_param($upd, 'ssssssssssi', $nama, $kategori, $area, $harga_estimasi, $keterangan, $deskripsi, $alamat, $jam_buka, $status, $foto, $id);
        mysqli_stmt_execute($upd);
        berhasil('Konten tempat berhasil diperbarui.');
    }

    $ins = mysqli_prepare($conn, 'INSERT INTO tempat (nama, kategori, area, harga_estimasi, keterangan, deskripsi, alamat, jam_buka, status, foto) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    mysqli_stmt_bind_param($ins, 'ssssssssss', $nama, $kategori, $area, $harga_estimasi, $keterangan, $deskripsi, $alamat, $jam_buka, $status, $foto);
    mysqli_stmt_execute($ins);
    berhasil('Konten tempat baru berhasil ditambahkan.');
}

gagal('Aksi tidak dikenal.');