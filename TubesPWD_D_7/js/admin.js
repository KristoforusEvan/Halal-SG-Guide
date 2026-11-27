// ============================================================
// admin.js - Dashboard Administrator (CRUD Konten + Upload Foto & Hapus Akun)
// ============================================================

var daftarKonten = [];
var daftarUser = [];

function buatTombolAksi(teks, kelas, saatKlik) {
    var tombol = buatElemen('button', teks, 'tombol-teks ' + kelas);
    tombol.setAttribute('type', 'button');
    tombol.addEventListener('click', saatKlik);
    return tombol;
}

// ---------- BAGIAN 1: CRUD KONTEN TEMPAT ----------
async function muatKonten() {
    var hasil = await panggilApi('api/tempat.php');
    if (sesiHabis(hasil)) {
        return;
    }
    if (!hasil.sukses) {
        tampilPesan(hasil.pesan, 'error');
        return;
    }
    daftarKonten = hasil.data;
    gambarTabelKonten();
}

function gambarTabelKonten() {
    var tbody = document.getElementById('tabelKonten');
    kosongkan(tbody);

    if (daftarKonten.length === 0) {
        var trKosong = document.createElement('tr');
        var td = buatElemen('td', 'Belum ada konten tempat.');
        td.setAttribute('colspan', '6');
        trKosong.appendChild(td);
        tbody.appendChild(trKosong);
    }

    for (var i = 0; i < daftarKonten.length; i++) {
        (function (row, nomor) {
            var tr = document.createElement('tr');
            tr.appendChild(buatElemen('td', String(nomor)));

            var tdNama = document.createElement('td');
            tdNama.appendChild(buatElemen('strong', row.nama));
            tr.appendChild(tdNama);

            tr.appendChild(buatElemen('td', row.kategori.toUpperCase()));
            tr.appendChild(buatElemen('td', row.area));
            tr.appendChild(buatElemen('td', row.harga_estimasi));

            var tdAksi = document.createElement('td');
            tdAksi.appendChild(buatTombolAksi('Edit', 'aksi-link', function () { mulaiEditKonten(row); }));
            tdAksi.appendChild(document.createTextNode(' | '));
            tdAksi.appendChild(buatTombolAksi('Hapus', 'aksi-hapus', function () { hapusKonten(row); }));
            tr.appendChild(tdAksi);

            tbody.appendChild(tr);
        })(daftarKonten[i], i + 1);
    }
    document.getElementById('jumlahKonten').textContent = daftarKonten.length;
}

function mulaiEditKonten(row) {
    document.getElementById('id_konten').value = row.id;
    document.getElementById('nama').value = row.nama;
    document.getElementById('kategori').value = row.kategori;
    document.getElementById('area').value = row.area;
    document.getElementById('harga_estimasi').value = row.harga_estimasi;
    document.getElementById('lat').value = row.lat || '';
    document.getElementById('lng').value = row.lng || '';
    document.getElementById('status').value = row.status;
    document.getElementById('jam_buka').value = row.jam_buka;
    document.getElementById('alamat').value = row.alamat;
    document.getElementById('foto_lama').value = row.foto || '';
    document.getElementById('foto_file').value = '';
    document.getElementById('infoFotoSaatIni').textContent = 'Foto aktif saat ini: ' + (row.foto || 'bg-singapore.jpg') + ' (biarkan kosong jika tidak ingin mengganti).';
    document.getElementById('keterangan').value = row.keterangan;
    document.getElementById('deskripsi').value = row.deskripsi;

    document.getElementById('judulFormKonten').textContent = 'Edit Konten Tempat';
    document.getElementById('tombolSimpanKonten').value = 'Perbarui Konten';
    document.getElementById('tombolBatalKonten').classList.remove('sembunyi');
    tampilPesan('');
    window.scrollTo(0, 0);
}

function selesaiEditKonten() {
    document.getElementById('formKonten').reset();
    document.getElementById('id_konten').value = '';
    document.getElementById('foto_lama').value = '';
    document.getElementById('infoFotoSaatIni').textContent = 'Opsional: jika dikosongkan saat tambah baru, otomatis memakai bg-singapore.jpg.';
    document.getElementById('judulFormKonten').textContent = 'Tambah Konten Tempat (Homepage)';
    document.getElementById('tombolSimpanKonten').value = 'Simpan Konten';
    document.getElementById('tombolBatalKonten').classList.add('sembunyi');
}

async function hapusKonten(row) {
    if (!window.confirm('Yakin ingin menghapus konten "' + row.nama + '"?')) {
        return;
    }
    var hasil = await panggilApi('api/tempat.php', { aksi: 'hapus', id: row.id });
    if (sesiHabis(hasil)) {
        return;
    }
    tampilPesan(hasil.pesan, hasil.sukses ? 'sukses' : 'error');
    if (hasil.sukses) {
        if (document.getElementById('id_konten').value === String(row.id)) {
            selesaiEditKonten();
        }
        await muatKonten();
    }
}

