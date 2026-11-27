// ============================================================
// profil.js - Halaman Lihat & Update Profil + Upload Foto
// ============================================================

async function muatProfil() {
    var hasil = await panggilApi('api/profil.php');
    if (sesiHabis(hasil)) {
        return;
    }
    if (!hasil.sukses) {
        tampilPesan(hasil.pesan, 'error');
        return;
    }

    var u = hasil.data;
    document.getElementById('username').value = u.username || '';
    document.getElementById('email').value = u.email || '';
    document.getElementById('nama').value = u.nama || '';
    document.getElementById('asal_kota').value = u.asal_kota || '';

    var fotoAktif = u.foto ? u.foto : 'bg-singapore.jpg';
    document.getElementById('previewFotoProfil').setAttribute('src', 'img/' + fotoAktif + '?t=' + Date.now());
}

document.addEventListener('DOMContentLoaded', async function () {
    var sesi = await siapkanHalaman('privat');
    if (!sesi) {
        return;
    }

    await muatProfil();

    // Pratinjau langsung saat user memilih file gambar dari perangkat
    var inputFoto = document.getElementById('foto_profil');
    inputFoto.addEventListener('change', function () {
        if (inputFoto.files && inputFoto.files[0]) {
            var urlSementara = URL.createObjectURL(inputFoto.files[0]);
            document.getElementById('previewFotoProfil').setAttribute('src', urlSementara);
        }
    });

    document.getElementById('formProfil').addEventListener('submit', async function (e) {
        e.preventDefault();

        if (typeof fn_ValProfil === 'function') {
            var daftarError = fn_ValProfil();
            if (daftarError.length > 0) {
                tampilPesan(daftarError, 'error');
                return;
            }
        }

        var formData = new FormData();
        formData.append('username', document.getElementById('username').value.trim());
        formData.append('email', document.getElementById('email').value.trim());
        formData.append('nama', document.getElementById('nama').value.trim());
        formData.append('asal_kota', document.getElementById('asal_kota').value.trim());

        if (inputFoto.files && inputFoto.files[0]) {
            formData.append('foto_profil', inputFoto.files[0]);
        }

        var hasil = await panggilApi('api/profil.php', formData);
        if (sesiHabis(hasil)) {
            return;
        }

        tampilPesan(hasil.pesan, hasil.sukses ? 'sukses' : 'error');
        if (hasil.sukses) {
            inputFoto.value = '';
            if (hasil.foto) {
                document.getElementById('previewFotoProfil').setAttribute('src', 'img/' + hasil.foto + '?t=' + Date.now());
            }
        }
    });
});