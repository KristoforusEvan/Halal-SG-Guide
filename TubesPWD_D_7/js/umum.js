async function panggilApi(url, data) {
    var opsi = { credentials: 'same-origin' };
    if (data !== undefined) {
        opsi.method = 'POST';
        if (typeof FormData !== 'undefined' && data instanceof FormData) {
            opsi.body = data;
        } else {
            opsi.headers = { 'Content-Type': 'application/x-www-form-urlencoded' };
            opsi.body = new URLSearchParams(data).toString();
        }
    }

    var respons;
    try {
        respons = await fetch(url, opsi);
    } catch (e) {
        return { sukses: false, pesan: 'Tidak bisa menghubungi server. Pastikan Apache XAMPP menyala.', kode: 0 };
    }

    var hasil;
    try {
        hasil = await respons.json();
    } catch (e) {
        hasil = { sukses: false, pesan: 'Balasan server tidak valid (bukan JSON). Cek file PHP di folder api/.' };
    }
    hasil.kode = respons.status;
    if (hasil.detail) {
        hasil.pesan = hasil.pesan + ' (' + hasil.detail + ')';
    }
    return hasil;
}

function sesiHabis(hasil) {
    if (hasil.kode === 401) {
        window.location.replace('login.xhtml');
        return true;
    }
    if (hasil.kode === 403) {
        window.location.replace('index.xhtml');
        return true;
    }
    return false;
}

function kosongkan(elemen) {
    while (elemen.firstChild) {
        elemen.removeChild(elemen.firstChild);
    }
}

function buatElemen(tag, teks, kelas) {
    var el = document.createElement(tag);
    if (teks) {
        el.appendChild(document.createTextNode(teks));
    }
    if (kelas) {
        el.className = kelas;
    }
    return el;
}

function tampilPesan(isi, tipe) {
    var wadah = document.getElementById('pesan');
    if (!wadah) {
        return;
    }
    kosongkan(wadah);
    if (!isi || isi.length === 0) {
        return;
    }
    var daftar = Array.isArray(isi) ? isi : [isi];
    var p = buatElemen('p', '', tipe);
    for (var i = 0; i < daftar.length; i++) {
        if (i > 0) {
            p.appendChild(document.createElement('br'));
        }
        p.appendChild(document.createTextNode(daftar[i]));
    }
    wadah.appendChild(p);
}

async function ambilSesi() {
    var hasil = await panggilApi('api/session.php');
    if (hasil.sukses && hasil.login) {
        return hasil;
    }
    return null;
}

function tambahLink(nav, teks, href) {
    var a = buatElemen('a', teks);
    a.setAttribute('href', href);
    nav.appendChild(a);
    return a;
}

function bangunNavbar(sesi) {
    pasangScrollProgress();
    var nav = document.getElementById('nav');
    if (!nav) {
        return;
    }
    kosongkan(nav);
    tambahLink(nav, 'Homepage', 'index.xhtml');

    if (sesi) {
        if (sesi.role === 'admin') {
            tambahLink(nav, 'Dashboard Admin', 'admin.xhtml');
        } else {
            tambahLink(nav, 'Rencana Kuliner', 'makanan.xhtml');
            tambahLink(nav, 'Jadwal Masjid', 'masjid.xhtml');
        }
        tambahLink(nav, 'Profil Saya', 'profil.xhtml');
        var linkLogout = tambahLink(nav, 'Logout', '#');
        linkLogout.addEventListener('click', async function (e) {
            e.preventDefault();
            await panggilApi('api/logout.php', {});
            window.location.href = 'index.xhtml';
        });
    } else {
        tambahLink(nav, 'Login', 'login.xhtml');
        tambahLink(nav, 'Daftar', 'register.xhtml');
    }

    bangunFooter();
}

async function siapkanHalaman(aturan) {
    var sesi = await ambilSesi();
    if (aturan === 'privat' && !sesi) {
        window.location.replace('login.xhtml');
        return null;
    }
    if (aturan === 'admin') {
        if (!sesi) {
            window.location.replace('login.xhtml');
            return null;
        }
        if (sesi.role !== 'admin') {
            window.location.replace('index.xhtml');
            return null;
        }
    }
    if (aturan === 'tamu' && sesi) {
        window.location.replace(sesi.role === 'admin' ? 'admin.xhtml' : 'index.xhtml');
        return null;
    }
    bangunNavbar(sesi);
    return sesi;
}

