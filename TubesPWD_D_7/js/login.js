// ============================================================
// login.js - halaman login (email + password)
// ============================================================

document.addEventListener('DOMContentLoaded', async function () {
    await siapkanHalaman('tamu');

    document.getElementById('formLogin').addEventListener('submit', async function (e) {
        e.preventDefault();

        var daftarError = fn_ValLogin();
        if (daftarError.length > 0) {
            tampilPesan(daftarError, 'error');
            return;
        }

        var hasil = await panggilApi('api/login.php', {
            email: document.getElementById('email').value.trim(),
            password: document.getElementById('password').value
        });

        if (hasil.sukses) {
            if (hasil.role === 'admin') {
                window.location.href = 'admin.xhtml';
            } else {
                window.location.href = 'index.xhtml';
            }
        } else {
            tampilPesan(hasil.pesan, 'error');
        }
    });
});