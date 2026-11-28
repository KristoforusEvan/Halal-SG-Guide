var usernameTersedia = true;
var emailTersedia = true;

function tampilStatusInput(idElemen, teks, tersedia) {
    var el = document.getElementById(idElemen);
    if (!el) {
        return;
    }
    el.textContent = teks;
    el.className = 'form-hint ' + (teks === '' ? '' : (tersedia ? 'status-tersedia' : 'status-terpakai'));
}

async function cekKetersediaanUsername() {
    var username = document.getElementById('username').value.trim();
    if (username === '') {
        usernameTersedia = true;
        tampilStatusInput('statusUsername', '', true);
        return;
    }
    var hasil = await panggilApi('api/cek_akun.php?username=' + encodeURIComponent(username));
    if (hasil.sukses) {
        usernameTersedia = hasil.tersedia;
        tampilStatusInput('statusUsername', hasil.pesan, hasil.tersedia);
    }
}

async function cekKetersediaanEmail() {
    var email = document.getElementById('email').value.trim().toLowerCase();
    if (email === '') {
        emailTersedia = true;
        tampilStatusInput('statusEmail', '', true);
        return;
    }
    var hasil = await panggilApi('api/cek_akun.php?email=' + encodeURIComponent(email));
    if (hasil.sukses) {
        emailTersedia = hasil.tersedia;
        tampilStatusInput('statusEmail', hasil.pesan, hasil.tersedia);
    }
}

document.addEventListener('DOMContentLoaded', async function () {
    await siapkanHalaman('tamu');

    var inputUsername = document.getElementById('username');
    var inputEmail = document.getElementById('email');

    inputUsername.addEventListener('blur', cekKetersediaanUsername);
    inputEmail.addEventListener('blur', cekKetersediaanEmail);

    inputUsername.addEventListener('input', function () {
        tampilStatusInput('statusUsername', '', true);
    });
    inputEmail.addEventListener('input', function () {
        tampilStatusInput('statusEmail', '', true);
    });

    document.getElementById('formRegister').addEventListener('submit', async function (e) {
        e.preventDefault();

        var daftarError = fn_ValRegister();
        if (daftarError.length > 0) {
            tampilPesan(daftarError, 'error');
            return;
        }

        await cekKetersediaanUsername();
        await cekKetersediaanEmail();

        if (!usernameTersedia || !emailTersedia) {
            tampilPesan('Silakan gunakan username dan email yang masih tersedia.', 'error');
            return;
        }

        var hasil = await panggilApi('api/register.php', {
            username: inputUsername.value.trim(),
            email: inputEmail.value.trim(),
            password: document.getElementById('password').value
        });

        if (hasil.sukses) {
            tampilPesan(hasil.pesan, 'sukses');
            document.getElementById('formRegister').reset();
            tampilStatusInput('statusUsername', '', true);
            tampilStatusInput('statusEmail', '', true);
            setTimeout(function () {
                window.location.href = 'login.xhtml';
            }, 1500);
        } else {
            tampilPesan(hasil.pesan, 'error');
        }
    });
});