/* JIBAS Anjungan — menu atas + pemuat seksi tanpa reload, tanpa /anjungan di address bar.
   Pane memakai id warisan (content-pane-a..k) sehingga script tiap seksi anjungan tetap jalan:
   potongan HTML dimuat, <script>-nya dieksekusi berurutan, baru fungsi init-nya dipanggil. */
(function () {
    'use strict';

    var root = document.documentElement;
    var CSS_TEMA = ['/style/jibas-theme.css', '/style/jibas-anjungan.css'];

    function kartu(key) {
        return document.querySelector('.anj-card[data-seksi="' + key + '"]');
    }

    function judul(key) {
        var el = kartu(key);
        var h2 = el ? el.querySelector('.anj-card-title') : null;
        return h2 ? h2.textContent.trim() : '';
    }

    /* ---------- pemuatan seksi ---------- */

    var sudahDimuat = {};

    function muatLuarscript(urlAbs) {
        if (sudahDimuat[urlAbs]) return Promise.resolve();
        sudahDimuat[urlAbs] = true;
        return new Promise(function (resolve) {
            var node = document.createElement('script');
            node.src = urlAbs;
            node.onload = node.onerror = function () { resolve(); };
            document.body.appendChild(node);
        });
    }

    function muatScript(el) {
        return new Promise(function (resolve) {
            if (el.src) {
                muatLuarscript(el.src).then(resolve);
                return;
            }
            try {
                window.eval(el.textContent);
            } catch (e) { /* script warisan yang bergantung pada DOM dialog — abaikan */ }
            resolve();
        });
    }

    function temaIframe(frame) {
        if (frame.dataset.anjTema === '1') return;
        frame.addEventListener('load', function () {
            var doc;
            try { doc = frame.contentDocument; } catch (e) { return; }
            if (!doc || !doc.documentElement) return;
            frame.dataset.anjTema = '1';
            doc.documentElement.setAttribute('data-jibas-phase', root.getAttribute('data-jibas-phase') || 'terang');
            doc.documentElement.setAttribute('data-bs-theme', root.getAttribute('data-bs-theme') || 'light');
            if (doc.body) doc.body.classList.add('anj-iframe');
            if (!doc.querySelector('link[data-anj-tema]')) {
                CSS_TEMA.forEach(function (href) {
                    var link = doc.createElement('link');
                    link.rel = 'stylesheet';
                    link.setAttribute('data-anj-tema', '1');
                    link.href = href;
                    (doc.head || doc.documentElement).appendChild(link);
                });
            }
        });
    }

    function pasangIsi(kartuEl, html) {
        var doc = new DOMParser().parseFromString(html, 'text/html');
        var paneEl = doc.body;
        var scripts = [].slice.call(paneEl.querySelectorAll('script'));
        scripts.forEach(function (s) { s.parentNode.removeChild(s); });

        var target = kartuEl.querySelector('.anj-pane');
        target.textContent = '';

        /* Sebagian script warisan Informasi Siswa menulis ulang ke '#is_main', jadi
           isinya dibungkus elemen dengan id itu bila kartunya meminta (data-wadah). */
        var tujuan = target;
        var wadahId = kartuEl.getAttribute('data-wadah');
        if (wadahId) {
            tujuan = document.createElement('div');
            tujuan.id = wadahId;
            target.appendChild(tujuan);
        }
        while (paneEl.firstChild) tujuan.appendChild(paneEl.firstChild);
        target.querySelectorAll('iframe').forEach(temaIframe);

        var rantai = Promise.resolve();
        scripts.forEach(function (s) {
            rantai = rantai.then(function () {
                var tiruan = document.createElement('script');
                if (s.src) tiruan.src = new URL(s.getAttribute('src'), target.baseURI).href;
                if (s.type) tiruan.type = s.type;
                if (!s.src) tiruan.textContent = s.textContent;
                return muatScript(tiruan);
            });
        });
        return rantai;
    }

    function muatSeksi(key) {
        var el = kartu(key);
        if (!el || el.dataset.anjTermuat === '1') return Promise.resolve();
        el.dataset.anjTermuat = '1';
        var target = el.querySelector('.anj-pane');
        target.innerHTML = '<div class="anj-loading"><span class="anj-spinner" aria-hidden="true"></span>Memuat ' + judul(key) + '…</div>';

        var pustaka = [];
        try { pustaka = JSON.parse(el.dataset.lib || '[]') || []; } catch (e) { /* tanpa pustaka */ }
        var rantai = Promise.resolve();
        pustaka.forEach(function (rel) {
            rantai = rantai.then(function () { return muatLuarscript(new URL(rel, document.baseURI).href); });
        });

        return rantai
            .then(function () { return fetch(el.dataset.src, { credentials: 'same-origin' }); })
            .then(function (res) {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.text();
            })
            .then(function (html) {
                target.textContent = '';
                return pasangIsi(el, html);
            })
            .then(function () {
                var nama = el.dataset.init;
                if (nama && typeof window[nama] === 'function') window[nama]();
            })
            .catch(function (err) {
                el.dataset.anjTermuat = '';
                target.innerHTML = '<div class="anj-error"><i class="bi bi-exclamation-triangle me-2"></i>'
                    + judul(key) + ' tidak dapat dimuat' + (err && err.message ? ' (' + err.message + ')' : '') + '.</div>';
            });
    }

    /* ---------- navigasi menu atas ---------- */

    function aktifkan(key, push) {
        var el = kartu(key);
        if (!el) return;
        [].slice.call(document.querySelectorAll('.anj-card')).forEach(function (c) {
            c.classList.toggle('d-none', c !== el);
        });
        [].slice.call(document.querySelectorAll('.anj-nav-link')).forEach(function (a) {
            a.classList.toggle('active', a.getAttribute('data-seksi') === key);
        });
        var h1 = document.getElementById('anjHeroTitle');
        if (h1) h1.textContent = judul(key);
        if (push) history.pushState({ seksi: key }, '', '/' + (key === 'beranda' ? '' : key + '/'));
        muatSeksi(key);
    }

    document.addEventListener('click', function (ev) {
        var link = ev.target.closest ? ev.target.closest('.anj-nav-link') : null;
        if (link) {
            ev.preventDefault();
            aktifkan(link.getAttribute('data-seksi'), true);
            return;
        }
        var tool = ev.target.closest ? ev.target.closest('.anj-tool') : null;
        if (tool) {
            var grup = tool.parentNode.children;
            for (var i = 0; i < grup.length; i++) grup[i].classList.toggle('active', grup[i] === tool);
        }
    });

    window.addEventListener('popstate', function (ev) {
        var key = ev.state && ev.state.seksi ? ev.state.seksi : jbSeksiDariPath(location.pathname);
        aktifkan(key, false);
    });

    function jbSeksiDariPath(path) {
        var m = /^\/(infosekolah|mading|infosiswa|jadkal|pustaka|pegawai|psb)\/?$/.exec(path || '/');
        return m ? m[1] : 'beranda';
    }

    /* ---------- latar hero ---------- */

    function latar() {
        var daftar = window.b_bgList;
        var layer = document.getElementById('anjBg');
        if (!layer || !Array.isArray(daftar) || daftar.length === 0) return;
        var url = daftar.map(function (img) { return new URL(img, document.baseURI).href; });
        var i = 0;
        layer.style.backgroundImage = 'url("' + url[0] + '")';
        setInterval(function () {
            i = (i + 1) % url.length;
            layer.style.opacity = '0';
            setTimeout(function () {
                layer.style.backgroundImage = 'url("' + url[i] + '")';
                layer.style.opacity = '';
            }, 600);
        }, Math.max(8000, (window.b_bgDelay || 15000)));
    }

    /* ---------- modal Masuk (form login Informasi Siswa) ---------- */

    function tampilkanGalat(pesan) {
        var box = document.getElementById('anjMasukError');
        if (!box) return;
        box.textContent = pesan;
        box.hidden = false;
    }

    function sembunyikanGalat() {
        var box = document.getElementById('anjMasukError');
        if (box) { box.textContent = ''; box.hidden = true; }
    }

    function tutupModal() {
        var el = document.getElementById('anjMasukModal');
        if (el && window.bootstrap) window.bootstrap.Modal.getOrCreateInstance(el).hide();
    }

    /* Isi halaman Informasi Siswa diambil dari infosiswa.content.php, bukan infosiswa.php:
       file itu hanya merender form login (sesi siswa tidak dibuka di sana) dan include
       'infosiswa.main.php' milik warisan tidak ada di disk. */
    function bukaIsiSiswa(nis) {
        var el = kartu('infosiswa');
        if (!el) return;
        el.dataset.src = 'infosiswa/infosiswa.content.php?nis=' + encodeURIComponent(nis) + '&reporttype=PROFIL';
        el.dataset.anjTermuat = '';
        aktifkan('infosiswa', true);
    }

    function kirimLogin(nis, pin) {
        var tombol = document.getElementById('anjMasukKirim');
        if (tombol) tombol.disabled = true;
        fetch('infosiswa/infosiswa.dologin.php?nis=' + encodeURIComponent(nis) + '&pin=' + encodeURIComponent(pin),
              { credentials: 'same-origin' })
            .then(function (res) {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.text();
            })
            .then(function (html) {
                if (html.indexOf('infosiswa.content.php') === -1) throw new Error('NIS atau PIN salah!');
                tutupModal();
                var form = document.getElementById('anjMasukForm');
                if (form) form.reset();
                sembunyikanGalat();
                bukaIsiSiswa(nis);
            })
            .catch(function (err) {
                tampilkanGalat(err && err.message ? err.message : 'Login gagal, silakan coba lagi.');
            })
            .then(function () {
                if (tombol) tombol.disabled = false;
            });
    }

    function initMasuk() {
        var form = document.getElementById('anjMasukForm');
        if (!form) return;
        form.addEventListener('submit', function (ev) {
            ev.preventDefault();
            sembunyikanGalat();
            var nis = document.getElementById('anjIsNis').value.trim();
            var pin = document.getElementById('anjIsPin').value.trim();
            if (nis === '' || pin === '') { tampilkanGalat('NIS dan PIN harus diisi.'); return; }
            kirimLogin(nis, pin);
        });
        document.querySelectorAll('[data-bs-target="#anjMasukModal"]').forEach(function (pemicu) {
            pemicu.addEventListener('click', function () {
                setTimeout(function () {
                    var nis = document.getElementById('anjIsNis');
                    if (nis && !nis.value) nis.focus();
                }, 120);
            });
        });
    }


    /* ---------- iframe bertema & pembaruan service worker ---------- */

    function sediaTemaIframeUlang() {
        var fase = root.getAttribute('data-jibas-phase');
        document.querySelectorAll('.anj-pane iframe').forEach(function (f) {
            try {
                if (f.contentDocument && f.contentDocument.documentElement) {
                    f.contentDocument.documentElement.setAttribute('data-jibas-phase', fase);
                    f.contentDocument.documentElement.setAttribute('data-bs-theme', root.getAttribute('data-bs-theme'));
                }
            } catch (e) { /* dokumen lintas sumber */ }
        });
    }

    /* iframe warisan (mading / info sekolah) dokumen terpisah: ikutkan fase tema ke dalamnya */
    new MutationObserver(sediaTemaIframeUlang)
        .observe(root, { attributes: true, attributeFilter: ['data-jibas-phase', 'data-bs-theme'] });

    var tombolMuat = document.querySelector('[data-jb-pwa-reload]');
    if (tombolMuat) {
        tombolMuat.addEventListener('click', function () {
            if (navigator.serviceWorker && navigator.serviceWorker.controller) {
                navigator.serviceWorker.controller.postMessage('SKIP_TUNGGU');
            }
            location.reload();
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () { initMasuk(); latar(); aktifkan(document.body.dataset.anjActive || 'beranda', false); });
    } else {
        initMasuk(); latar(); aktifkan(document.body.dataset.anjActive || 'beranda', false);
    }
})();