// ---------- BAGIAN 2: MANAJEMEN AKUN USER ----------
async function muatUser() {
    var hasil = await panggilApi('api/admin_users.php');
    if (sesiHabis(hasil)) {
        return;
    }
    if (!hasil.sukses) {
        tampilPesan(hasil.pesan, 'error');
        return;
    }
    daftarUser = hasil.data;
    gambarTabelUser();
}

function gambarTabelUser() {
    var tbody = document.getElementById('tabelUser');
    kosongkan(tbody);

    for (var i = 0; i < daftarUser.length; i++) {
        (function (u, nomor) {
            var tr = document.createElement('tr');
            tr.appendChild(buatElemen('td', String(nomor)));
            tr.appendChild(buatElemen('td', u.username));
            tr.appendChild(buatElemen('td', u.email));
            tr.appendChild(buatElemen('td', u.nama || '-'));

            var tdRole = document.createElement('td');
            tdRole.appendChild(buatElemen('span', u.role.toUpperCase(), 'badge-halal'));
            tr.appendChild(tdRole);

            var tdAksi = document.createElement('td');
            if (u.role === 'user') {
                tdAksi.appendChild(buatTombolAksi('Hapus Akun', 'aksi-hapus', function () { hapusAkun(u); }));
            } else {
                tdAksi.appendChild(document.createTextNode('Akun Admin'));
            }
            tr.appendChild(tdAksi);

            tbody.appendChild(tr);
        })(daftarUser[i], i + 1);
    }
    document.getElementById('jumlahUser').textContent = daftarUser.length;
}

async function hapusAkun(u) {
    if (!window.confirm('Yakin ingin menghapus akun pengguna "' + u.username + '" (' + u.email + ')?')) {
        return;
    }
    var hasil = await panggilApi('api/admin_users.php', { aksi: 'hapus', id: u.id });
    if (sesiHabis(hasil)) {
        return;
    }
    tampilPesan(hasil.pesan, hasil.sukses ? 'sukses' : 'error');
    if (hasil.sukses) {
        await muatUser();
    }
}

document.addEventListener('DOMContentLoaded', async function () {
    var sesi = await siapkanHalaman('admin');
    if (!sesi) {
        return;
    }

    document.getElementById('formKonten').addEventListener('submit', async function (e) {
        e.preventDefault();

        var daftarError = fn_ValKonten();
        if (daftarError.length > 0) {
            tampilPesan(daftarError, 'error');
            return;
        }

        // Gunakan FormData agar data teks + file foto terkirim lewat multipart/form-data
        var formData = new FormData();
        formData.append('aksi', 'simpan');
        formData.append('id', document.getElementById('id_konten').value);
        formData.append('nama', document.getElementById('nama').value.trim());
        formData.append('kategori', document.getElementById('kategori').value);
        formData.append('area', document.getElementById('area').value.trim());
        formData.append('harga_estimasi', document.getElementById('harga_estimasi').value.trim());
        formData.append('lat', document.getElementById('lat').value.trim());
        formData.append('lng', document.getElementById('lng').value.trim());
        formData.append('status', document.getElementById('status').value.trim());
        formData.append('jam_buka', document.getElementById('jam_buka').value.trim());
        formData.append('alamat', document.getElementById('alamat').value.trim());
        formData.append('foto_lama', document.getElementById('foto_lama').value);
        formData.append('keterangan', document.getElementById('keterangan').value.trim());
        formData.append('deskripsi', document.getElementById('deskripsi').value.trim());

        var inputFoto = document.getElementById('foto_file');
        if (inputFoto.files && inputFoto.files[0]) {
            formData.append('foto_file', inputFoto.files[0]);
        }
        var inputGaleri = document.getElementById('foto_galeri');
        if (inputGaleri && inputGaleri.files) {
            for (var g = 0; g < inputGaleri.files.length; g++) {
                formData.append('foto_galeri[]', inputGaleri.files[g]);
            }
        }

        var hasil = await panggilApi('api/tempat.php', formData);

        if (sesiHabis(hasil)) {
            return;
        }
        tampilPesan(hasil.pesan, hasil.sukses ? 'sukses' : 'error');
        if (hasil.sukses) {
            selesaiEditKonten();
            await muatKonten();
        }
    });

    document.getElementById('tombolBatalKonten').addEventListener('click', function () {
        selesaiEditKonten();
        tampilPesan('');
    });

    muatKonten();
    muatUser();
});