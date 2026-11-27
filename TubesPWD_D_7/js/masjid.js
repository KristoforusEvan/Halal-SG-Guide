// ============================================================
// masjid.js - CRUD 2: jadwal kunjungan masjid (wajib login)
// Semua data diproses backend lewat api/masjid.php
// ============================================================

var daftarData = []; // salinan data terakhir dari server (dipakai saat tombol Edit)

// ---------- READ: ambil daftar lalu gambar tabel ----------
async function muatDaftar() {
    var hasil = await panggilApi('api/masjid.php');
    if (sesiHabis(hasil)) {
        return;
    }
    if (!hasil.sukses) {
        tampilPesan(hasil.pesan, 'error');
        return;
    }
    daftarData = hasil.data;
    gambarTabel();
}

// Buat satu tombol bergaya teks (Edit / Hapus)
function buatTombol(teks, kelas, saatKlik) {
    var tombol = buatElemen('button', teks, 'tombol-teks ' + kelas);
    tombol.setAttribute('type', 'button');
    tombol.addEventListener('click', saatKlik);
    return tombol;
}

// Buat satu baris tabel untuk satu data
function buatBaris(row, nomor) {
    var tr = document.createElement('tr');
    tr.appendChild(buatElemen('td', String(nomor)));

    var tdNama = document.createElement('td');
    tdNama.appendChild(buatElemen('strong', row.nama_masjid));
    tr.appendChild(tdNama);

    tr.appendChild(buatElemen('td', row.area_singapura));

    var tdWaktu = document.createElement('td');
    tdWaktu.appendChild(buatElemen('span', row.waktu_sholat, 'badge-halal'));
    tr.appendChild(tdWaktu);

    tr.appendChild(buatElemen('td', row.keterangan));

    var tdAksi = document.createElement('td');
    tdAksi.appendChild(buatTombol('Edit', 'aksi-link', function () { mulaiEdit(row); }));
    tdAksi.appendChild(document.createTextNode(' | '));
    tdAksi.appendChild(buatTombol('Hapus', 'aksi-hapus', function () { hapusData(row); }));
    tr.appendChild(tdAksi);

    return tr;
}

function gambarTabel() {
    var isi = document.getElementById('isiTabel');
    kosongkan(isi);

    if (daftarData.length === 0) {
        var trKosong = document.createElement('tr');
        var td = buatElemen('td', 'Belum ada jadwal masjid. Tambahkan lewat form di atas.');
        td.setAttribute('colspan', '6');
        trKosong.appendChild(td);
        isi.appendChild(trKosong);
    }
    for (var i = 0; i < daftarData.length; i++) {
        isi.appendChild(buatBaris(daftarData[i], i + 1));
    }
    document.getElementById('jumlah').textContent = daftarData.length;
}

// ---------- Mode edit: isi form dengan data yang dipilih ----------
function mulaiEdit(row) {
    document.getElementById('id_edit').value = row.id;
    document.getElementById('nama_masjid').value = row.nama_masjid;
    document.getElementById('area_singapura').value = row.area_singapura;
    document.getElementById('waktu_sholat').value = row.waktu_sholat; // dropdown otomatis terpilih
    document.getElementById('keterangan').value = row.keterangan;

    document.getElementById('judulForm').textContent = 'Edit Jadwal Kunjungan Masjid';
    document.getElementById('tombolSimpan').value = 'Perbarui Jadwal Ibadah';
    document.getElementById('tombolBatal').classList.remove('sembunyi');
    tampilPesan('');
    window.scrollTo(0, 0);
}

// Kembalikan form ke mode "tambah"
function selesaiEdit() {
    document.getElementById('formMasjid').reset();
    document.getElementById('id_edit').value = '';
    document.getElementById('judulForm').textContent = 'Tambah Jadwal Kunjungan Masjid';
    document.getElementById('tombolSimpan').value = 'Simpan Jadwal Ibadah';
    document.getElementById('tombolBatal').classList.add('sembunyi');
}

// ---------- DELETE: lewat POST + konfirmasi ----------
async function hapusData(row) {
    if (!window.confirm('Hapus jadwal "' + row.nama_masjid + '"?')) {
        return;
    }
    var hasil = await panggilApi('api/masjid.php', { aksi: 'hapus', id: row.id });
    if (sesiHabis(hasil)) {
        return;
    }
    tampilPesan(hasil.pesan, hasil.sukses ? 'sukses' : 'error');
    if (hasil.sukses) {
        // Kalau data yang sedang diedit ikut terhapus, tutup mode edit
        if (document.getElementById('id_edit').value === String(row.id)) {
            selesaiEdit();
        }
        await muatDaftar();
    }
}

document.addEventListener('DOMContentLoaded', async function () {
    var sesi = await siapkanHalaman('privat');
    if (!sesi) {
        return;
    }

    // ---------- CREATE / UPDATE: simpan form ----------
    document.getElementById('formMasjid').addEventListener('submit', async function (e) {
        e.preventDefault();

        var daftarError = fn_ValMasjid();
        if (daftarError.length > 0) {
            tampilPesan(daftarError, 'error');
            return;
        }

        var hasil = await panggilApi('api/masjid.php', {
            aksi: 'simpan',
            id: document.getElementById('id_edit').value,
            nama_masjid: document.getElementById('nama_masjid').value.trim(),
            area_singapura: document.getElementById('area_singapura').value.trim(),
            waktu_sholat: document.getElementById('waktu_sholat').value,
            keterangan: document.getElementById('keterangan').value.trim()
        });
        if (sesiHabis(hasil)) {
            return;
        }
        tampilPesan(hasil.pesan, hasil.sukses ? 'sukses' : 'error');
        if (hasil.sukses) {
            selesaiEdit();
            await muatDaftar();
        }
    });

    document.getElementById('tombolBatal').addEventListener('click', function () {
        selesaiEdit();
        tampilPesan('');
    });

    muatDaftar();
});
