// ============================================================
// profil.js - lihat dan update profil pengguna (wajib login)
// ============================================================

// Isi form dengan data profil dari server (kolom opsional bisa null)
function isiForm(data) {
    document.getElementById('nama').value = data.nama || '';
    document.getElementById('email').value = data.email || '';
    document.getElementById('username').value = data.username || '';
    document.getElementById('asal_kota').value = data.asal_kota || '';
}

document.addEventListener('DOMContentLoaded', async function () {
    var sesi = await siapkanHalaman('privat');
    if (!sesi) {
        return;
    }

    // Lihat profil: ambil data dari backend
    var hasil = await panggilApi('api/profil.php');
    if (sesiHabis(hasil)) {
        return;
    }
    if (hasil.sukses) {
        isiForm(hasil.data);
    } else {
        tampilPesan(hasil.pesan, 'error');
    }

    // Update profil
    document.getElementById('formProfil').addEventListener('submit', async function (e) {
        e.preventDefault();

        var daftarError = fn_ValProfil();
        if (daftarError.length > 0) {
            tampilPesan(daftarError, 'error');
            return;
        }

        var balasan = await panggilApi('api/profil.php', {
            nama: document.getElementById('nama').value.trim(),
            email: document.getElementById('email').value.trim(),
            username: document.getElementById('username').value.trim(),
            asal_kota: document.getElementById('asal_kota').value.trim()
        });
        if (sesiHabis(balasan)) {
            return;
        }
        tampilPesan(balasan.pesan, balasan.sukses ? 'sukses' : 'error');
    });
});
