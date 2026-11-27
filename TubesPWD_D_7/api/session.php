<?php
// session.php (GET) - cek apakah pengguna sedang login.
// Dipakai JavaScript untuk membuat navbar dan memproteksi halaman.
require 'fungsi.php';

if (isset($_SESSION['user_id'])) {
    berhasil('', array('login' => true, 'nama' => $_SESSION['nama']));
}
berhasil('', array('login' => false));
