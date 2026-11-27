// ============================================================
// login.js - halaman login (email + password)
// ============================================================

document.addEventListener('DOMContentLoaded', async function () {
    // Kalau sudah login, tidak perlu lihat halaman login lagi
    await siapkanHalaman('tamu');

    document.getElementById('formLogin').addEventListener('submit', async function (e) {
        e.preventDefault();

        // 1) Validasi di browser dulu
        var daftarError = fn_ValLogin();
        if (daftarError.length > 0) {
            tampilPesan(daftarError, 'error');
            return;
        }

        // 2) Kirim ke backend PHP
        var hasil = await panggilApi('api/login.php', {
            email: document.getElementById('email').value.trim(),
            password: document.getElementById('password').value
        });

        // 3) Tampilkan hasil
        if (hasil.sukses) {
            window.location.href = 'index.xhtml';
        } else {
            tampilPesan(hasil.pesan, 'error');
        }
    });
});
