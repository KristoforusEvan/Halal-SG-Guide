<?php
require 'fungsi.php';
require 'koneksi.php';

$daftarKategori = array('wisata', 'kuliner', 'masjid');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if (isset($_GET['id'])) {
        wajibLogin();

        $id = is_string($_GET['id']) ? trim($_GET['id']) : '';
        if (!ctype_digit($id)) {
            gagal('ID tempat tidak valid.');
        }

        $stmt = mysqli_prepare($conn, 'SELECT id, nama, kategori, area, harga_estimasi, lat, lng, keterangan, deskripsi, alamat, jam_buka, status, foto FROM tempat WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if (!$row) {
            gagal('Tempat tidak ditemukan.', 404);
        }

        $fotoUtama = basename($row['foto']);
        $row['foto'] = $fotoUtama;

        $galeri = array($fotoUtama);
        $qGal = mysqli_prepare($conn, 'SELECT foto FROM tempat_galeri WHERE tempat_id = ? ORDER BY id ASC');
        mysqli_stmt_bind_param($qGal, 'i', $id);
        mysqli_stmt_execute($qGal);
        $resGal = mysqli_stmt_get_result($qGal);

        while ($g = mysqli_fetch_assoc($resGal)) {
            $namaGal = basename($g['foto']);
            if ($namaGal !== '' && !in_array($namaGal, $galeri, true)) {
                $galeri[] = $namaGal;
            }
        }
        $row['galeri'] = $galeri;

        berhasil('', array('data' => $row));
    }

    $keyword  = (isset($_GET['q']) && is_string($_GET['q'])) ? trim($_GET['q']) : '';
    $kategori = (isset($_GET['kategori']) && is_string($_GET['kategori'])) ? trim($_GET['kategori']) : '';
    $harga    = (isset($_GET['harga']) && is_string($_GET['harga'])) ? trim($_GET['harga']) : '';

    $sql    = 'SELECT id, nama, kategori, area, harga_estimasi, lat, lng, keterangan, deskripsi, alamat, jam_buka, status, foto FROM tempat WHERE 1=1';
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

wajibPost();
wajibAdmin();

$aksi = ambilPost('aksi');

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

if ($aksi === 'simpan') {
    $id             = ambilPost('id');
    $nama           = ambilPost('nama');
    $kategori       = ambilPost('kategori');
    $area           = ambilPost('area');
    $harga_estimasi = ambilPost('harga_estimasi');
    $latInput       = ambilPost('lat');
    $lngInput       = ambilPost('lng');
    $keterangan     = ambilPost('keterangan');
    $deskripsi      = ambilPost('deskripsi');
    $alamat         = ambilPost('alamat');
    $jam_buka       = ambilPost('jam_buka');
    $status         = ambilPost('status');
    $fotoLama       = basename(ambilPost('foto_lama'));

    $foto = ($fotoLama !== '') ? $fotoLama : 'bg-singapore.jpg';
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

    $lat = null;
    $lng = null;
    if ($latInput !== '' || $lngInput !== '') {
        if (!is_numeric($latInput) || !is_numeric($lngInput)) {
            gagal('Latitude dan Longitude harus berupa angka desimal (contoh: 1.3023).');
        }
        $latFloat = (float) $latInput;
        $lngFloat = (float) $lngInput;
        if ($latFloat < -90 || $latFloat > 90 || $lngFloat < -180 || $lngFloat > 180) {
            gagal('Nilai Latitude (-90 s.d. 90) atau Longitude (-180 s.d. 180) di luar jangkauan.');
        }
        $lat = (string) $latFloat;
        $lng = (string) $lngFloat;
    }

    $ekstensiDiizinkan = array(
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp'
    );
    $folderTujuan = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'img';

    if (isset($_FILES['foto_file']) && $_FILES['foto_file']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['foto_file'];
        if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > 2 * 1024 * 1024) {
            gagal('Foto utama gagal diunggah atau ukurannya melebihi 2 MB.');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!isset($ekstensiDiizinkan[$mime])) {
            gagal('Format foto utama harus JPG, PNG, atau WEBP.');
        }

        $namaFileBaru = 'tempat_' . time() . '_' . mt_rand(100, 999) . '.' . $ekstensiDiizinkan[$mime];
        if (!move_uploaded_file($file['tmp_name'], $folderTujuan . DIRECTORY_SEPARATOR . $namaFileBaru)) {
            gagal('Gagal menyimpan foto utama ke folder img/.', 500);
        }
        $foto = $namaFileBaru;
    }

    $targetTempatId = 0;

    if ($id !== '') {
        if (!ctype_digit($id)) {
            gagal('ID tempat tidak valid.');
        }
        $upd = mysqli_prepare($conn, 'UPDATE tempat SET nama = ?, kategori = ?, area = ?, harga_estimasi = ?, lat = ?, lng = ?, keterangan = ?, deskripsi = ?, alamat = ?, jam_buka = ?, status = ?, foto = ? WHERE id = ?');
        mysqli_stmt_bind_param($upd, 'ssssssssssssi', $nama, $kategori, $area, $harga_estimasi, $lat, $lng, $keterangan, $deskripsi, $alamat, $jam_buka, $status, $foto, $id);
        mysqli_stmt_execute($upd);
        $targetTempatId = (int) $id;
    } else {
        $ins = mysqli_prepare($conn, 'INSERT INTO tempat (nama, kategori, area, harga_estimasi, lat, lng, keterangan, deskripsi, alamat, jam_buka, status, foto) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        mysqli_stmt_bind_param($ins, 'ssssssssssss', $nama, $kategori, $area, $harga_estimasi, $lat, $lng, $keterangan, $deskripsi, $alamat, $jam_buka, $status, $foto);
        mysqli_stmt_execute($ins);
        $targetTempatId = (int) mysqli_insert_id($conn);
    }

    if ($targetTempatId > 0 && isset($_FILES['foto_galeri']) && is_array($_FILES['foto_galeri']['name'])) {
        $jumlahFile = count($_FILES['foto_galeri']['name']);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        for ($i = 0; $i < $jumlahFile; $i++) {
            $errCode = $_FILES['foto_galeri']['error'][$i];
            if ($errCode === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            if ($errCode !== UPLOAD_ERR_OK || $_FILES['foto_galeri']['size'][$i] > 2 * 1024 * 1024) {
                continue;
            }

            $tmpName = $_FILES['foto_galeri']['tmp_name'][$i];
            $mime    = finfo_file($finfo, $tmpName);
            if (!isset($ekstensiDiizinkan[$mime])) {
                continue;
            }

            $namaGaleri = 'galeri_' . $targetTempatId . '_' . time() . '_' . $i . '_' . mt_rand(10, 99) . '.' . $ekstensiDiizinkan[$mime];
            if (move_uploaded_file($tmpName, $folderTujuan . DIRECTORY_SEPARATOR . $namaGaleri)) {
                $insGal = mysqli_prepare($conn, 'INSERT INTO tempat_galeri (tempat_id, foto) VALUES (?, ?)');
                mysqli_stmt_bind_param($insGal, 'is', $targetTempatId, $namaGaleri);
                mysqli_stmt_execute($insGal);
            }
        }
        finfo_close($finfo);
    }

    berhasil($id !== '' ? 'Konten tempat berhasil diperbarui.' : 'Konten tempat baru berhasil ditambahkan.');
}

gagal('Aksi tidak dikenal.');