function pasangScrollProgress() {
    if (document.getElementById('scrollProgressBar')) {
        return;
    }

    var wadah = buatElemen('div', '', 'scroll-progress-wrap');
    var bar = buatElemen('div', '', 'scroll-progress-bar');
    bar.setAttribute('id', 'scrollProgressBar');
    wadah.appendChild(bar);
    document.body.insertBefore(wadah, document.body.firstChild);

    function perbaruiProgress() {
        var scrollTop = window.scrollY || document.documentElement.scrollTop || 0;
        var tinggiDokumen = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        var persen = tinggiDokumen > 0 ? (scrollTop / tinggiDokumen) * 100 : 0;
        if (persen < 0) {
            persen = 0;
        } else if (persen > 100) {
            persen = 100;
        }
        bar.style.width = persen + '%';
    }

    window.addEventListener('scroll', perbaruiProgress, { passive: true });
    window.addEventListener('resize', perbaruiProgress, { passive: true });
    perbaruiProgress();
}


function bangunFooter() {
    if (document.getElementById('footerPortal')) {
        return;
    }

    var footer = buatElemen('div', '', 'footer-modern');
    footer.setAttribute('id', 'footerPortal');

    var container = buatElemen('div', '', 'footer-container');

    var mainGrid = buatElemen('div', '', 'footer-columns');

    var colBrand = buatElemen('div', '', 'footer-col-brand');
    var brandTitle = buatElemen('div', '', 'footer-brand-row');
    brandTitle.appendChild(buatElemen('span', '', 'footer-brand-dot'));
    brandTitle.appendChild(buatElemen('strong', 'Halal SG Guide'));
    colBrand.appendChild(brandTitle);
    colBrand.appendChild(buatElemen('p', 'Platform panduan wisata ramah Muslim di Singapura. Temukan destinasi ikonik, kuliner bersertifikat halal MUIS, dan masjid terdekat dalam satu tempat.'));
    mainGrid.appendChild(colBrand);

    var colNav = buatElemen('div', '', 'footer-col-links');
    colNav.appendChild(buatElemen('h4', 'Jelajahi'));
    var ulNav = document.createElement('ul');
    var menuNav = [
        { teks: 'Beranda Katalog', href: 'index.xhtml' },
        { teks: 'Rencana Kuliner', href: 'makanan.xhtml' },
        { teks: 'Jadwal Masjid', href: 'masjid.xhtml' },
        { teks: 'Profil Akun', href: 'profil.xhtml' }
    ];
    for (var j = 0; j < menuNav.length; j++) {
        var li = document.createElement('li');
        var a = buatElemen('a', menuNav[j].teks);
        a.setAttribute('href', menuNav[j].href);
        li.appendChild(a);
        ulNav.appendChild(li);
    }
    colNav.appendChild(ulNav);
    mainGrid.appendChild(colNav);

    var colInfo = buatElemen('div', '', 'footer-col-links');
    colInfo.appendChild(buatElemen('h4', 'Informasi & Kontak'));
    var ulInfo = document.createElement('ul');
    var infoItems = [
        'Kampong Glam, Singapore 198833',
        'info@halalsg-guide.com',
        'Instagram: @halalsg_guide',
        'Panduan Sertifikasi Halal MUIS'
    ];
    for (var k = 0; k < infoItems.length; k++) {
        ulInfo.appendChild(buatElemen('li', infoItems[k]));
    }
    colInfo.appendChild(ulInfo);
    mainGrid.appendChild(colInfo);

    container.appendChild(mainGrid);

    var bottomBar = buatElemen('div', '', 'footer-bottom');
    bottomBar.appendChild(buatElemen('span', '© 2026 Halal SG Guide. All rights reserved.'));
    bottomBar.appendChild(buatElemen('span', 'Tugas Besar Pemrograman Web Dasar • Kelompok D-7'));
    container.appendChild(bottomBar);

    footer.appendChild(container);
    document.body.appendChild(footer);
}