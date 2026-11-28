<?php
require 'fungsi.php';

if (isset($_SESSION['user_id'])) {
    berhasil('', array(
        'login' => true,
        'nama'  => $_SESSION['nama'],
        'role'  => isset($_SESSION['role']) ? $_SESSION['role'] : 'user'
    ));
}
berhasil('', array('login' => false));