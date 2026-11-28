<?php
define('MODE_DEBUG', true);

ini_set('display_errors', '0');

session_set_cookie_params(array('httponly' => true, 'samesite' => 'Lax'));
session_start();

set_exception_handler(function ($e) {
    error_log('[Halal SG Guide] ' . $e->getMessage());
    $hasil = array('sukses' => false, 'pesan' => 'Terjadi kesalahan pada server.');
    if (MODE_DEBUG) {
        $hasil['detail'] = $e->getMessage();
    }
    kirimJson($hasil, 500);
});

function kirimJson($data, $kode = 200)
{
    http_response_code($kode);
    header('Content-Type: application/json; charset=UTF-8');
    header('Cache-Control: no-store');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function gagal($pesan, $kode = 400)
{
    kirimJson(array('sukses' => false, 'pesan' => $pesan), $kode);
}

function berhasil($pesan, $tambahan = array())
{
    kirimJson(array_merge(array('sukses' => true, 'pesan' => $pesan), $tambahan));
}

function wajibPost()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        gagal('Permintaan harus menggunakan metode POST.', 405);
    }
}

function wajibLogin()
{
    if (!isset($_SESSION['user_id'])) {
        gagal('Silakan login terlebih dahulu.', 401);
    }
    return (int) $_SESSION['user_id'];
}

function wajibAdmin()
{
    $user_id = wajibLogin();
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        gagal('Akses ditolak. Halaman ini khusus Administrator.', 403);
    }
    return $user_id;
}

function ambilPost($kunci)
{
    if (isset($_POST[$kunci]) && is_string($_POST[$kunci])) {
        return trim($_POST[$kunci]);
    }
    return '';
}

function ambilPassword()
{
    if (isset($_POST['password']) && is_string($_POST['password'])) {
        return $_POST['password'];
    }
    return '';
}

function validasiNama($nama)
{
    if ($nama === '') {
        return '';
    }
    if (!preg_match('/^[a-zA-Z ]+$/', $nama)) {
        return 'Nama lengkap hanya boleh berisi huruf dan spasi.';
    }
    if (strlen($nama) > 100) {
        return 'Nama lengkap maksimal 100 karakter.';
    }
    return '';
}

function validasiEmail($email)
{
    if ($email === '') {
        return 'Email wajib diisi.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Format email tidak valid (contoh: nama@gmail.com).';
    }
    if (strlen($email) > 100) {
        return 'Email maksimal 100 karakter.';
    }
    return '';
}

function validasiPassword($password)
{
    if ($password === '') {
        return 'Password wajib diisi.';
    }
    if (strlen($password) < 8) {
        return 'Password minimal 8 karakter.';
    }
    if (strlen($password) > 72) {
        return 'Password maksimal 72 karakter.';
    }
    return '';
}

function validasiUsername($username)
{
    if ($username === '') {
        return 'Username wajib diisi.';
    }
    if (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
        return 'Username 3-30 karakter, hanya huruf, angka, dan underscore (_).';
    }
    return '';
}

function validasiAsalKota($kota)
{
    if (strlen($kota) > 100) {
        return 'Asal kota maksimal 100 karakter.';
    }
    return '';
}