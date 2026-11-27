<?php
// tempat.php (GET)
//  - Tanpa ?id : daftar tempat untuk homepage (PUBLIK, tidak perlu login).
//  - Dengan ?id : detail satu tempat (PRIVAT, wajib login).
require 'fungsi.php';
require 'data_tempat.php'; // berisi array $daftarTempat

// ---------- MODE DETAIL (WAJIB LOGIN) ----------
if (isset($_GET['id'])) {
    wajibLogin(); // balas 401 jika belum login

    $id = is_string($_GET['id']) ? trim($_GET['id']) : '';
    if (!ctype_digit($id)) {
        gagal('ID tempat tidak valid.');
    }

    $targetId = (int) $id;
    foreach ($daftarTempat as $t) {
        if (isset($t['id']) && (int) $t['id'] === $targetId) {
            $t['foto'] = isset($t['foto']) ? basename($t['foto']) : '';
            berhasil('', array('data' => $t));
        }
    }
    gagal('Tempat tidak ditemukan.', 404);
}

// ---------- MODE PENCARIAN HOMEPAGE (PUBLIK) ----------
$keyword  = '';
$kategori = '';
if (isset($_GET['q']) && is_string($_GET['q'])) {
    $keyword = trim($_GET['q']);
}
if (isset($_GET['kategori']) && is_string($_GET['kategori'])) {
    $kategori = $_GET['kategori'];
}

$hasil = array();
foreach ($daftarTempat as $t) {
    if ($kategori !== '' && $t['kategori'] !== $kategori) {
        continue;
    }
    if ($keyword !== '' && mb_stripos($t['nama'], $keyword) === false && mb_stripos($t['area_mrt'], $keyword) === false) {
        continue;
    }
    $t['foto'] = isset($t['foto']) ? basename($t['foto']) : '';
    $hasil[] = $t;
}

berhasil('', array('jumlah' => count($hasil), 'data' => $hasil));