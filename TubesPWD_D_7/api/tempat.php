<?php
// tempat.php (GET) - data tempat untuk homepage (PUBLIK, tidak perlu login).
// Data diambil dari array di data_tempat.php (tanpa database), lalu disaring
// berdasarkan ?q=kata kunci dan ?kategori=kuliner|masjid|wisata.
require 'fungsi.php';
require 'data_tempat.php'; // berisi array $daftarTempat

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
    // Lewati kalau kategori dipilih tapi tidak cocok
    if ($kategori !== '' && $t['kategori'] !== $kategori) {
        continue;
    }
    // Lewati kalau keyword diisi tapi tidak ada di nama maupun area MRT (huruf besar/kecil diabaikan)
    if ($keyword !== '' && mb_stripos($t['nama'], $keyword) === false && mb_stripos($t['area_mrt'], $keyword) === false) {
        continue;
    }
    // basename() = hanya nama file, supaya tidak bisa menyisipkan path folder lain
    $t['foto'] = isset($t['foto']) ? basename($t['foto']) : '';
    $hasil[] = $t;
}

berhasil('', array('jumlah' => count($hasil), 'data' => $hasil));
