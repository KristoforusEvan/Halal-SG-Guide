<?php
// logout.php (POST) - hapus session. Pakai POST supaya tidak bisa dipicu lewat link/gambar dari situs lain.
require 'fungsi.php';

wajibPost();

$_SESSION = array();
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();

berhasil('Anda sudah logout.');
