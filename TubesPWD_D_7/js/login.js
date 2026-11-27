// ============================================================
// login.js - halaman login (email + password) + smart redirect ke detail
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
                return;
            }

            // Jika user tadi mengklik kartu detail sebelum login, arahkan kembali ke detail tempat tersebut
            var params = new URLSearchParams(window.location.search);
            var redirect = params.get('redirect');
            if (redirect && /^detail\.xhtml\?id=[0-9]+$/.test(redirect)) {
                window.location.href = redirect;
            } else {
                window.location.href = 'index.xhtml';
            }
        } else {
            tampilPesan(hasil.pesan, 'error');
        }
    });
});