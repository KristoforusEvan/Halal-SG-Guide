// ============================================================
// detail.js - halaman detail privat + Carousel Gambar Auto-Slide
// ============================================================

var timerSlide = null;

function buatCarouselGambar(wadah, daftarFoto, namaTempat) {
    kosongkan(wadah);
    if (timerSlide) {
        clearInterval(timerSlide);
        timerSlide = null;
    }

    var daftar = (Array.isArray(daftarFoto) && daftarFoto.length > 0) ? daftarFoto : ['bg-singapore.jpg'];
    var indeksAktif = 0;

    var wrap = buatElemen('div', '', 'carousel-wrap');

    var img = document.createElement('img');
    img.setAttribute('src', 'img/' + daftar[0]);
    img.setAttribute('alt', namaTempat);
    img.className = 'carousel-img';
    wrap.appendChild(img);

    // Jika gambar hanya 1, tampilkan tanpa panah dan tanpa auto-slide
    if (daftar.length <= 1) {
        wadah.appendChild(wrap);
        return;
    }

    // Penghitung slide di pojok kanan atas (contoh: 1 / 3)
    var counter = buatElemen('span', '1 / ' + daftar.length, 'carousel-counter');
    wrap.appendChild(counter);

    // Tombol panah kiri (<) dan kanan (>)
    var btnPrev = buatElemen('button', '\u2039', 'carousel-btn carousel-prev');
    btnPrev.setAttribute('type', 'button');
    btnPrev.setAttribute('title', 'Gambar sebelumnya');

    var btnNext = buatElemen('button', '\u203A', 'carousel-btn carousel-next');
    btnNext.setAttribute('type', 'button');
    btnNext.setAttribute('title', 'Gambar berikutnya');

    wrap.appendChild(btnPrev);
    wrap.appendChild(btnNext);

    // Indikator titik (dots) di bagian bawah gambar
    var dotsWrap = buatElemen('div', '', 'carousel-dots');
    var daftarDot = [];

    for (var i = 0; i < daftar.length; i++) {
        (function (idx) {
            var dot = buatElemen('button', '', idx === 0 ? 'carousel-dot aktif' : 'carousel-dot');
            dot.setAttribute('type', 'button');
            dot.setAttribute('title', 'Gambar ' + (idx + 1));
            dot.addEventListener('click', function () {
                pindahSlide(idx);
                mulaiAutoSlide();
            });
            dotsWrap.appendChild(dot);
            daftarDot.push(dot);
        })(i);
    }
    wrap.appendChild(dotsWrap);

    function pindahSlide(tujuan) {
        indeksAktif = (tujuan + daftar.length) % daftar.length;
        img.classList.add('fading');
        setTimeout(function () {
            img.setAttribute('src', 'img/' + daftar[indeksAktif]);
            img.setAttribute('alt', namaTempat + ' (' + (indeksAktif + 1) + ')');
            img.classList.remove('fading');
        }, 140);

        counter.textContent = (indeksAktif + 1) + ' / ' + daftar.length;
        for (var j = 0; j < daftarDot.length; j++) {
            if (j === indeksAktif) {
                daftarDot[j].classList.add('aktif');
            } else {
                daftarDot[j].classList.remove('aktif');
            }
        }
    }

    function mulaiAutoSlide() {
        if (timerSlide) {
            clearInterval(timerSlide);
        }
        // Geser otomatis ke gambar berikutnya setiap 3.5 detik jika tidak diinteraksi
        timerSlide = setInterval(function () {
            pindahSlide(indeksAktif + 1);
        }, 3500);
    }

    function hentikanAutoSlide() {
        if (timerSlide) {
            clearInterval(timerSlide);
            timerSlide = null;
        }
    }

    btnPrev.addEventListener('click', function () {
        pindahSlide(indeksAktif - 1);
        mulaiAutoSlide();
    });

    btnNext.addEventListener('click', function () {
        pindahSlide(indeksAktif + 1);
        mulaiAutoSlide();
    });

    // Jeda auto-slide saat kursor diarahkan ke gambar agar user nyaman melihat
    wrap.addEventListener('mouseenter', hentikanAutoSlide);
    wrap.addEventListener('mouseleave', mulaiAutoSlide);

    wadah.appendChild(wrap);
    mulaiAutoSlide();
}

document.addEventListener('DOMContentLoaded', async function () {
    var sesi = await siapkanHalaman('privat');
    if (!sesi) {
        return;
    }

    var params = new URLSearchParams(window.location.search);
    var id = params.get('id');
    if (!id) {
        tampilPesan('ID tempat tidak ditemukan di URL.', 'error');
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

    var t = hasil.data;
    document.title = t.nama + ' - Halal SG Guide';

    buatCarouselGambar(document.getElementById('detailBanner'), t.galeri, t.nama);

    document.getElementById('detailStatus').textContent = t.status;
    document.getElementById('detailNama').textContent = t.nama;
    document.getElementById('detailArea').textContent = t.area;
    document.getElementById('detailHarga').textContent = t.harga_estimasi;
    document.getElementById('detailAlamat').textContent = t.alamat;
    document.getElementById('detailJam').textContent = t.jam_buka;
    document.getElementById('detailDeskripsi').textContent = t.deskripsi;

    var tombolCatat = document.getElementById('tombolCatat');
    if (t.kategori === 'kuliner') {
        tombolCatat.setAttribute('href', 'makanan.xhtml');
        tombolCatat.textContent = '+ Catat ke Rencana Kuliner';
    } else if (t.kategori === 'masjid') {
        tombolCatat.setAttribute('href', 'masjid.xhtml');
        tombolCatat.textContent = '+ Jadwalkan ke Kunjungan Masjid';
    } else {
        tombolCatat.classList.add('sembunyi');
    }

    document.getElementById('kartuDetail').classList.remove('sembunyi');
});