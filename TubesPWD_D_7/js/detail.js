// ============================================================
// detail.js - halaman deskripsi detail tempat (wajib login)
// ============================================================

function gambarDetail(t) {
    var wadah = document.getElementById('kontenDetail');
    kosongkan(wadah);

    var kartu = buatElemen('div', '', 'detail-card');

    var banner = buatElemen('div', '', 'detail-banner kat-' + t.kategori);
    if (t.foto) {
        var img = document.createElement('img');
        img.setAttribute('src', 'img/' + t.foto);
        img.setAttribute('alt', t.nama);
        banner.appendChild(img);
    }
    kartu.appendChild(banner);

    var badan = buatElemen('div', '', 'detail-body');
    badan.appendChild(buatElemen('span', t.status, 'badge-halal'));
    badan.appendChild(buatElemen('h2', t.nama, 'detail-judul'));

    var meta = buatElemen('div', '', 'detail-meta');

    var pArea = document.createElement('p');
    pArea.appendChild(buatElemen('strong', 'Area / Kawasan: '));
    pArea.appendChild(document.createTextNode(t.area));
    meta.appendChild(pArea);

    var pHarga = document.createElement('p');
    pHarga.appendChild(buatElemen('strong', 'Estimasi Biaya: '));
    pHarga.appendChild(document.createTextNode(t.harga_estimasi));
    meta.appendChild(pHarga);

    if (t.alamat) {
        var pAlamat = document.createElement('p');
        pAlamat.appendChild(buatElemen('strong', 'Alamat Lengkap: '));
        pAlamat.appendChild(document.createTextNode(t.alamat));
        meta.appendChild(pAlamat);
    }

    if (t.jam_buka) {
        var pJam = document.createElement('p');
        pJam.appendChild(buatElemen('strong', 'Jam Operasional: '));
        pJam.appendChild(document.createTextNode(t.jam_buka));
        meta.appendChild(pJam);
    }

    badan.appendChild(meta);

    badan.appendChild(buatElemen('h3', 'Deskripsi Lengkap'));
    badan.appendChild(buatElemen('p', t.deskripsi || t.keterangan, 'detail-deskripsi'));

    kartu.appendChild(badan);
    wadah.appendChild(kartu);

    var tombolCatat = document.getElementById('tombolCatat');
    if (t.kategori === 'kuliner') {
        tombolCatat.setAttribute('href', 'makanan.xhtml');
        tombolCatat.textContent = '+ Catat ke Rencana Kuliner';
        tombolCatat.classList.remove('sembunyi');
    } else if (t.kategori === 'masjid') {
        tombolCatat.setAttribute('href', 'masjid.xhtml');
        tombolCatat.textContent = '+ Catat ke Jadwal Masjid';
        tombolCatat.classList.remove('sembunyi');
    }
}

document.addEventListener('DOMContentLoaded', async function () {
    var sesi = await siapkanHalaman('privat');
    if (!sesi) {
        return;
    }

    var params = new URLSearchParams(window.location.search);
    var id = params.get('id');
    if (!id) {
        tampilPesan('ID tempat tidak ditemukan di alamat URL.', 'error');
        return;
    }

    var hasil = await panggilApi('api/tempat.php?id=' + encodeURIComponent(id));
    if (sesiHabis(hasil)) {
        return;
    }
    if (!hasil.sukses) {
        tampilPesan(hasil.pesan, 'error');
        return;
    }

    gambarDetail(hasil.data);
});