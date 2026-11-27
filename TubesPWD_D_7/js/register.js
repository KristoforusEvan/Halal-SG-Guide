// ============================================================
// register.js - halaman registrasi (username, email, password)
// ============================================================

document.addEventListener('DOMContentLoaded', async function () {
    await siapkanHalaman('tamu');

    document.getElementById('formRegister').addEventListener('submit', async function (e) {
        e.preventDefault();

        // 1) Validasi di browser dulu
        var daftarError = fn_ValRegister();
        if (daftarError.length > 0) {
            tampilPesan(daftarError, 'error');
            return;
        }

        // 2) Kirim ke backend PHP (password dienkripsi di sana dengan password_hash)
        var hasil = await panggilApi('api/register.php', {
            username: document.getElementById('username').value.trim(),
            email: document.getElementById('email').value.trim(),
            password: document.getElementById('password').value
        });

        // 3) Tampilkan hasil; kalau berhasil, arahkan ke halaman login
        if (hasil.sukses) {
            tampilPesan(hasil.pesan, 'sukses');
            document.getElementById('formRegister').reset();
            setTimeout(function () {
                window.location.href = 'login.xhtml';
            }, 1500);
        } else {
            tampilPesan(hasil.pesan, 'error');
        }
    });
});
