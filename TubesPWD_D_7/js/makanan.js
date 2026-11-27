// ============================================================
// makanan.js - CRUD 1: rencana kuliner halal (wajib login)
// Semua data diproses backend lewat api/makanan.php
// ============================================================

var daftarData = []; // salinan data terakhir dari server (dipakai saat tombol Edit)

// ---------- READ: ambil daftar lalu gambar tabel ----------
async function muatDaftar() {
    var hasil = await panggilApi('api/makanan.php');
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
    tdNama.appendChild(buatElemen('strong', row.nama_tempat));
    tr.appendChild(tdNama);

    tr.appendChild(buatElemen('td', row.lokasi_mrt));

    var tdStatus = document.createElement('td');
    tdStatus.appendChild(buatElemen('span', row.status_halal, 'badge-halal'));
    tr.appendChild(tdStatus);

    tr.appendChild(buatElemen('td', row.catatan));

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
        var td = buatElemen('td', 'Belum ada rencana kuliner. Tambahkan lewat form di atas.');
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
    document.getElementById('nama_tempat').value = row.nama_tempat;
    document.getElementById('lokasi_mrt').value = row.lokasi_mrt;
    document.getElementById('status_halal').value = row.status_halal; // dropdown otomatis terpilih
    document.getElementById('catatan').value = row.catatan;

    document.getElementById('judulForm').textContent = 'Edit Rencana Kuliner Halal';
    document.getElementById('tombolSimpan').value = 'Perbarui Rencana Kuliner';
    document.getElementById('tombolBatal').classList.remove('sembunyi');
    tampilPesan('');
    window.scrollTo(0, 0);
}

// Kembalikan form ke mode "tambah"
function selesaiEdit() {
    document.getElementById('formMakanan').reset();
    document.getElementById('id_edit').value = '';
    document.getElementById('judulForm').textContent = 'Tambah Rencana Kuliner Halal';
    document.getElementById('tombolSimpan').value = 'Simpan Rencana Kuliner';
    document.getElementById('tombolBatal').classList.add('sembunyi');
}

// ---------- DELETE: lewat POST + konfirmasi ----------
async function hapusData(row) {
    if (!window.confirm('Hapus rencana "' + row.nama_tempat + '"?')) {
        return;
    }
    var hasil = await panggilApi('api/makanan.php', { aksi: 'hapus', id: row.id });
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
    document.getElementById('formMakanan').addEventListener('submit', async function (e) {
        e.preventDefault();

        var daftarError = fn_ValMakanan();
        if (daftarError.length > 0) {
            tampilPesan(daftarError, 'error');
            return;
        }

        var hasil = await panggilApi('api/makanan.php', {
            aksi: 'simpan',
            id: document.getElementById('id_edit').value,
            nama_tempat: document.getElementById('nama_tempat').value.trim(),
            lokasi_mrt: document.getElementById('lokasi_mrt').value.trim(),
            status_halal: document.getElementById('status_halal').value,
            catatan: document.getElementById('catatan').value.trim()
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
