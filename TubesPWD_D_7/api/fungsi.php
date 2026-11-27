<?php
// ============================================================
// fungsi.php  -  fungsi bantu untuk SEMUA endpoint di folder api/
// WAJIB di-require paling atas di setiap endpoint.
// Semua endpoint membalas dalam format JSON (bukan HTML),
// lalu JavaScript di halaman .xhtml yang menampilkannya.
// ============================================================

// Saat development: true = pesan error teknis ikut dikirim ke browser (membantu cari bug).
// Ganti ke false kalau sudah final.
define('MODE_DEBUG', true);

// Jangan tampilkan error PHP ke layar, karena output HTML error akan merusak format JSON.
// Error tetap tercatat di log Apache (xampp/apache/logs/error.log).
ini_set('display_errors', '0');

// Cookie session: tidak bisa dibaca JavaScript (httponly) dan
// tidak ikut dikirim dari situs lain (samesite) -> lebih aman dari pencurian session.
session_set_cookie_params(array('httponly' => true, 'samesite' => 'Lax'));
session_start();

// Kalau ada error tak terduga (misal database mati), balas JSON rapi, bukan halaman error PHP.
set_exception_handler(function ($e) {
    error_log('[Halal SG Guide] ' . $e->getMessage());
    $hasil = array('sukses' => false, 'pesan' => 'Terjadi kesalahan pada server.');
    if (MODE_DEBUG) {
        $hasil['detail'] = $e->getMessage();
    }
    kirimJson($hasil, 500);
});

// ---------- Balasan JSON ----------

// Kirim data sebagai JSON lalu hentikan script
function kirimJson($data, $kode = 200)
{
    http_response_code($kode);
    header('Content-Type: application/json; charset=UTF-8');
    header('Cache-Control: no-store');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// Balasan gagal: {"sukses": false, "pesan": "..."}
function gagal($pesan, $kode = 400)
{
    kirimJson(array('sukses' => false, 'pesan' => $pesan), $kode);
}

// Balasan berhasil: {"sukses": true, "pesan": "...", ...data tambahan}
function berhasil($pesan, $tambahan = array())
{
    kirimJson(array_merge(array('sukses' => true, 'pesan' => $pesan), $tambahan));
}

// ---------- Pengecekan permintaan ----------

// Endpoint yang mengubah data hanya boleh lewat POST
function wajibPost()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        gagal('Permintaan harus menggunakan metode POST.', 405);
    }
}

// Endpoint khusus pengguna yang sudah login. Mengembalikan id pengguna.
function wajibLogin()
{
    if (!isset($_SESSION['user_id'])) {
        gagal('Silakan login terlebih dahulu.', 401);
    }
    return (int) $_SESSION['user_id'];
}

// Endpoint khusus Administrator. Memastikan user sudah login DAN ber-role 'admin'.
function wajibAdmin()
{
    $user_id = wajibLogin();
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        gagal('Akses ditolak. Halaman ini khusus Administrator.', 403);
    }
    return $user_id;
}

// Ambil nilai teks dari $_POST (dibuang spasi di pinggir). Kalau bukan teks, dianggap kosong.
function ambilPost($kunci)
{
    if (isset($_POST[$kunci]) && is_string($_POST[$kunci])) {
        return trim($_POST[$kunci]);
    }
    return '';
}

// Password tidak boleh di-trim (spasi bisa jadi bagian dari password)
function ambilPassword()
{
    if (isset($_POST['password']) && is_string($_POST['password'])) {
        return $_POST['password'];
    }
    return '';
}

// ---------- Aturan validasi (disamakan dengan js/validasi.js) ----------
// Tiap fungsi mengembalikan teks error, atau '' kalau valid.

// Nama lengkap opsional: boleh kosong, tapi kalau diisi hanya huruf dan spasi
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
    // bcrypt hanya membaca 72 byte pertama
    if (strlen($password) > 72) {
        return 'Password maksimal 72 karakter.';
    }
    return '';
}

// Username WAJIB: 3-30 karakter, hanya huruf/angka/underscore
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

// Asal kota opsional
function validasiAsalKota($kota)
{
    if (strlen($kota) > 100) {
        return 'Asal kota maksimal 100 karakter.';
    }
    return '';
}